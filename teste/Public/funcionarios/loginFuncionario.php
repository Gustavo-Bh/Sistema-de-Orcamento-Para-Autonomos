<?php
// DEBUG: mostra todos os erros na tela
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Sao_Paulo');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
require __DIR__ . '/../../../app/Models/Database.php';
require __DIR__ . '/../../../app/Models/Funcionarios.php';

use App\Models\Funcionario;

// 1) lê JSON
$input        = json_decode(file_get_contents('php://input'), true);
$usernameRaw  = trim($input['username'] ?? '');
$password     = $input['password'] ?? '';


// 2) valida campos obrigatórios
if ($usernameRaw === '' || $password === '' ) {
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'username e password são obrigatórios.']);
    exit;
}

// 3) chama Model
$model = new Funcionario();
$user  = $model->findByUsernameAndCnpj($usernameRaw);

if (!$user) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Usuário não encontrado para este CNPJ.']);
    exit;
}

// 4) verifica senha
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Senha incorreta.']);
    exit;
}

// 5) sucesso
echo json_encode([
    'success'      => true,
    'userId'       => (int)$user['id'],
    'username'     => $user['username']
]);
exit;
