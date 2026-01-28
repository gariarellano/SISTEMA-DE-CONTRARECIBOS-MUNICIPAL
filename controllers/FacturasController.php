<?php
class FacturasController {
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['usuario'])) {
            header("Location: ".BASE_URL."index.php?c=Admin&a=login");
            exit();
        }
    }

    /* ===============================
       LISTADO
    =============================== */
    public function index(){

        $where = [];

        if (!empty($_GET['proveedor'])) {
            $where[] = "p.nombre LIKE '%" . $_GET['proveedor'] . "%'";
        }

        if (!empty($_GET['estatus'])) {
            if ($_GET['estatus'] == 1)
                $where[] = "(f.contrarecibo IS NULL OR f.contrarecibo = '' OR f.contrarecibo = 0)";
            if ($_GET['estatus'] == 2)
                $where[] = "(f.contrarecibo IS NOT NULL AND f.contrarecibo != '' AND f.contrarecibo != 0)";
        }

        if (!empty($_GET['fondo'])) {
            $where[] = "p.fondo = '" . $_GET['fondo'] . "'";
        }

        if (!empty($_GET['numero'])) {
            $where[] = "f.numero LIKE '%" . $_GET['numero'] . "%'";
        }

        $whereSQL = count($where) ? "WHERE ".implode(" AND ", $where) : "";

        $sql = "
            SELECT 
                f.id,
                p.nombre AS proveedor,
                p.fondo,
                f.descripcion,
                f.numero,
                f.cantidad,
                CASE 
                    WHEN f.contrarecibo IS NULL OR f.contrarecibo = '' OR f.contrarecibo = 0 
                    THEN 'Se debe'
                    ELSE 'Pagada'
                END AS estatus,
                f.fecha1,
                f.fecha2,
                f.cheque,
                f.suma,
                f.contrarecibo
            FROM facturas f
            LEFT JOIN proveedores p ON f.proveedor = p.id
            $whereSQL
            ORDER BY f.id DESC
        ";

        $facturas = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $proveedores = $this->pdo->query("SELECT DISTINCT nombre FROM proveedores")->fetchAll(PDO::FETCH_COLUMN);
        $fondos = $this->pdo->query("SELECT DISTINCT fondo FROM proveedores ORDER BY fondo")->fetchAll(PDO::FETCH_COLUMN);

        require __DIR__.'/../views/facturas/index.php';
    }

    /* ===============================
       AGREGAR
    =============================== */
    public function agregar(){
        $proveedores = $this->pdo
            ->query("SELECT id, nombre FROM proveedores ORDER BY nombre")
            ->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__.'/../views/facturas/agregar.php';
    }

    public function guardar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $sql = "INSERT INTO facturas
                (proveedor, descripcion, numero, cantidad, estatus, fecha1, fecha2, cheque, suma, contrarecibo)
                VALUES (:proveedor, :descripcion, :numero, :cantidad, :estatus, :fecha1, :fecha2, :cheque, :suma, 0)";

            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':proveedor'   => $_POST['proveedor'],
                ':descripcion' => $_POST['descripcion'],
                ':numero'      => $_POST['numero'],
                ':cantidad'    => $_POST['cantidad'],
                ':estatus'     => $_POST['estatus'],
                ':fecha1'      => $_POST['fecha1'],
                ':fecha2'      => $_POST['fecha2'],
                ':cheque'      => $_POST['cheque'],
                ':suma'        => $_POST['suma']
            ]);

            if ($ok) {
                $this->registrarBitacora("Alta de factura", "Facturas");
                header("Location: ".BASE_URL."index.php?c=Facturas&a=index&success=1");
                exit;
            }
        }
    }

    /* ===============================
       EDITAR
    =============================== */
    public function edit(){
        $id = intval($_GET['id'] ?? 0);

        $stmt = $this->pdo->prepare("SELECT * FROM facturas WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $factura = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$factura) {
            header("Location: ".BASE_URL."index.php?c=Facturas&a=index&error=1");
            exit;
        }

        $proveedores = $this->pdo
            ->query("SELECT id, nombre FROM proveedores ORDER BY nombre")
            ->fetchAll(PDO::FETCH_ASSOC);

        require __DIR__.'/../views/facturas/editar.php';
    }

    public function actualizar(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $sql = "UPDATE facturas SET
                        proveedor = :proveedor,
                        descripcion = :descripcion,
                        numero = :numero,
                        cantidad = :cantidad,
                        estatus = :estatus,
                        fecha1 = :fecha1,
                        fecha2 = :fecha2,
                        cheque = :cheque,
                        suma = :suma,
                        contrarecibo = :contrarecibo
                    WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':proveedor'    => $_POST['proveedor'],
                ':descripcion'  => $_POST['descripcion'],
                ':numero'       => $_POST['numero'],
                ':cantidad'     => $_POST['cantidad'],
                ':estatus'      => $_POST['estatus'],
                ':fecha1'       => $_POST['fecha1'],
                ':fecha2'       => $_POST['fecha2'],
                ':cheque'       => $_POST['cheque'],
                ':suma'         => $_POST['suma'],
                ':contrarecibo' => $_POST['contrarecibo'],
                ':id'           => $_POST['id']
            ]);

            if ($ok) {
                $this->registrarBitacora("Edición de factura ID ".$_POST['id'], "Facturas");
                header("Location: ".BASE_URL."index.php?c=Facturas&a=index&updated=1");
                exit;
            }
        }
    }

    /* ===============================
       ELIMINAR
    =============================== */
    public function delete(){
        $id = intval($_GET['id'] ?? 0);

        $stmt = $this->pdo->prepare("DELETE FROM facturas WHERE id = :id");
        $ok = $stmt->execute([':id' => $id]);

        if ($ok) {
            $this->registrarBitacora("Eliminación de factura ID $id", "Facturas");
            header("Location: ".BASE_URL."index.php?c=Facturas&a=index&deleted=1");
            exit;
        }
    }

    /* ===============================
       BITÁCORA
    =============================== */
    private function registrarBitacora($accion, $modulo){
        $sql = "INSERT INTO bitacora
            (usuario_id, usuario_nombre, accion, modulo, fecha, hora)
            VALUES (:uid, :nombre, :accion, :modulo, CURDATE(), CURTIME())";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':uid'    => $_SESSION['usuario']['id'],
            ':nombre' => $_SESSION['usuario']['usuario'],
            ':accion' => $accion,
            ':modulo' => $modulo
        ]);
    }
}




