<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Cliente
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** API web “normal” */
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT
                id, nome, rua, numero, bairro, cep,
                telefone, complemento, ie_rg, cpf_cnpj,
                contribuinte, cidade_id, created_at
            FROM clientes
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna todos os clientes vinculados à empresa-usuário,
     * usando o código que está em clientes.empresa_cnpj (ex: "90").
     */
    public function getAllApp(?string $empresaCnpj = null): array
    {
        if (!$empresaCnpj) {
            return [];
        }

        $stmt = $this->db->prepare("
            SELECT
                id, nome, rua, numero, bairro, cep,
                telefone, complemento, ie_rg, cpf_cnpj,
                contribuinte, cidade_id, created_at
            FROM clientes
            WHERE empresa_cnpj = ?
        ");
        $stmt->execute([$empresaCnpj]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                id, nome, rua, numero, bairro, cep,
                telefone, complemento, ie_rg, cpf_cnpj,
                contribuinte, cidade_id, created_at
            FROM clientes
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO clientes (
                nome, rua, numero, bairro, cep,
                telefone, complemento, ie_rg, cpf_cnpj,
                contribuinte, cidade_id, created_at
            ) VALUES (
                :nome, :rua, :numero, :bairro, :cep,
                :telefone, :complemento, :ie_rg, :cpf_cnpj,
                :contribuinte, :cidade_id, :created_at
            )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'         => $data['nome'],
            ':rua'          => $data['rua'],
            ':numero'       => $data['numero'],
            ':bairro'       => $data['bairro'],
            ':cep'          => $data['cep'],
            ':telefone'     => $data['telefone'],
            ':complemento'  => $data['complemento'] ?? null,
            ':ie_rg'        => $data['ie_rg'],
            ':cpf_cnpj'     => $data['cpf_cnpj'],
            ':contribuinte' => $data['contribuinte'],
            ':cidade_id'    => $data['cidade_id'],
            ':created_at'   => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE clientes SET
                nome         = :nome,
                rua          = :rua,
                numero       = :numero,
                bairro       = :bairro,
                cep          = :cep,
                telefone     = :telefone,
                complemento  = :complemento,
                ie_rg        = :ie_rg,
                cpf_cnpj     = :cpf_cnpj,
                contribuinte = :contribuinte,
                cidade_id    = :cidade_id
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute([
            ':nome'         => $data['nome'],
            ':rua'          => $data['rua'],
            ':numero'       => $data['numero'],
            ':bairro'       => $data['bairro'],
            ':cep'          => $data['cep'],
            ':telefone'     => $data['telefone'],
            ':complemento'  => $data['complemento'] ?? null,
            ':ie_rg'        => $data['ie_rg'],
            ':cpf_cnpj'     => $data['cpf_cnpj'],
            ':contribuinte' => $data['contribuinte'],
            ':cidade_id'    => $data['cidade_id'],
            ':id'           => $id,
        ]);
    }

    public function createApp(array $data): int
    {
        $sql = "
            INSERT INTO clientes (
                nome, rua, numero, bairro, cep,
                telefone, complemento, ie_rg, cpf_cnpj,
                contribuinte, cidade_id, empresa_cnpj, created_at
            ) VALUES (
                :nome, :rua, :numero, :bairro, :cep,
                :telefone, :complemento, :ie_rg, :cpf_cnpj,
                :contribuinte, :cidade_id, :empresa_cnpj, :created_at
            )
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nome'          => $data['nome'],
            ':rua'           => $data['rua'],
            ':numero'        => $data['numero'],
            ':bairro'        => $data['bairro'],
            ':cep'           => $data['cep'],
            ':telefone'      => $data['telefone'],
            ':complemento'   => $data['complemento'] ?? null,
            ':ie_rg'         => $data['ie_rg'],
            ':cpf_cnpj'      => $data['cpf_cnpj'],
            ':contribuinte'  => $data['contribuinte'],
            ':cidade_id'     => $data['cidade_id'],
            ':empresa_cnpj'  => $data['empresa_cnpj'],
            ':created_at'    => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateApp(int $id, array $data): bool
    {
        $sql = "
            UPDATE clientes SET
                nome          = :nome,
                rua           = :rua,
                numero        = :numero,
                bairro        = :bairro,
                cep           = :cep,
                telefone      = :telefone,
                complemento   = :complemento,
                ie_rg         = :ie_rg,
                cpf_cnpj      = :cpf_cnpj,
                contribuinte  = :contribuinte,
                cidade_id     = :cidade_id,
                empresa_cnpj  = :empresa_cnpj
            WHERE id = :id
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'          => $data['nome'],
            ':rua'           => $data['rua'],
            ':numero'        => $data['numero'],
            ':bairro'        => $data['bairro'],
            ':cep'           => $data['cep'],
            ':telefone'      => $data['telefone'],
            ':complemento'   => $data['complemento'] ?? null,
            ':ie_rg'         => $data['ie_rg'],
            ':cpf_cnpj'      => $data['cpf_cnpj'],
            ':contribuinte'  => $data['contribuinte'],
            ':cidade_id'     => $data['cidade_id'],
            ':empresa_cnpj'  => $data['empresa_cnpj'],
            ':id'            => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM clientes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
