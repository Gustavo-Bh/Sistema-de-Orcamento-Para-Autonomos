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
        $this->db = Database::getConnection();
    }

    /**
     * Retorna todos os itens de um determinado orçamento
     */
    public function getAllByOrcamento(int $orcamentoId): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                id, orcamento_id, tipo_item, item_id,
                descricao, quantidade,
                valor_unit AS valor_unitario,
                valor_total
             FROM {$this->table}
             WHERE orcamento_id = ?"
        );
        $stmt->execute([$orcamentoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna um item por ID
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT
                id, orcamento_id, tipo_item, item_id,
                descricao, quantidade,
                valor_unit AS valor_unitario,
                valor_total
             FROM {$this->table}
             WHERE id = ?"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Insere um novo item */
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

    /** Atualiza um item existente */
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

    /** Exclui um item pelo ID */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Exclui todos os itens de um determinado orçamento */
    public function deleteByOrcamento(int $orcamentoId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE orcamento_id = ?");
        return $stmt->execute([$orcamentoId]);
    }
}
