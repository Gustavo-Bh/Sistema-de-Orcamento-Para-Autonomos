<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class Empresa
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
                id, razao_social, nome_fantasia, rua,
                numero, bairro, cep, telefone, complemento,
                ie_rg, cpf_cnpj, numero_serie_nfe,
                ultimo_numero_nfe, certificado, senha,
                ambiente, cidade_id, created_at
            FROM empresas
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                id, razao_social, nome_fantasia, rua,
                numero, bairro, cep, telefone, complemento,
                ie_rg, cpf_cnpj, numero_serie_nfe,
                ultimo_numero_nfe, certificado, senha,
                ambiente, cidade_id, created_at
            FROM empresas
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByCnpj(string $cnpj): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                id, razao_social, nome_fantasia, rua,
                numero, bairro, cep, telefone, complemento,
                ie_rg, cpf_cnpj, numero_serie_nfe,
                ultimo_numero_nfe, certificado, senha,
                ambiente, cidade_id, created_at
            FROM empresas
            WHERE cpf_cnpj = ?
            LIMIT 1
        ");
        $stmt->execute([$cnpj]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        $sql = "
            INSERT INTO empresas (
                razao_social, nome_fantasia, rua,
                numero, bairro, cep, telefone, complemento,
                ie_rg, cpf_cnpj, numero_serie_nfe,
                ultimo_numero_nfe, certificado, senha,
                ambiente, cidade_id, created_at
            ) VALUES (
                :razao_social, :nome_fantasia, :rua,
                :numero, :bairro, :cep, :telefone, :complemento,
                :ie_rg, :cpf_cnpj, :numero_serie_nfe,
                :ultimo_numero_nfe, :certificado, :senha,
                :ambiente, :cidade_id, :created_at
            )
        ";

        // Sempre hash na senha, se enviada
        $senha = !empty($data['senha']) ? password_hash($data['senha'], PASSWORD_DEFAULT) : null;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':razao_social'      => $data['razao_social'],
            ':nome_fantasia'     => $data['nome_fantasia'],
            ':rua'               => $data['rua'],
            ':numero'            => $data['numero'],
            ':bairro'            => $data['bairro'],
            ':cep'               => $data['cep'],
            ':telefone'          => $data['telefone'],
            ':complemento'       => $data['complemento']       ?? null,
            ':ie_rg'             => $data['ie_rg'],
            ':cpf_cnpj'          => $data['cpf_cnpj'],
            ':numero_serie_nfe'  => $data['numero_serie_nfe'],
            ':ultimo_numero_nfe' => $data['ultimo_numero_nfe'],
            ':certificado'       => $data['certificado']       ?? '',
            ':senha'             => $senha,
            ':ambiente'          => $data['ambiente'],
            ':cidade_id'         => $data['cidade_id'],
            ':created_at'        => $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        // Se senha foi enviada e não está vazia, faz hash, senão mantém a antiga
        if (isset($data['senha']) && $data['senha'] !== '') {
            $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
        } else {
            // Busca a senha já salva, para não sobrescrever com vazio
            $stmt = $this->db->prepare("SELECT senha FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            $data['senha'] = $stmt->fetchColumn();
        }

        $sql = "
            UPDATE empresas SET
                razao_social      = :razao_social,
                nome_fantasia     = :nome_fantasia,
                rua               = :rua,
                numero            = :numero,
                bairro            = :bairro,
                cep               = :cep,
                telefone          = :telefone,
                complemento       = :complemento,
                ie_rg             = :ie_rg,
                cpf_cnpj          = :cpf_cnpj,
                numero_serie_nfe  = :numero_serie_nfe,
                ultimo_numero_nfe = :ultimo_numero_nfe,
                certificado       = :certificado,
                senha             = :senha,
                ambiente          = :ambiente,
                cidade_id         = :cidade_id
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':razao_social'      => $data['razao_social'],
            ':nome_fantasia'     => $data['nome_fantasia'],
            ':rua'               => $data['rua'],
            ':numero'            => $data['numero'],
            ':bairro'            => $data['bairro'],
            ':cep'               => $data['cep'],
            ':telefone'          => $data['telefone'],
            ':complemento'       => $data['complemento']       ?? null,
            ':ie_rg'             => $data['ie_rg'],
            ':cpf_cnpj'          => $data['cpf_cnpj'],
            ':numero_serie_nfe'  => $data['numero_serie_nfe'],
            ':ultimo_numero_nfe' => $data['ultimo_numero_nfe'],
            ':certificado'       => $data['certificado']       ?? '',
            ':senha'             => $data['senha'],
            ':ambiente'          => $data['ambiente'],
            ':cidade_id'         => $data['cidade_id'],
            ':id'                => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM empresas WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
