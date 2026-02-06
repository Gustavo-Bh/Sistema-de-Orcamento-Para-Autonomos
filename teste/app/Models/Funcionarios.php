<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class Funcionario
{
    private PDO $db;

    public function __construct()
    {
        // Tenta estabelecer a conexão PDO
        try {
            $this->db = Database::getConnection();  // Conexão do banco
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Habilitar exceções no PDO
            // Se a conexão for bem-sucedida, podemos continuar
        } catch (PDOException $e) {
            // Se falhar, lançar exceção
            throw new \RuntimeException("Falha na conexão com o banco: " . $e->getMessage());
        }
    }

    // Método para buscar um usuário pelo username
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

    // Método para login com username e empresa_cnpj
    public function findByUsernameAndCnpj(string $username, string $empresaCnpj): ?array
    {
        $sql  = "SELECT id, username, password
                 FROM users
                 WHERE username = ? AND empresa_cnpj = ?
                 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $empresaCnpj]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Criação de um novo usuário
 public function create(string $username, string $empresaCnpj, string $passwordPlain): int
{
    if (!$this->db instanceof PDO) {
        throw new \RuntimeException('Sem conexão com o banco (PDO nulo).');
    }

    // Garante que veremos exceções
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criptografando a senha
    $hash = password_hash($passwordPlain, PASSWORD_DEFAULT);

    // SQL de inserção
    $sql = "INSERT INTO users (username, empresa_cnpj, password)
            VALUES (:username, :empresa_cnpj, :password)";

    // Preparação da consulta
    $stmt = $this->db->prepare($sql);

    try {
        $stmt->bindValue(':username',     $username,    PDO::PARAM_STR);
        $stmt->bindValue(':empresa_cnpj', $empresaCnpj, PDO::PARAM_STR);
        $stmt->bindValue(':password',     $hash,        PDO::PARAM_STR);

        // Execução da inserção
        $stmt->execute();
    } catch (PDOException $e) {
        // Captura e exibe um erro caso a execução falhe
        throw new \RuntimeException("Erro ao inserir no banco: " . $e->getMessage());
    }

    // Retorna o ID do novo usuário inserido
    return (int)$this->db->lastInsertId(); // lastInsertId() é string, convertemos para int
}


}
