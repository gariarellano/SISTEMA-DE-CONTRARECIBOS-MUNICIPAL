<?php
require_once "models/Proveedores.php";

class ProveedoresController {
    private $model;
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
        $this->model = new Proveedores($pdo);

        if(session_status() === PHP_SESSION_NONE) session_start();

        if(!isset($_SESSION['usuario'])){
            header("Location: ".BASE_URL."index.php?c=Admin&a=login");
            exit;
        }
    }

    /* =======================
       MÉTODO PARA BITÁCORA
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
            ':modulo'         => 'Proveedores'
        ]);
    }

    /* =======================
       VISTA PRINCIPAL
    ======================== */
    public function index(){
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $campo = isset($_GET['campo']) ? $_GET['campo'] : 'nombre';

        $proveedores = ($q !== '') 
            ? $this->model->searchCampo($campo, $q) 
            : $this->model->getAll();

        require "views/proveedores/index.php";
    }

    /* =======================
       CREAR PROVEEDOR
    ======================== */
    public function create() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->model->insert(
                $_POST['rfc'],
                $_POST['nombre'],
                $_POST['domicilio'],
                $_POST['telefono'],
                $_POST['email'],
                $_POST['fondo']
            );

            $this->registrarBitacora("Registró proveedor: ".$_POST['nombre']);

            header('Location: ' . BASE_URL . 'index.php?c=Proveedores&a=index');
            exit;
        }

        require "views/proveedores/create.php";
    }

    /* =======================
       EDITAR PROVEEDOR
    ======================== */
    public function edit() {
        $id = $_GET['id'];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->model->update(
                $id,
                $_POST['rfc'],
                $_POST['nombre'],
                $_POST['domicilio'],
                $_POST['telefono'],
                $_POST['email'],
                $_POST['fondo']
            );

            $this->registrarBitacora("Editó proveedor ID: ".$id);

            header('Location: ' . BASE_URL . 'index.php?c=Proveedores&a=index');
            exit;
        }

        $proveedor = $this->model->getById($id);
        require "views/proveedores/edit.php";
    }

    /* =======================
       ELIMINAR PROVEEDOR
    ======================== */
    public function delete() {
        $id = $_GET['id'];

        $proveedor = $this->model->getById($id);
        $this->model->delete($id);

        $this->registrarBitacora("Eliminó proveedor: ".$proveedor['nombre']);

        header('Location: ' . BASE_URL . 'index.php?c=Proveedores&a=index');
        exit;
    }

    /* =======================
       AJAX SEARCH
    ======================== */
    public function ajaxSearch(){
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $campo = isset($_GET['campo']) ? $_GET['campo'] : 'nombre';

        $proveedores = ($q !== '') 
            ? $this->model->searchCampo($campo, $q)
            : $this->model->getAll();

        header('Content-Type: application/json');
        echo json_encode(array_values($proveedores));
        exit;
    }
}
