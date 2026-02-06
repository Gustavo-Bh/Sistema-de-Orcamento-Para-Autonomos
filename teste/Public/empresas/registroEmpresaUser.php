<?php
// registroEmpresaUser.php — sem certificado obrigatório
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Empresa.php';

use App\Config\Database;

header('Content-Type: application/json; charset=utf-8');

try {
    // 1) Lê JSON ou $_POST
    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);
    if (!is_array($data) || empty($data)) {
        $data = $_POST;
    }

    // 2) Campos obrigatórios em empresa_user (removido 'certificado')
    $required = [
        'razao_social',
        'nome_fantasia',
        'rua',
        'numero',
        'bairro',
        'cep',
        'telefone',
        'complemento',
        'ie_rg',
        'cpf_cnpj',
        'numero_serie_nfe',
        'ultimo_numero_nfe',
        // 'certificado',  <-- removido
        'senha',
        'ambiente',
        'cidade_id'
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

    // 3) Conecta e força exceções
    $db = Database::getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4) Insere em empresa_user
    $sql = "
        INSERT INTO empresa_user (
            razao_social, nome_fantasia, rua, numero, bairro,
            cep, telefone, complemento, ie_rg, cpf_cnpj,
            numero_serie_nfe, ultimo_numero_nfe,
            senha, ambiente, cidade_id, created_at
        ) VALUES (
            :razao_social, :nome_fantasia, :rua, :numero, :bairro,
            :cep, :telefone, :complemento, :ie_rg, :cpf_cnpj,
            :numero_serie_nfe, :ultimo_numero_nfe,
            :senha, :ambiente, :cidade_id, :created_at
        )
    ";
    $stmt = $db->prepare($sql);
    $now  = date('Y-m-d H:i:s');
    $stmt->execute([
        ':razao_social'      => $data['razao_social'],
        ':nome_fantasia'     => $data['nome_fantasia'],
        ':rua'               => $data['rua'],
        ':numero'            => $data['numero'],
        ':bairro'            => $data['bairro'],
        ':cep'               => $data['cep'],
        ':telefone'          => $data['telefone'],
        ':complemento'       => $data['complemento'],
        ':ie_rg'             => $data['ie_rg'],
        ':cpf_cnpj'          => $data['cpf_cnpj'],
        ':numero_serie_nfe'  => $data['numero_serie_nfe'],
        ':ultimo_numero_nfe' => $data['ultimo_numero_nfe'],
        ':senha'             => password_hash($data['senha'], PASSWORD_DEFAULT),
        ':ambiente'          => $data['ambiente'],
        ':cidade_id'         => $data['cidade_id'],
        ':created_at'        => $now,
    ]);

    $newId = (int)$db->lastInsertId();
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'user_id' => $newId,
    ]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Falha no banco: '.$e->getMessage(),
    ]);
} catch (\Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ]);
}
