<?php
require_once __DIR__ . '/../config.php';  
require_once __DIR__ . '/../models/Usuario.php';

class PerfilController {
    private $pdo;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        global $pdo;   
        $this->pdo = $pdo;

        // Validar sesión
        if (!isset($_SESSION['usuario'])) {
            header("Location: " . BASE_URL . "index.php?c=Admin&a=login");
            exit();
        }
    }

    // ===============================
    // MOSTRAR PERFIL
    // ===============================
    public function index() {
        $usuarios = [];

        // SOLO admin ve la lista completa de usuarios
        $rol = is_array($_SESSION['usuario']) 
            ? $_SESSION['usuario']['rol'] 
            : $_SESSION['usuario']->rol;

        if ($rol === 'admin') {
            $usuarioModel = new Usuario($this->pdo);
            $usuarios = $usuarioModel->getUsuarios();
        }

        require_once __DIR__ . '/../views/perfil/index.php';
    }

    // ===============================
    // ACTUALIZAR CONTRASEÑA PROPIA
    // ===============================
  public function actualizar() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . BASE_URL . "index.php?c=Perfil&a=index");
        exit();
    }

    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password_confirm'] ?? '';

    // Validar que nueva y confirmación coincidan
    if ($pass === '' || $pass !== $pass2) {
        header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&error=1");
        exit();
    }

    $usuario = is_array($_SESSION['usuario'])
        ? $_SESSION['usuario']
        : (array) $_SESSION['usuario'];

    $id = $usuario['id'];

    // Guardar nueva contraseña (hash + claro)
    $hash = password_hash($pass, PASSWORD_BCRYPT);

    $stmt = $this->pdo->prepare(
        "UPDATE usuarios SET password = :hash, password_plain = :plain WHERE id = :id"
    );

    $ok = $stmt->execute([
        ':hash'  => $hash,
        ':plain' => $pass,   // <-- contraseña en claro para toggle
        ':id'    => $id
    ]);

    if ($ok) {
        // Actualizar sesión
        $_SESSION['usuario']['password'] = $hash;
        $_SESSION['usuario']['password_plain'] = $pass;
        header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&ok=1");
    } else {
        header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&error=2");
    }
    exit();
}


    // ===============================
    // CREAR NUEVO USUARIO (ADMIN)
    // ===============================
    public function crearUsuario() {
        $rol = is_array($_SESSION['usuario'])
            ? $_SESSION['usuario']['rol']
            : $_SESSION['usuario']->rol;

        if ($rol !== 'admin') {
            header("Location: " . BASE_URL . "index.php?c=Perfil&a=index");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = trim($_POST['nuevo_usuario'] ?? '');
            $pass    = $_POST['nuevo_password'] ?? '';
            $pass2   = $_POST['nuevo_password_confirm'] ?? '';

            if ($usuario === '' || $pass === '' || $pass2 === '') {
                header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&errorUser=empty");
                exit();
            }

            if ($pass !== $pass2) {
                header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&errorUser=pass");
                exit();
            }

            $check = $this->pdo->prepare("SELECT id FROM usuarios WHERE usuario = ?");
            $check->execute([$usuario]);

            if ($check->rowCount() > 0) {
                header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&errorUser=exists");
                exit();
            }

            $hash = password_hash($pass, PASSWORD_BCRYPT);

            $sql = "INSERT INTO usuarios (usuario, password, password_plain, rol)
                    VALUES (:u, :p, :plain, 'usuario')";

            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([
                ':u'     => $usuario,
                ':p'     => $hash,
                ':plain' => $pass
            ]);

            if ($ok) {
                header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&okUser=1");
            } else {
                header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&errorUser=db");
            }
            exit();
        }
    }

    // ===============================
    // ELIMINAR USUARIO (ADMIN)
    // ===============================
    public function eliminar() {
        $rol = is_array($_SESSION['usuario'])
            ? $_SESSION['usuario']['rol']
            : $_SESSION['usuario']->rol;

        if ($rol !== 'admin') {
            die("Acceso denegado. Solo el administrador puede eliminar usuarios.");
        }

        if (!isset($_GET['usuario'])) {
            die("Usuario no especificado");
        }

        $usuario = $_GET['usuario'];
        $usuarioModel = new Usuario($this->pdo);

        if ($usuarioModel->eliminarUsuario($usuario)) {
            header("Location: " . BASE_URL . "index.php?c=Perfil&a=index&msg=deleted");
            exit;
        } else {
            die("Error al eliminar usuario.");
        }
    }
}

