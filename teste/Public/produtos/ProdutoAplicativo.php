<?php
// Public/produtos/ProdutoAplicativo.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Produto.php';

use App\Models\Produto;

$produtoModel = new Produto();
$method       = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json; charset=utf-8');

switch ($method) {
    // ─── LISTAR / FILTRAR POR empresa_cnpj ───────────────────────────────
    case 'GET':
        // lê o query-param que vem do app
        $cnpj = trim((string)($_GET['empresa_cnpj'] ?? ''));
        if ($cnpj === '') {
            echo json_encode($produtoModel->getAll());
        } else {
            echo json_encode($produtoModel->getAllApp($cnpj));
        }
        exit;

    // ─── CRIAR NOVO PRODUTO ───────────────────────────────────────────────
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        if (empty($body['empresa_cnpj'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Campo empresa_cnpj é obrigatório']);
            exit;
        }
        $data = [
            'empresa_cnpj' => $body['empresa_cnpj'],
            'nome'         => $body['nome']       ?? '',
            'valor'        => $body['valor']      ?? 0,
            'created_at'   => date('Y-m-d H:i:s'),
        ];
        $newId = $produtoModel->create($data);
        http_response_code(201);
        echo json_encode(['id' => $newId]);
        exit;

    // ─── ATUALIZAR PRODUTO ────────────────────────────────────────────────
    case 'PUT':
        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $id   = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }
        $ok = $produtoModel->update($id, $body);
        echo json_encode(['updated' => (bool)$ok]);
        exit;

    // ─── EXCLUIR PRODUTO ────────────────────────────────────────────────
    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            exit;
        }
        $ok = $produtoModel->delete($id);
        echo json_encode(['deleted' => (bool)$ok]);
        exit;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        exit;
}
