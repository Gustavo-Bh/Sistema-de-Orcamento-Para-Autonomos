<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class Orcamento
{
    private PDO $db;

    public function __construct()
    {
        // Database.php já foi incluído antes no index
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT
                id, cliente_id, empresa_id, data_emissao,
                total_produtos, total_servicos, total_geral,
                created_at
            FROM orcamentos
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                id, cliente_id, empresa_id, data_emissao,
                total_produtos, total_servicos, total_geral,
                created_at
            FROM orcamentos
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO orcamentos (
                cliente_id, empresa_id, data_emissao,
                total_produtos, total_servicos, total_geral,
                created_at
            ) VALUES (
                :cliente_id, :empresa_id, :data_emissao,
                :total_produtos, :total_servicos, :total_geral,
                :created_at
            )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':cliente_id'     => $data['cliente_id'],
            ':empresa_id'     => $data['empresa_id'],
            ':data_emissao'   => $data['data_emissao'],
            ':total_produtos' => $data['total_produtos'],
            ':total_servicos' => $data['total_servicos'],
            ':total_geral'    => $data['total_geral'],
            ':created_at'     => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE orcamentos SET
                cliente_id     = :cliente_id,
                empresa_id     = :empresa_id,
                data_emissao   = :data_emissao,
                total_produtos = :total_produtos,
                total_servicos = :total_servicos,
                total_geral    = :total_geral
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute([
            ':cliente_id'     => $data['cliente_id'],
            ':empresa_id'     => $data['empresa_id'],
            ':data_emissao'   => $data['data_emissao'],
            ':total_produtos' => $data['total_produtos'],
            ':total_servicos' => $data['total_servicos'],
            ':total_geral'    => $data['total_geral'],
            ':id'             => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM orcamentos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
