<?php 
require_once 'models/Contrarecibo.php';
require_once 'models/Factura.php';
require_once 'models/Proveedores.php';

class ContrarecibosController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            header("Location: " . BASE_URL . "index.php?c=Admin&a=login");
            exit();
        }
    }

    /* =======================
       MÉTODO BITÁCORA
    ======================== */
    private function registrarBitacora($accion){
        $sql = "INSERT INTO bitacora
                (usuario_id, usuario_nombre, accion, modulo, fecha, hora)
                VALUES (:usuario_id, :usuario_nombre, :accion, :modulo, CURDATE(), CURTIME())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':usuario_id'     => $_SESSION['usuario']['id'],
            ':usuario_nombre' => $_SESSION['usuario']['usuario'],
            ':accion'         => $accion,
            ':modulo'         => 'Contrarecibos'
        ]);
    }

    /* =======================
       LISTADO
    ======================== */
    public function index() {
        $stmt = $this->pdo->prepare("
            SELECT 
                c.id, 
                c.fecha, 
                p.nombre AS proveedor
            FROM contrarecibos c
            LEFT JOIN proveedores p ON c.proveedor = p.id
            ORDER BY c.id DESC
        ");
        $stmt->execute();
        $contrarecibos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $facturaModel = new Factura($this->pdo);
        foreach ($contrarecibos as &$c) {
            $c['facturas'] = $facturaModel->getByContrareciboId($c['id']);
        }

        require __DIR__ . '/../views/contrarecibos/index.php';
    }

    /* =======================
       PDF
    ======================== */
    public function pdf() {
        $id = $_GET['id'] ?? null;
        if (!$id) return;

        $stmt = $this->pdo->prepare("
            SELECT 
                c.id AS id_contrarecibo, 
                c.fecha AS fecha_contrarecibo,
                p.nombre AS proveedor, 
                f.id AS id_factura, 
                f.fecha1 AS fecha_factura, 
                f.cantidad AS cantidad_factura,
                f.descripcion AS descripcion_factura
            FROM contrarecibos c
            LEFT JOIN proveedores p ON c.proveedor = p.id
            LEFT JOIN facturas f ON f.contrarecibo = c.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->registrarBitacora("Generó PDF del contrarecibo ID: $id");

        require __DIR__ . '/../views/contrarecibos/pdf.php';
    }

    /* =======================
       FORM AGREGAR
    ======================== */
    public function agregar() {
        $proveedorModel = new Proveedores($this->pdo);
        $proveedoresPendientes = $proveedorModel->getConFacturasPendientes();
        require __DIR__ . '/../views/contrarecibos/agregar.php';
    }

    /* =======================
       GUARDAR NUEVO
    ======================== */
    public function store() {

        $proveedor = $_POST['proveedor'] ?? null;
        $facturasSeleccionadas = $_POST['facturas'] ?? [];

        if (!$proveedor) {
            die("Error: No seleccionaste proveedor");
        }

        $fecha = date("Y-m-d");

        $stmt = $this->pdo->prepare("
            INSERT INTO contrarecibos (proveedor, fecha)
            VALUES (?, ?)
        ");
        $stmt->execute([$proveedor, $fecha]);

        $contrareciboId = $this->pdo->lastInsertId();

        $facturaModel = new Factura($this->pdo);
        $facturaModel->assignContrarecibo($contrareciboId, $facturasSeleccionadas);

        $this->registrarBitacora("Registró contrarecibo ID: $contrareciboId");

        header("Location: " . BASE_URL . "index.php?c=Contrarecibos&a=index");
        exit;
    }

    /* =======================
       EDITAR
    ======================== */
    public function edit() {
        $contrareciboModel = new Contrarecibo($this->pdo);
        $facturaModel = new Factura($this->pdo);

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "index.php?c=Contrarecibos&a=index");
            exit;
        }

        $contrarecibo = $contrareciboModel->getById($id);
        $facturas = $facturaModel->getAll();
        $facturasAsociadas = $facturaModel->getByContrareciboId($id);

        $facturasAsociadasIds = array_column($facturasAsociadas, 'id');

        require 'views/contrarecibos/editar.php';
    }

    /* =======================
       ACTUALIZAR
    ======================== */
    public function update() {
        $id = $_POST['id'];
        $facturasSeleccionadas = $_POST['facturas'] ?? [];

        $facturaModel = new Factura($this->pdo);
        $facturaModel->unassignContrarecibo($id);
        $facturaModel->assignContrarecibo($id, $facturasSeleccionadas);

        $this->registrarBitacora("Editó contrarecibo ID: $id");

        header("Location: " . BASE_URL . "index.php?c=Contrarecibos&a=index");
        exit;
    }

    /* =======================
       ELIMINAR
    ======================== */
    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . BASE_URL . "index.php?c=Contrarecibos&a=index");
            exit;
        }

        $facturaModel = new Factura($this->pdo);
        $contrareciboModel = new Contrarecibo($this->pdo);

        $facturaModel->unassignContrarecibo($id);
        $contrareciboModel->delete($id);

        $this->registrarBitacora("Eliminó contrarecibo ID: $id");

        header("Location: " . BASE_URL . "index.php?c=Contrarecibos&a=index");
        exit;
    }
    /* =======================
   OBTENER FACTURAS DE PROVEEDOR (AJAX)
======================= */
public function getFacturasProveedor() {
    header('Content-Type: application/json; charset=utf-8');

    $id = $_GET['id'] ?? null;

    if (!$id) {
        echo json_encode(['pendientes'=>[], 'pagadas'=>[]]);
        exit;
    }

    try {
        $facturaModel = new Factura($this->pdo);

        $pendientes = $facturaModel->getPendientesByProveedor($id);
        $pagadas    = $facturaModel->getPagadasByProveedor($id);

        echo json_encode([
            'pendientes' => $pendientes,
            'pagadas'    => $pagadas
        ]);
    } catch (\Exception $e) {
        echo json_encode([
            'pendientes'=>[],
            'pagadas'=>[],
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

}


