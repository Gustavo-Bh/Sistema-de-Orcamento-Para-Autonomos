<?php
namespace App\Models;

use App\Config\Database;
use PDO;
use PDOException;

class Servico
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os serviços cadastrados.
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query(
            "SELECT id, empresa_cnpj, descricao, valor, created_at FROM servicos"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna um serviço pelo ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT id, empresa_cnpj, descricao, valor, created_at
             FROM servicos
             WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Retorna todos os serviços de uma empresa pelo CNPJ.
     *
     * @param string $cnpj
     * @return array
     */
    public function getAllByEmpresaCnpj(string $cnpj): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, empresa_cnpj, descricao, valor, created_at
             FROM servicos
             WHERE empresa_cnpj = :cnpj"
        );
        $stmt->execute([':cnpj' => $cnpj]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo serviço e retorna seu ID.
     *
     * @param array $data
     * @return int
     * @throws PDOException
     */
    public function create(array $data): int
    {
        $sql = <<<SQL
INSERT INTO servicos (empresa_cnpj, descricao, valor, created_at)
VALUES (:empresa_cnpj, :descricao, :valor, :created_at)
SQL;
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':empresa_cnpj' => $data['empresa_cnpj'],
            ':descricao'    => $data['descricao'],
            ':valor'        => $data['valor'],
            ':created_at'   => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza os dados de um serviço.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE servicos SET descricao = :descricao, valor = :valor WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':descricao' => $data['descricao'],
            ':valor'     => $data['valor'],
            ':id'        => $id,
        ]);
    }

    /**
     * Exclui um serviço pelo ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM servicos WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
