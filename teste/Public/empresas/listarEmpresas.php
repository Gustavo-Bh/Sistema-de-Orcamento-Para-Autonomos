<?php
// Public/empresa/listarEmpresas.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Empresa.php';

use App\Config\Database;
use App\Models\Empresa;

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new \Exception('Método não permitido, use GET', 405);
    }

    // recebe o empresa_cnpj via query string
    $cnpj = trim((string)($_GET['empresa_cnpj'] ?? ''));
    if ($cnpj === '') {
        throw new \Exception('Parâmetro empresa_cnpj é obrigatório', 400);
    }

    // Conecta e prepara a consulta
    $pdo  = Database::getConnection();
    $sql  = "
        SELECT
            id,
            empresa_cnpj,
            razao_social,
            nome_fantasia,
            rua,
            numero,
            bairro,
            cep,
            telefone,
            complemento,
            ie_rg,
            cpf_cnpj,
            numero_serie_nfe,
            ultimo_numero_nfe,
            certificado,
            ambiente,
            cidade_id,
            created_at
        FROM empresa_cadastrada
        WHERE empresa_cnpj = :cnpj
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':cnpj' => $cnpj]);

    // Busca todas as empresas vinculadas e retorna como JSON
    $empresas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    echo json_encode($empresas);
    exit;

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Erro no banco: ' . $e->getMessage(),
    ]);
} catch (\Exception $e) {
    $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
    http_response_code($code);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
    ]);
}
