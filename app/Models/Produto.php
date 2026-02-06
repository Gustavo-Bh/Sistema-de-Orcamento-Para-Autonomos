<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class Produto
{
    /** @var PDO */
    private $db;

    /** Nome da tabela no banco */
    private $table = 'produtos';

    public function __construct()
    {
        // A classe Database deve ter sido carregada antes deste model
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os produtos
     *
     * @return array
     */
    public function getAll(): array
    {
        $stmt = $this->db->query("
            SELECT
                id, nome, valor, cfop_int, cfop_ext, ncm,
                categoria_id, codigo_barras, unidade_venda,
                perc_icms, perc_pis, perc_cofins, perc_ipi,
                cst_csosn, cst_pis, cst_cofins, cst_ipi,
                created_at
            FROM {$this->table}
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna um produto pelo ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT
                id, nome, valor, cfop_int, cfop_ext, ncm,
                categoria_id, codigo_barras, unidade_venda,
                perc_icms, perc_pis, perc_cofins, perc_ipi,
                cst_csosn, cst_pis, cst_cofins, cst_ipi,
                created_at
            FROM {$this->table}
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Insere um novo produto
     *
     * @param array $data
     * @return int ID recém-criado
     */
    public function create(array $data): int
    {
        $fields       = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza um produto existente
     *
     * @param int   $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $sets = [];
        foreach ($data as $field => $value) {
            $sets[] = "`{$field}` = ?";
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE id = ?",
            $this->table,
            implode(', ', $sets)
        );
        $stmt = $this->db->prepare($sql);
        $params = array_values($data);
        $params[] = $id;

        return $stmt->execute($params);
    }

    /**
     * Exclui um produto
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
}
