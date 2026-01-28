<?php
class Usuario {
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    /* Obtiene un usuario por su nombre de usuario */
    public function getByUsername($usuario){
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario=? LIMIT 1");
        $stmt->execute([$usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* Valida login usando contraseña hasheada */
    public function validarLogin($usuario, $password){
        $user = $this->getByUsername($usuario);

        if (!$user) {
            return false;
        }

        // Verificar hash
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /* Actualiza contraseña (hash + texto claro si lo necesitas) */
    public function actualizarPassword($id, $newPassword){
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare("
            UPDATE usuarios 
            SET password = ?, password_plain = ?
            WHERE id = ?
        ");

        return $stmt->execute([$hash, $newPassword, $id]);
    }

    /* Obtiene un usuario por ID */
    public function getById($id){
        $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE id=? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* Obtiene todos los usuarios, opcionalmente excluyendo uno */
    public function getUsuarios($excludeUser = null) {
        if ($excludeUser) {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE usuario != ?");
            $stmt->execute([$excludeUser]);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM usuarios");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Elimina un usuario por nombre */
    public function eliminarUsuario($usuario) {
        $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE usuario = ?");
        return $stmt->execute([$usuario]);
    }
}




