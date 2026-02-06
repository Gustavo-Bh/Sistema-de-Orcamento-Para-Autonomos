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
    $method = $_SERVER['REQUEST_METHOD'];

    // ------------------------------------------------------
    // DELETE por id OU por (user_id + empresa_cnpj)
    // Ex.: DELETE .../cadastroEmpresa.php?id=123
    //  ou  DELETE .../cadastroEmpresa.php?user_id=1&empresa_cnpj=00000000000191
    //  ou  Body JSON equivalente
    // ------------------------------------------------------
    if ($method === 'DELETE') {
        // Lê JSON do body; se vazio, usa query string
        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true) ?: [];
        $qs   = $_GET ?? [];

        $id          = isset($data['id']) ? (int)$data['id'] : (int)($qs['id'] ?? 0);
        $userId      = isset($data['user_id']) ? (int)$data['user_id'] : (int)($qs['user_id'] ?? 0);
        $empresaCnpj = $data['empresa_cnpj'] ?? ($qs['empresa_cnpj'] ?? '');
        $empresaCnpj = preg_replace('/\D+/', '', (string)$empresaCnpj);

        $db = Database::getConnection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $model = new Empresa();

        // Prioriza exclusão por ID (casa com o app Android)
        if ($id > 0) {
            $ok = $model->deleteCadastroById($id); // empresa_cadastrada.id
            http_response_code($ok ? 200 : 404);
            echo json_encode(['deleted' => (bool)$ok, 'success' => (bool)$ok]);
            exit;
        }

        // Alternativa por (user_id + empresa_cnpj)
        if ($userId > 0 && $empresaCnpj !== '') {
            $ok = $model->deleteUserAndCadastro($userId, $empresaCnpj);
            http_response_code($ok ? 200 : 404);
            echo json_encode(['deleted' => (bool)$ok, 'success' => (bool)$ok]);
            exit;
        }

        http_response_code(400);
        echo json_encode([
            'deleted' => false,
            'success' => false,
            'error'   => 'Informe id OU (user_id e empresa_cnpj).'
        ]);
        exit;
    }

    // ------------------------------------------------------
    // POST — cria empresa_user + empresa_cadastrada
    // ------------------------------------------------------
    if ($method === 'POST') {
        // 1) Lê JSON ou $_POST
        $input = file_get_contents('php://input');
        $data  = json_decode($input, true);
        if (!is_array($data) || empty($data)) {
            $data = $_POST;
        }

        // 2) Validação
        $required = [
            'razao_social','nome_fantasia','rua','numero','bairro','cep','telefone',
            'complemento','ie_rg','cpf_cnpj','empresa_cnpj',
            'numero_serie_nfe','ultimo_numero_nfe','certificado','senha',
            'ambiente','cidade_id'
        ];
        $missing = [];
        foreach ($required as $f) {
            if (!isset($data[$f]) || trim((string)$data[$f]) === '') {
                $missing[] = $f;
            }
        }
        if ($missing) {
            throw new \Exception('Campos faltando: '.implode(', ', $missing));
        }

        // 3) Conecta e força exceptions no PDO
        $db = Database::getConnection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 4) Executa o cadastro via Model
        $model     = new Empresa();
        $newUserId = $model->createUserAndCadastro($data);

        // 5) Retorna sucesso
        http_response_code(201);
        echo json_encode(['success' => true, 'user_id' => $newUserId]);
        exit;
    }

    // ------------------------------------------------------
    // Outros métodos não permitidos
    // ------------------------------------------------------
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error'   => 'Método não permitido. Use POST (criar) ou DELETE (excluir).'
    ]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'type'    => 'PDOException',
        'error'   => $e->getMessage(),
        'trace'   => $e->getTraceAsString(),
    ]);
} catch (\Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'type'    => 'Exception',
        'error'   => $e->getMessage(),
    ]);
}
