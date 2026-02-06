<?php
// Public/clientes/index.php (API somente, sem HTML)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1) conexão e models
require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Cliente.php';

use App\Models\Cliente;

$cliente = new Cliente();
$method  = $_SERVER['REQUEST_METHOD'];

// 2) Endpoint GET para app via empresa_cnpj
if ($method === 'GET' && isset($_GET['empresa_cnpj'])) {
    header('Content-Type: application/json; charset=utf-8');
    $cnpj = trim((string) $_GET['empresa_cnpj']);
    echo json_encode(
        $cnpj !== ''
            ? $cliente->getAllApp($cnpj)
            : []
    );
    exit;
}

// 3) API JSON (POST, PUT, DELETE)
header('Content-Type: application/json; charset=utf-8');
switch ($method) {
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        // 3.1) Consulta por ID (via POST)
        if (isset($data['id']) && count($data) === 1) {
            $item = $cliente->getById((int)$data['id']);
            if ($item) {
                echo json_encode($item);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Cliente não encontrado']);
            }
        }
        // 3.2) Lista filtrada por empresa_cnpj (via POST somente quando é único parâmetro)
        elseif (isset($data['empresa_cnpj']) && count($data) === 1) {
            echo json_encode(
                $cliente->getAllApp($data['empresa_cnpj'])
            );
        }
        // 3.3) Sem filtro retorna vazio
        elseif (empty($data)) {
            echo json_encode([]);
        }
        // 3.4) Cria novo cliente (quando vier o payload completo)
        else {
            // Override: garante que o campo empresa_cnpj seja sempre '90'
            $data['empresa_cnpj'] = '90';

            $id = $cliente->createApp($data);
            http_response_code(201);
            echo json_encode(['id' => $id]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $id   = (int)($_GET['id'] ?? 0);
        $ok   = $cliente->updateApp($id, $data);
        echo json_encode(['updated' => $ok]);
        break;

    case 'DELETE':
        $id = (int)($_GET['id'] ?? 0);
        $ok = $cliente->delete($id);
        echo json_encode(['deleted' => $ok]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
