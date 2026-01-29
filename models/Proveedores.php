<?php
class Proveedores {
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function getAll(){
        $stmt = $this->pdo->query("SELECT * FROM proveedores ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Obtiene un proveedor por ID */
    public function getById($id){
        $stmt = $this->pdo->prepare("SELECT * FROM proveedores WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Inserta un nuevo proveedor */
    public function insert($rfc, $nombre, $domicilio, $telefono, $email, $fondo){
        $stmt = $this->pdo->prepare("
            INSERT INTO proveedores (rfc, nombre, domicilio, telefono, email, fondo) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$rfc, $nombre, $domicilio, $telefono, $email, $fondo]);
    }

    /** Actualiza un proveedor existente */
    public function update($id, $rfc, $nombre, $domicilio, $telefono, $email, $fondo){
        $stmt = $this->pdo->prepare("
            UPDATE proveedores 
            SET rfc=?, nombre=?, domicilio=?, telefono=?, email=?, fondo=? 
            WHERE id=?
        ");
        return $stmt->execute([$rfc, $nombre, $domicilio, $telefono, $email, $fondo, $id]);
    }

    /*Elimina un proveedor */
    public function delete($id){
        $stmt = $this->pdo->prepare("DELETE FROM proveedores WHERE id=?");
        return $stmt->execute([$id]);
    }

    /* Busca proveedores por campo específico */
    public function searchCampo($campo, $q){
        $allowed = ['id','nombre','rfc','telefono'];
        if(!in_array($campo, $allowed)) $campo = 'nombre';

        $stmt = $this->pdo->prepare("SELECT * FROM proveedores WHERE $campo LIKE :q ORDER BY id DESC");
        $stmt->execute([':q' => "%$q%"]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  public function getConFacturasPendientes() {
    $stmt = $this->pdo->prepare("
        SELECT 
            p.id,
            p.nombre
        FROM proveedores p
        INNER JOIN facturas f ON f.proveedor = p.id
        WHERE f.estatus = 1
        AND (f.contrarecibo IS NULL OR f.contrarecibo = 0)
        GROUP BY p.id, p.nombre
        ORDER BY p.nombre ASC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}

