<?php
class Contrarecibo {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /** Obtiene todos los contrarecibos con sus facturas */
    public function getAll() {
        $sql = "SELECT 
                    c.id AS id_contrarecibo, 
                    c.fecha AS fecha_contrarecibo, 
                    p.nombre AS proveedor, 
                    f.id AS id_factura, 
                    f.fecha1 AS fecha_factura, 
                    f.suma AS total_factura
                FROM contrarecibos c
                LEFT JOIN proveedores p ON c.proveedor = p.id
                LEFT JOIN facturas f ON f.contrarecibo = c.id
                ORDER BY c.id DESC, f.id ASC";
        
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $contrarecibos = [];
        foreach ($data as $row) {
            $id = $row['id_contrarecibo'];

            if (!isset($contrarecibos[$id])) {
                $contrarecibos[$id] = [
                    'id' => $row['id_contrarecibo'],
                    'proveedor' => $row['proveedor'] ?? 'Sin proveedor',
                    'fecha' => $row['fecha_contrarecibo'],
                    'facturas' => []
                ];
            }

            if (!empty($row['id_factura'])) {
                $contrarecibos[$id]['facturas'][] = [
                    'id' => $row['id_factura'],
                    'fecha' => $row['fecha_factura'],
                    'total' => $row['total_factura']
                ];
            }
        }

        return array_values($contrarecibos);
    }

    /** Obtiene un contrarecibo por ID */
    public function getById($id) {
        $stmt = $this->pdo->prepare("
            SELECT 
                c.id,
                c.proveedor,
                c.fecha AS fecha_contrarecibo,
                p.nombre AS proveedor_nombre
            FROM contrarecibos c
            LEFT JOIN proveedores p ON c.proveedor = p.id
            WHERE c.id = ?
        ");
        $stmt->execute([$id]);
        $contrarecibo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($contrarecibo) {
            $stmt2 = $this->pdo->prepare("
                SELECT 
                    id, 
                    fecha1 AS fecha_factura, 
                    suma AS total
                FROM facturas
                WHERE contrarecibo = ?
            ");
            $stmt2->execute([$id]);
            $contrarecibo['facturas'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $contrarecibo;
    }

    /** Inserta un nuevo contrarecibo */
    public function insert($proveedor, $fecha) {
        $stmt = $this->pdo->prepare("
            INSERT INTO contrarecibos (proveedor, fecha)
            VALUES (?, ?)
        ");
        return $stmt->execute([$proveedor, $fecha]);
    }

    /** Actualiza un contrarecibo */
    public function update($id, $proveedor, $fecha) {
        $stmt = $this->pdo->prepare("
            UPDATE contrarecibos
            SET proveedor = ?, fecha = ?
            WHERE id = ?
        ");
        return $stmt->execute([$proveedor, $fecha, $id]);
    }

    /** Elimina un contrarecibo */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM contrarecibos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
