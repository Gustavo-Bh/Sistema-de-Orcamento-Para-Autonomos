<?php
namespace App\Models;
use App\Config\Database;
use PDO;

class OrcamentoItem
{
    /** @var PDO */
    private $db;

    /** Nome da tabela */
    protected $table = 'orcamento_itens';

    public function __construct()
    {
        // Database.php deve ter sido incluído antes deste model
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os itens de um determinado orçamento
     *
     * @param int $orcamentoId
     * @return array
     */
    public function getAllByOrcamento(int $orcamentoId): array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                id, orcamento_id, tipo_item, item_id,
                descricao, quantidade, valor_unit, valor_total
             FROM {$this->table}
             WHERE orcamento_id = ?"
        );
        $stmt->execute([$orcamentoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna um item por ID
     *
     * @param int $id
     * @return array|null
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT 
                id, orcamento_id, tipo_item, item_id,
                descricao, quantidade, valor_unit, valor_total
             FROM {$this->table}
             WHERE id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Insere um novo item
     *
     * @param array $data
     * @return int último ID inserido
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
     * Atualiza um item existente
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $sets = [];
        foreach ($data as $field => $value) {
            $sets[] = "`$field` = ?";
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
     * Exclui um item pelo ID
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

    /**
     * Exclui todos os itens de um determinado orçamento
     *
     * @param int $orcamentoId
     * @return bool
     */
    public function deleteByOrcamento(int $orcamentoId): bool
    {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE orcamento_id = ?"
        );
        return $stmt->execute([$orcamentoId]);
    }
}
