<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Servico
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT id, nome, valor, created_at
            FROM servicos
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, nome, valor, created_at
            FROM servicos
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO servicos (nome, valor, created_at)
            VALUES (:nome, :valor, :created_at)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'       => $data['nome'],
            ':valor'      => $data['valor'],
            ':created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE servicos SET
                nome  = :nome,
                valor = :valor
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'  => $data['nome'],
            ':valor' => $data['valor'],
            ':id'    => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM servicos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
