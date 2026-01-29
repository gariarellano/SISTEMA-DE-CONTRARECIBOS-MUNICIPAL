<?php
class Factura {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getByContrareciboId($contrarecibo_id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                id, 
                numero, 
                fecha1 AS fecha_factura, 
                cantidad
            FROM facturas
            WHERE contrarecibo = ?
        ");
        $stmt->execute([$contrarecibo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendientesByProveedor($proveedorId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                id, 
                numero, 
                cantidad, 
                fecha1 AS fecha_factura
            FROM facturas
            WHERE proveedor = ?
            AND (contrarecibo IS NULL OR contrarecibo = '')
            ORDER BY fecha1 DESC
        ");
        $stmt->execute([$proveedorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPagadasByProveedor($proveedorId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                id, 
                numero, 
                cantidad, 
                fecha1 AS fecha_factura
            FROM facturas
            WHERE proveedor = ?
            AND contrarecibo IS NOT NULL
            AND contrarecibo != ''
            ORDER BY fecha1 DESC
        ");
        $stmt->execute([$proveedorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll() {
    $stmt = $this->pdo->query("
        SELECT 
            f.id,
            f.numero,
            f.fecha1 AS fecha_factura,
            YEAR(f.fecha1) AS anio,
            f.cantidad,
            f.contrarecibo,
            f.descripcion,

            -- 1 = se debe, 2 = pagada
            CASE 
                WHEN f.contrarecibo IS NULL 
                    OR f.contrarecibo = '' 
                    OR f.contrarecibo = 0
                THEN 1
                ELSE 2
            END AS estatus_num,

            p.id AS proveedor_id,
            p.nombre AS proveedor,
            p.fondo AS fondo
        FROM facturas f
        INNER JOIN proveedores p ON f.proveedor = p.id
        ORDER BY f.id DESC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function unassignContrarecibo($contrareciboId)
    {
        $sql = "UPDATE facturas SET contrarecibo = 0 WHERE contrarecibo = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$contrareciboId]);
    }

    // ✔️ ESTE ES EL ÚNICO QUE SE DEBE QUEDAR
    public function assignContrarecibo($id_contrarecibo, $facturas) {
        $sql = "UPDATE facturas SET contrarecibo = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);

        foreach ($facturas as $factura_id) {
            $stmt->execute([$id_contrarecibo, $factura_id]);
        }

        return true;
    }
}



