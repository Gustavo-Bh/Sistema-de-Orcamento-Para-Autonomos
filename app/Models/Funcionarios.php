<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class Funcionario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Já existe:
    public function findByUsername(string $username): ?array
    {
        $sql  = "SELECT id, username, password
                 FROM users
                 WHERE username = ? 
                 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $func = $stmt->fetch(PDO::FETCH_ASSOC);
        return $func ?: null;
    }

    // NOVO MÉTODO para login com CNPJ:
    public function findByUsernameAndCnpj(string $username): ?array
    {
        $sql  = "SELECT id, username, password
                 FROM users
                 WHERE username = ?
                 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $func = $stmt->fetch(PDO::FETCH_ASSOC);
        return $func ?: null;
    }
}
