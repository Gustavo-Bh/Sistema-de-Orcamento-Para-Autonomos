<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Empresa
{
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // BUSCA empresa_user por ID (usa a FK empresa_id do orçamento)
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM empresa_user WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // (opcional) já deixe pronto se quiser mostrar a empresa_cadastrada no PDF
    public function getCadastradaById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM empresa_cadastrada WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

  
public function getCadastroByCnpj(string $cnpj): ?array
{
    $sql = "
        SELECT
            empresa_cnpj,
            razao_social,
            nome_fantasia,
            rua,
            numero,
            bairro,
            cep,
            telefone,
            complemento,
            ie_rg,
            cpf_cnpj,
            numero_serie_nfe,
            ultimo_numero_nfe,
            certificado,
            ambiente,
            cidade_id,
            created_at
        FROM empresa_cadastrada
        WHERE empresa_cnpj = :cnpj
        LIMIT 1
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':cnpj' => $cnpj]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

public function getByCnpj(string $cnpj): ?array {
    $cnpj = preg_replace('/\D+/', '', $cnpj);
    $stmt = $this->db->prepare("
        SELECT * FROM empresa_user
        WHERE REPLACE(REPLACE(REPLACE(cpf_cnpj,'.',''),'-',''),'/','') = ?
        LIMIT 1
    ");
    $stmt->execute([$cnpj]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}




    /**
     * Busca um usuário (empresa_user) pelo CNPJ para autenticação
     *
     * @param string $cnpj
     * @return array|null
     */
    public function getUserByCnpj(string $cnpj): ?array
    {
        $sql = "
            SELECT
                id,
                razao_social,
                nome_fantasia,
                rua,
                numero,
                bairro,
                cep,
                telefone,
                complemento,
                ie_rg,
                cpf_cnpj,
                numero_serie_nfe,
                ultimo_numero_nfe,
                certificado,
                senha,
                ambiente,
                cidade_id,
                created_at
            FROM empresa_user
            WHERE cpf_cnpj = :cnpj
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cnpj' => $cnpj]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Busca o cadastro em empresa_cadastrada pelo CNPJ
     *
     * @param string $cnpj
     * @return array|null
     */
    

    /**
     * Cadastra um novo usuário em empresa_user e o respectivo registro em empresa_cadastrada
     *
     * @param array $data
     *   Campos obrigatórios em empresa_user:
     *     razao_social, nome_fantasia, rua, numero, bairro, cep, telefone,
     *     complemento, ie_rg, cpf_cnpj, numero_serie_nfe, ultimo_numero_nfe,
     *     certificado, senha, ambiente, cidade_id
     *   Campo adicional para empresa_cadastrada:
     *     empresa_cnpj
     *
     * @return int Retorna o ID gerado em empresa_user
     */
    public function createUserAndCadastro(array $data): int
    {
        $createdAt = date('Y-m-d H:i:s');

        // 1) Insere em empresa_user
        $sqlUser = "
            INSERT INTO empresa_user (
                razao_social, nome_fantasia, rua, numero, bairro,
                cep, telefone, complemento, ie_rg, cpf_cnpj,
                numero_serie_nfe, ultimo_numero_nfe, certificado,
                senha, ambiente, cidade_id, created_at
            ) VALUES (
                :razao_social, :nome_fantasia, :rua, :numero, :bairro,
                :cep, :telefone, :complemento, :ie_rg, :cpf_cnpj,
                :numero_serie_nfe, :ultimo_numero_nfe, :certificado,
                :senha, :ambiente, :cidade_id, :created_at
            )
        ";
        $stmt = $this->db->prepare($sqlUser);
        $stmt->execute([
            ':razao_social'      => $data['razao_social'],
            ':nome_fantasia'     => $data['nome_fantasia'],
            ':rua'               => $data['rua'],
            ':numero'            => $data['numero'],
            ':bairro'            => $data['bairro'],
            ':cep'               => $data['cep'],
            ':telefone'          => $data['telefone'],
            ':complemento'       => $data['complemento'] ?? null,
            ':ie_rg'             => $data['ie_rg'],
            ':cpf_cnpj'          => $data['cpf_cnpj'],
            ':numero_serie_nfe'  => $data['numero_serie_nfe'],
            ':ultimo_numero_nfe' => $data['ultimo_numero_nfe'],
            ':certificado'       => $data['certificado'] ?? '',
            ':senha'             => password_hash($data['senha'], PASSWORD_DEFAULT),
            ':ambiente'          => $data['ambiente'],
            ':cidade_id'         => $data['cidade_id'],
            ':created_at'        => $createdAt,
        ]);
        $userId = (int)$this->db->lastInsertId();

        // 2) Insere em empresa_cadastrada (sem FK nem senha)
        $sqlCad = "
            INSERT INTO empresa_cadastrada (
                empresa_cnpj, razao_social, nome_fantasia,
                rua, numero, bairro, cep, telefone, complemento,
                ie_rg, cpf_cnpj, numero_serie_nfe, ultimo_numero_nfe,
                certificado, ambiente, cidade_id, created_at
            ) VALUES (
                :empresa_cnpj, :razao_social, :nome_fantasia,
                :rua, :numero, :bairro, :cep, :telefone, :complemento,
                :ie_rg, :cpf_cnpj, :numero_serie_nfe, :ultimo_numero_nfe,
                :certificado, :ambiente, :cidade_id, :created_at
            )
        ";
        $stmt = $this->db->prepare($sqlCad);
        $stmt->execute([
            ':empresa_cnpj'      => $data['empresa_cnpj'],
            ':razao_social'      => $data['razao_social'],
            ':nome_fantasia'     => $data['nome_fantasia'],
            ':rua'               => $data['rua'],
            ':numero'            => $data['numero'],
            ':bairro'            => $data['bairro'],
            ':cep'               => $data['cep'],
            ':telefone'          => $data['telefone'],
            ':complemento'       => $data['complemento'] ?? null,
            ':ie_rg'             => $data['ie_rg'],
            ':cpf_cnpj'          => $data['cpf_cnpj'],
            ':numero_serie_nfe'  => $data['numero_serie_nfe'],
            ':ultimo_numero_nfe' => $data['ultimo_numero_nfe'],
            ':certificado'       => $data['certificado'] ?? '',
            ':ambiente'          => $data['ambiente'],
            ':cidade_id'         => $data['cidade_id'],
            ':created_at'        => $createdAt,
        ]);

        return $userId;
    }

    /**
     * Atualiza apenas dados de login em empresa_user
     *
     * @param int   $id
     * @param array $data
     * @return bool
     */
    public function updateUser(int $id, array $data): bool
{
    if (!empty($data['senha'])) {
        $data['senha'] = password_hash($data['senha'], PASSWORD_DEFAULT);
    } else {
        $stmtOld = $this->db->prepare("SELECT senha FROM empresa_user WHERE id = ?");
        $stmtOld->execute([$id]);
        $data['senha'] = $stmtOld->fetchColumn(); 
    }

    $sql = "
        UPDATE empresa_user SET
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
        ':complemento'       => $data['complemento'] ?? null,
        ':ie_rg'             => $data['ie_rg'],
        ':cpf_cnpj'          => $data['cpf_cnpj'],
        ':numero_serie_nfe'  => $data['numero_serie_nfe'],
        ':ultimo_numero_nfe' => $data['ultimo_numero_nfe'],
        ':certificado'       => $data['certificado'] ?? '',
        ':senha'             => $data['senha'],
        ':ambiente'          => $data['ambiente'],
        ':cidade_id'         => $data['cidade_id'],
        ':id'                => $id,
    ]);
}


    /**
     * Atualiza o cadastro em empresa_cadastrada (pelo CNPJ)
     *
     * @param string $cnpj
     * @param array  $data
     * @return bool
     */
    public function updateCadastro(string $cnpj, array $data): bool
    {
        $sql = "
            UPDATE empresa_cadastrada SET
                razao_social       = :razao_social,
                nome_fantasia      = :nome_fantasia,
                rua                = :rua,
                numero             = :numero,
                bairro             = :bairro,
                cep                = :cep,
                telefone           = :telefone,
                complemento        = :complemento,
                ie_rg              = :ie_rg,
                cpf_cnpj           = :cpf_cnpj,
                numero_serie_nfe   = :numero_serie_nfe,
                ultimo_numero_nfe  = :ultimo_numero_nfe,
                certificado        = :certificado,
                ambiente           = :ambiente,
                cidade_id          = :cidade_id
            WHERE empresa_cnpj = :empresa_cnpj
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
            ':complemento'       => $data['complemento'] ?? null,
            ':ie_rg'             => $data['ie_rg'],
            ':cpf_cnpj'          => $data['cpf_cnpj'],
            ':numero_serie_nfe'  => $data['numero_serie_nfe'],
            ':ultimo_numero_nfe' => $data['ultimo_numero_nfe'],
            ':certificado'       => $data['certificado'] ?? '',
            ':ambiente'          => $data['ambiente'],
            ':cidade_id'         => $data['cidade_id'],
            ':empresa_cnpj'      => $cnpj,
        ]);
    }

    /**
     * Remove o usuário e o cadastro correspondente
     *
     * @param int    $userId
     * @param string $cnpj
     * @return bool
     */
     
     public function deleteCadastroById(int $id): bool
{
    $stmt = $this->db->prepare("DELETE FROM empresa_cadastrada WHERE id = :id LIMIT 1");
    return $stmt->execute([':id' => $id]);
}
    public function deleteUserAndCadastro(int $userId, string $cnpj): bool
    {
        // 1) Remove cadastro
        $this->db
            ->prepare("DELETE FROM empresa_cadastrada WHERE empresa_cnpj = ?")
            ->execute([$cnpj]);

        // 2) Remove usuário
        return $this->db
            ->prepare("DELETE FROM empresa_user WHERE id = ?")
            ->execute([$userId]);
    }
}
