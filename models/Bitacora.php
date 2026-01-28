<?php

class Bitacora {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ===============================
    // REGISTRAR ACCIÓN EN BITÁCORA
    // ===============================
    public function registrar($accion, $modulo) {

    $fecha = date('Y-m-d');
    $hora  = date('H:i:s');

    $sql = "
        INSERT INTO bitacora
        (usuario_id, usuario_nombre, accion, modulo, fecha, hora)
        VALUES (:uid, :nombre, :accion, :modulo, :fecha, :hora)
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':uid'    => $_SESSION['usuario']['id'],
        ':nombre' => $_SESSION['usuario']['usuario'],
        ':accion' => $accion,
        ':modulo' => $modulo,
        ':fecha'  => $fecha,
        ':hora'   => $hora
    ]);
}

    // ===============================
    // OBTENER TODA LA BITÁCORA
    // ===============================
    public function obtenerTodo() {
        $stmt = $this->pdo->query("
            SELECT usuario_nombre, accion, modulo, fecha, hora
            FROM bitacora
            ORDER BY fecha DESC, hora DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // OBTENER POR RANGO DE FECHAS
    // ===============================
    public function obtenerPorFechas($inicio, $fin) {
    $stmt = $this->pdo->prepare("
        SELECT usuario_nombre, accion, modulo, fecha, hora
        FROM bitacora
        WHERE fecha BETWEEN ? AND ?
        ORDER BY fecha DESC, hora DESC
    ");
    $stmt->execute([$inicio, $fin]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}



