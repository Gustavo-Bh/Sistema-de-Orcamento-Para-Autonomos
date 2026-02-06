<?php
// cadastroEmpresa.php — versão de debug
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Empresa.php';

use App\Config\Database;
use App\Models\Empresa;

header('Content-Type: application/json; charset=utf-8');

try {
    // 1) Lê JSON ou form-urlencoded
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);
    if (!is_array($data) || empty($data)) {
        $data = $_POST;
    }

    // 2) Valida campos obrigatórios
    if (empty($data['cpf_cnpj']) || empty($data['senha'])) {
        throw new \Exception('Informe o CNPJ e a senha.', 400);
    }

    // 3) Busca o usuário em empresa_user
    $model = new Empresa();
    $user  = $model->getUserByCnpj($data['cpf_cnpj']);
    if (! $user) {
        http_response_code(404);
        echo json_encode(['success'=>false,'error'=>'Usuário não encontrado']);
        exit;
    }

    // 4) Verifica senha
    if (! password_verify($data['senha'], $user['senha'])) {
        http_response_code(401);
        echo json_encode(['success'=>false,'error'=>'Senha inválida']);
        exit;
    }

    // 5) Remove a senha antes de devolver
    unset($user['senha']);

    // 6) Busca o cadastro em empresa_cadastrada pelo mesmo CNPJ
    $cadastro = $model->getCadastroByCnpj($data['cpf_cnpj']);
    if (! $cadastro) {
        // opcional: retornar partial ou erro
        http_response_code(404);
        echo json_encode(['success'=>false,'error'=>'Cadastro da empresa não encontrado']);
        exit;
    }

    // 7) Retorna sucesso com dados do usuário e da empresa
    echo json_encode([
        'success' => true,
        'user'    => $user,
        'empresa' => $cadastro,
    ]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Erro no banco: '.$e->getMessage(),
    ]);
} catch (\Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ]);
}
