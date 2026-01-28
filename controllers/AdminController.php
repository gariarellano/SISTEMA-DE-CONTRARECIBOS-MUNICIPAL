<?php
class AdminController {
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ===============================
// LOGIN
// ===============================
public function login() {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $usuario  = trim($_POST['usuario']);
        $password = $_POST['password'];

        // Buscar usuario
        $sql = "SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':usuario' => $usuario]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            // Guardar en sesión con contraseña en claro
            $_SESSION['usuario'] = [
                'id'             => $user['id'],
                'usuario'        => $user['usuario'],
                'rol'            => $user['rol'],
                'password_plain' => $password,       // <-- para mostrar en perfil
                'password'       => $user['password'] // <-- hash por seguridad
            ];

            // Registrar bitácora
            require_once __DIR__ . '/../models/Bitacora.php';
            (new Bitacora($this->pdo))->registrar('Inicio de sesión', 'Login');

            header("Location: " . BASE_URL . "index.php?c=admin&a=principal");
            exit;

        } else {
            $error = "Usuario o contraseña incorrectos";
            require __DIR__ . '/../views/admin/login.php';
        }

    } else {
        require __DIR__ . '/../views/admin/login.php';
    }
}


    // ===============================
    // PANEL PRINCIPAL
    // ===============================
    public function principal() {

        if (!isset($_SESSION['usuario'])) {
            header("Location: " . BASE_URL . "index.php?c=admin&a=login");
            exit;
        }

        require __DIR__ . '/../views/admin/principal.php';
    }

    // ===============================
    // LOGOUT
    // ===============================
    public function logout() {

        if (isset($_SESSION['usuario'])) {
            require_once __DIR__ . '/../models/Bitacora.php';
            (new Bitacora($this->pdo))->registrar('Cierre de sesión', 'Login');
        }

        session_destroy();
        header("Location: " . BASE_URL . "index.php?c=admin&a=login");
        exit;
    }

    // ===============================
    // BITÁCORA (VISTA)
    // ===============================
    public function bitacora() {

        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            header("Location: " . BASE_URL . "index.php?c=admin&a=principal");
            exit;
        }

        require __DIR__ . '/../views/admin/bitacora.php';
    }

    // ===============================
    // BITÁCORA PDF
    // ===============================
    public function bitacoraPdf() {

        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            exit;
        }

        $inicio = $_GET['inicio'] ?? null;
        $fin    = $_GET['fin'] ?? null;

        if (!$inicio || !$fin) {
            die("Rango de fechas inválido");
        }

        require_once __DIR__ . '/../models/Bitacora.php';
        $bitacora = new Bitacora($this->pdo);

        // 🔹 Igual que $rows en contrarecibo
        $registros = $bitacora->getByPeriodo($inicio, $fin);

        // Variables disponibles en el PDF
        require __DIR__ . '/../views/reportes/bitacora_pdf.php';
    }

    // ===============================
    // REDIRECCIÓN
    // ===============================
    public function index() {
        header("Location: " . BASE_URL . "index.php?c=admin&a=principal");
        exit;
    }
}

