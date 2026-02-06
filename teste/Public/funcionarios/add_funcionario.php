<?php
// DEBUG: mostra todos os erros na tela
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Sao_Paulo');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require __DIR__ . '/../../app/Models/Database.php';
try {
    $conn = Database::getConnection();
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit;  // Interrompe o script se a conexão falhar
}

require __DIR__ . '/../../../app/Models/Funcionarios.php';

use App\Models\Funcionario;

try {
    // 1) Lê JSON (remove BOM) e faz fallback para $_POST
    $raw  = file_get_contents('php://input');
    $raw  = preg_replace('/^\xEF\xBB\xBF/', '', $raw); // Remove BOM se necessário
    $json = json_decode($raw, true);
    if (!is_array($json)) {
        $json = $_POST;
    }

    // 2) Normaliza campos
    $username     = trim((string)($json['username'] ?? ''));
    $password     = (string)($json['password'] ?? '');
    $empresa_cnpj = trim((string)($json['empresa_cnpj'] ?? ''));

    // 3) Validação de campos obrigatórios
    $missing = [];
    foreach (['username', 'password', 'empresa_cnpj'] as $field) {
        if (empty($json[$field])) {  // Corrigido: Verificar se o campo está vazio
            $missing[] = $field;
        }
    }

    if ($missing) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Campos faltando: ' . implode(', ', $missing)]);
        exit;
    }

    // 4) Conexão com o banco de dados
    $pdo = Database::getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 5) Usa a model
    $model = new Funcionario();

    // 6) Verifica se o usuário já existe
    if ($model->findByUsername($username)) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Usuário já existe.']);
        exit;
    }

    // 7) Cria o novo usuário
    $novoId = $model->create($username, $empresa_cnpj, $password);

    // 8) Retorna sucesso com ID do novo usuário
    http_response_code(201);
    echo json_encode([
        'success'       => true,
        'id'            => (int)$novoId,
        'username'      => $username,
        'empresa_cnpj'  => $empresa_cnpj,  // Corrigido para $empresa_cnpj
        'message'       => 'Funcionário cadastrado com sucesso.'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Falha no banco: ' . $e->getMessage(),
        'trace'   => $e->getTraceAsString()  // Exibe o trace completo do erro
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
        'trace'   => $e->getTraceAsString()  // Exibe o trace completo do erro
    ]);
}
