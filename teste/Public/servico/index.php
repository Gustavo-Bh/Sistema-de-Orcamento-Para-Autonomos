<?php
// Public/servico/index.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Servico.php';

use App\Models\Servico;
use PDOException;

// CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$servicoModel = new Servico();

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $cnpj = $_GET['cnpj'] ?? '';
            if (empty($cnpj)) {
                throw new InvalidArgumentException('Informe o CNPJ', 400);
            }
            $lista = $servicoModel->getAllByEmpresaCnpj($cnpj);
            echo json_encode($lista);
            break;

        case 'POST':
            $body = json_decode(file_get_contents('php://input'), true) ?: [];
            if (empty($body['empresa_cnpj'])
                || empty($body['descricao'])
                || !isset($body['valor'])
                || !is_numeric($body['valor'])
            ) {
                throw new InvalidArgumentException(
                    'Campos obrigatórios: empresa_cnpj (string), descricao (string), valor (numérico)',
                    400
                );
            }

            $data = [
                'empresa_cnpj' => $body['empresa_cnpj'],
                'descricao'    => $body['descricao'],
                'valor'        => (float)$body['valor'],
                'created_at'   => date('Y-m-d H:i:s')
            ];
            $id = $servicoModel->create($data);
            http_response_code(201);
            echo json_encode(['id' => $id]);
            break;

        case 'PUT':
            $id = (int)($_GET['id'] ?? 0);
            $body = json_decode(file_get_contents('php://input'), true) ?: [];
            if ($id <= 0
                || empty($body['descricao'])
                || !isset($body['valor'])
                || !is_numeric($body['valor'])
            ) {
                throw new InvalidArgumentException('ID (positivo), descricao e valor (numérico) são obrigatórios', 400);
            }
            $data = [
                'descricao' => $body['descricao'],
                'valor'     => (float)$body['valor']
            ];
            $updated = $servicoModel->update($id, $data);
            echo json_encode(['updated' => $updated]);
            break;

        case 'DELETE':
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                throw new InvalidArgumentException('ID inválido', 400);
            }
            $deleted = $servicoModel->delete($id);
            // No content on success
            if ($deleted) {
                http_response_code(204);
                exit;
            }
            echo json_encode(['deleted' => false]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
            break;
    }
} catch (InvalidArgumentException $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno ao processar requisição']);
}
