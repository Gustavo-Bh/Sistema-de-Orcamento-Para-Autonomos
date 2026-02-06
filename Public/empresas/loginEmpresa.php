<?php
require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Empresa.php';

use App\Models\Empresa;

header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['cpf_cnpj']) || empty($data['senha'])) {
    http_response_code(400);
    echo json_encode(['error'=>'Informe o CNPJ e a senha.']);
    exit;
}

$empresaModel = new Empresa();
$empresa = $empresaModel->getByCnpj($data['cpf_cnpj']);

if (!$empresa) {
    http_response_code(404);
    echo json_encode(['error'=>'Empresa não encontrada']);
    exit;
}

if (password_verify($data['senha'], $empresa['senha'])) {
    unset($empresa['senha']);
    echo json_encode(['success'=>true, 'empresa'=>$empresa]);
} else {
    http_response_code(401);
    echo json_encode(['error'=>'Senha inválida']);
}
