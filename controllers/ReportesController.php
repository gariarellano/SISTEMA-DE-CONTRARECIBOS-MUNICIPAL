<?php
class ReportesController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 🔒 Solo administradores
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
            header("Location: " . BASE_URL . "index.php?c=admin&a=login");
            exit;
        }
    }

    // ===============================
    // VISTA DE REPORTES
    // ===============================
   public function index() {

    require_once __DIR__ . '/../models/Bitacora.php';
    $bitacora = new Bitacora($this->pdo);

    $inicio = $_GET['inicio'] ?? null;
    $fin    = $_GET['fin'] ?? null;

    if ($inicio && $fin) {
        // Filtrar por fechas (más reciente primero)
        $registros = $bitacora->obtenerPorFechas($inicio, $fin);
    } else {
        // Mostrar todo por defecto
        $registros = $bitacora->obtenerTodo();
    }

    require __DIR__ . '/../views/reportes/index.php';
}



    // ===============================
    // GENERAR PDF POR FECHAS
    // ===============================
    public function bitacoraPdf() {

        $inicio = $_GET['inicio'] ?? null;
        $fin    = $_GET['fin'] ?? null;

        if (!$inicio || !$fin) {
            die("Debe seleccionar un rango de fechas válido");
        }

        require_once __DIR__ . '/../models/Bitacora.php';
        $bitacora = new Bitacora($this->pdo);

        // Obtener registros filtrados
        $registros = $bitacora->obtenerPorFechas($inicio, $fin);

        // Registrar acción
        $bitacora->registrar(
            "Descargó reporte PDF de bitácora ($inicio a $fin)",
            "Reportes"
        );

        require __DIR__ . '/../views/reportes/reporte_pdf.php';
    }
}



