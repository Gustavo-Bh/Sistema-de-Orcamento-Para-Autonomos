<?php
// Public/orcamento/OrcamentoAplicativo.php

// em desenvolvimento, exibe erros; remova em produção
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Só envia JSON quando NÃO for geração de PDF:
$isPdf = ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pdf'], $_GET['id']));
if (!$isPdf) {
    header('Content-Type: application/json; charset=utf-8');
}

// modelos
require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Cliente.php';
require __DIR__ . '/../../app/Models/Empresa.php';
require __DIR__ . '/../../app/Models/Produto.php';
require __DIR__ . '/../../app/Models/Orcamento.php';
require __DIR__ . '/../../app/Models/OrcamentoItem.php';

use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Produto;
use App\Models\Orcamento;
use App\Models\OrcamentoItem;

$clienteModel = new Cliente();
$empresaModel = new Empresa();
$produtoModel = new Produto();
$orcModel     = new Orcamento();
$itemModel    = new OrcamentoItem();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

  // ==== PDF ou LISTAGEM ====
  case 'GET':
    // geração de PDF
    if (isset($_GET['pdf'], $_GET['id'])) {
      $id = (int) $_GET['id'];

      $orc = $orcModel->getById($id);
      if (!$orc) {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Orçamento não encontrado']);
        exit;
      }

      $items   = $itemModel->getAllByOrcamento($id);
      $cliente = $clienteModel->getById($orc['cliente_id']);
      $empresa = $empresaModel->getById($orc['empresa_id']);

      // (opcional) em PDF, é melhor não exibir erros para não corromper o arquivo
      // ini_set('display_errors', 0);

      // TCPDF
      $tcpdfPath = __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';
      if (!is_file($tcpdfPath)) {
        http_response_code(500);
        echo 'TCPDF não encontrado em ' . $tcpdfPath;
        exit;
      }
      require $tcpdfPath;

      $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');
      $pdf->setPrintHeader(false);
      $pdf->setPrintFooter(false);
      $pdf->SetMargins(10, 10, 10);
      $pdf->SetAutoPageBreak(TRUE, 10);
      $pdf->SetFont('helvetica','',10);
      $pdf->AddPage();

      // formata datas
      $dataEmissao = date('d/m/Y', strtotime($orc['data_emissao']));
      $dataGeracao = date('d/m/Y H:i:s');

      // monta HTML (baseado no layout do index.php)
      ob_start(); // apenas para facilitar concatenação grande
      ?>
      <style>
        .header { border:1px solid #000; padding:6px; text-align:center; }
        .header-title { font-size:14pt; font-weight:bold; }
        .header-company { font-size:8pt; margin-top:3px; }
        .info-row { border:1px solid #000; margin-top:6px; overflow:hidden; }
        .info-left { width:50%; float:left; padding:4px; }
        .info-right { width:50%; float:right; padding:4px; }
        .section { background:#eee; border:1px solid #000; padding:4px; font-weight:bold; text-align:center; margin-top:8px; }
        .data-row { border:1px solid #000; padding:4px; font-size:8pt; }
        .items-table { border-collapse:collapse; width:100%; margin-top:4px; }
        .items-table th, .items-table td { border:1px solid #000; padding:4px; font-size:8pt; }
        .items-table th { background:#ddd; }
        .summary { border:1px solid #000; padding:6px; width:40%; float:right; font-size:8pt; margin-top:6px; }
        .summary b { display:block; text-align:right; margin-top:4px; }
        .observations { clear:both; border:1px solid #000; padding:6px; font-size:8pt; margin-top:10px; }
        .sign { margin-top:30px; overflow:hidden; }
        .sign .line { border-bottom:1px solid #000; width:45%; float:left; margin-bottom:4px; }
        .sign .label { text-align:center; font-size:8pt; width:45%; float:left; }
        .footer { clear:both; text-align:center; font-size:7pt; margin-top:10px; color:#555; }
      </style>

      <div class="header">
        <div class="header-title">ORÇAMENTO</div>
        <div class="header-company"><?= strtoupper($empresa['razao_social']) ?> – CNPJ: <?= $empresa['cpf_cnpj'] ?></div>
        <div class="header-company">
          <?= $empresa['rua'] ?>, <?= $empresa['numero'] ?> – <?= $empresa['bairro'] ?>
          | CEP: <?= $empresa['cep'] ?> | Tel: <?= $empresa['telefone'] ?>
        </div>
      </div>

      <div class="info-row clearfix">
        <div class="info-left">
          <strong>ORÇAMENTO Nº:</strong> <?= $id ?><br>
          <strong>DATA DE EMISSÃO:</strong> <?= $dataEmissao ?>
        </div>
        <div class="info-right">
          <strong>VALIDADE:</strong> 30 DIAS
        </div>
      </div>

      <div class="section">DADOS DO CLIENTE</div>
      <div class="data-row"><strong>Nome/Razão Social:</strong> <?= strtoupper($cliente['nome']) ?></div>
      <div class="data-row"><strong>CPF/CNPJ:</strong> <?= $cliente['cpf_cnpj'] ?></div>
      <div class="data-row">
        <strong>Endereço:</strong>
        <?= strtoupper($cliente['rua'] . ', ' . $cliente['numero'] . ' – ' . $cliente['bairro']) ?>
      </div>

      <div class="section">DESCRIÇÃO DOS SERVIÇOS / PRODUTOS</div>
      <table class="items-table">
        <thead>
          <tr>
            <th width="8%">ITEM</th>
            <th width="44%">DESCRIÇÃO</th>
            <th width="12%">TIPO</th>
            <th width="12%">QTDE</th>
            <th width="12%">V. UNIT.</th>
            <th width="12%">TOTAL</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $i => $it):
          // descrição
          if (($it['tipo_item'] ?? '') === 'produto') {
            $p = $produtoModel->getById($it['item_id']);
            $desc = $p['nome'] ?? '—';
          } else {
            $desc = $it['descricao'] ?? '';
          }
          // valores
          $qtd = (int)($it['quantidade'] ?? 0);
          $vUnit = (float)($it['valor_unit'] ?? $it['valor_unitario'] ?? 0);
          $vTotal = $qtd * $vUnit;
        ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= strtoupper($desc) ?></td>
            <td><?= strtoupper($it['tipo_item']) ?></td>
            <td><?= $qtd ?></td>
            <td>R$ <?= number_format($vUnit, 2, ',', '.') ?></td>
            <td>R$ <?= number_format($vTotal, 2, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <div class="summary">
        Subtotal Produtos: R$ <?= number_format($orc['total_produtos'], 2, ',', '.') ?>
        <b>Subtotal Serviços:</b> R$ <?= number_format($orc['total_servicos'], 2, ',', '.') ?>
        <b>TOTAL GERAL: R$ <?= number_format($orc['total_geral'], 2, ',', '.') ?></b>
      </div>

      <div class="observations">
        <strong>OBSERVAÇÕES E CONDIÇÕES</strong><br>
        • Forma de pagamento: Conforme acordado entre as partes.<br>
        • Os preços estão sujeitos a alteração sem aviso prévio após o vencimento.
      </div>

      <div class="sign">
        <div class="line"></div>
        <div class="label">Responsável pela Empresa</div>
        <div style="clear:both;"></div>
        <div class="line"></div>
        <div class="label">Cliente</div>
      </div>

      <div class="footer">
        Documento gerado em: <?= $dataGeracao ?>
      </div>
      <?php
      $html = ob_get_clean();

      $pdf->writeHTML($html, true, false, true, false, '');

      // Força download quando ?dl=1, senão abre inline
      $mode = (!empty($_GET['dl']) && $_GET['dl'] == '1') ? 'D' : 'I';

      // limpa qualquer saída prévia que possa quebrar o PDF
      if (function_exists('ob_get_length') && ob_get_length()) { @ob_end_clean(); }

      $pdf->Output("orcamento_{$id}.pdf", $mode);
      exit;
    }

    // filtra por CNPJ
    if (!empty($_GET['cnpj'])) {
      $cnpj = preg_replace('/\D+/', '', $_GET['cnpj']);
      $emp  = $empresaModel->getByCnpj($cnpj);
      if (!$emp) { http_response_code(404); echo json_encode(['error'=>'Empresa não encontrada']); exit; }
      $lista = $orcModel->getAllByEmpresaId($emp['id']);
      echo json_encode($lista);
      exit;
    }

    // lista tudo
    echo json_encode($orcModel->getAll());
    exit;

  // ==== CRIAR OU RECUPERAR POR ID ====
  case 'POST':
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true) ?: [];

    // retorna por ID
    if (isset($body['id']) && count($body) === 1) {
      $id  = (int)$body['id'];
      $orc = $orcModel->getById($id);
      if (!$orc) { http_response_code(404); echo json_encode(['error'=>'Orçamento não encontrado']); exit; }
      $orc['items'] = $itemModel->getAllByOrcamento($id);
      echo json_encode($orc); exit;
    }

    // empresa por CNPJ (normaliza se vier)
    if (isset($body['empresa_cnpj'])) {
      $cnpj = preg_replace('/\D+/', '', $body['empresa_cnpj']);
      $emp  = $empresaModel->getByCnpj($cnpj);
      if (!$emp) { http_response_code(400); echo json_encode(['error'=>'CNPJ inválido']); exit; }
      $body['empresa_id'] = $emp['id'];
      unset($body['empresa_cnpj']);
    }

    // validações básicas
    $itemsPayload = $body['items'] ?? [];
    unset($body['items']);
    if (empty($body['cliente_id']) || empty($body['data_emissao'])) {
      http_response_code(400); echo json_encode(['error'=>'cliente_id e data_emissao são obrigatórios']); exit;
    }

    // calcula totais
    $totalProdutos = 0; $totalServicos = 0;
    foreach ($itemsPayload as $it) {
      $q = !empty($it['quantidade']) ? (int)$it['quantidade'] : 0;
      $v = $it['valor_unit'] ?? $it['valor_unitario'] ?? 0.0;
      $sub = $q * $v;
      if (($it['tipo_item'] ?? '') === 'produto') $totalProdutos += $sub; else $totalServicos += $sub;
    }
    $body['total_produtos'] = $totalProdutos;
    $body['total_servicos'] = $totalServicos;
    unset($body['total_geral']); // gerado no BD

    // cria orçamento e pega o ID
    $newId = $orcModel->create($body);

    // insere itens (normalizados/whitelist)
    foreach ($itemsPayload as $it) {
      $valorUnit = $it['valor_unit'] ?? $it['valor_unitario'] ?? 0;
      $clean = [
        'orcamento_id' => $newId,
        'tipo_item'    => $it['tipo_item'] ?? 'produto',
        'item_id'      => (int)($it['item_id'] ?? 0),
        'descricao'    => $it['descricao'] ?? '',
        'quantidade'   => (int)($it['quantidade'] ?? 1),
        'valor_unit'   => (float)$valorUnit,
      ];
      $itemModel->create($clean);
    }

    http_response_code(201);
    echo json_encode(['id'=>$newId]); exit;

  // ==== ATUALIZAR ====
  case 'PUT':
    $body = json_decode(file_get_contents('php://input'), true) ?: [];
    $id   = (int)($_GET['id'] ?? 0);

    if (isset($body['empresa_cnpj'])) {
      $cnpj = preg_replace('/\D+/', '', $body['empresa_cnpj']);
      $emp  = $empresaModel->getByCnpj($cnpj);
      if (!$emp) { http_response_code(400); echo json_encode(['error'=>'CNPJ inválido']); exit; }
      $body['empresa_id'] = $emp['id'];
      unset($body['empresa_cnpj']);
    }

    $itemsPayload = $body['items'] ?? [];
    unset($body['items']);

    // recalcula totais no PUT também
    $totalProdutos = 0; $totalServicos = 0;
    foreach ($itemsPayload as $it) {
      $q = !empty($it['quantidade']) ? (int)$it['quantidade'] : 0;
      $v = $it['valor_unit'] ?? $it['valor_unitario'] ?? 0.0;
      $sub = $q * $v;
      if (($it['tipo_item'] ?? '') === 'produto') $totalProdutos += $sub; else $totalServicos += $sub;
    }
    $body['total_produtos'] = $totalProdutos;
    $body['total_servicos'] = $totalServicos;
    unset($body['total_geral']); // não enviar

    $orcModel->update($id, $body);

    $itemModel->deleteByOrcamento($id);
    foreach ($itemsPayload as $it) {
      $valorUnit = $it['valor_unit'] ?? $it['valor_unitario'] ?? 0;
      $clean = [
        'orcamento_id' => $id,
        'tipo_item'    => $it['tipo_item'] ?? 'produto',
        'item_id'      => (int)($it['item_id'] ?? 0),
        'descricao'    => $it['descricao'] ?? '',
        'quantidade'   => (int)($it['quantidade'] ?? 1),
        'valor_unit'   => (float)$valorUnit,
      ];
      $itemModel->create($clean);
    }

    echo json_encode(['updated'=>true]); 
    exit;

  // ==== EXCLUIR ====
  case 'DELETE':
    $id = (int)($_GET['id'] ?? 0);
    $orcModel->delete($id);
    $itemModel->deleteByOrcamento($id);
    echo json_encode(['deleted'=>true]);
    exit;

  default:
    http_response_code(405);
    echo json_encode(['error'=>'Método não permitido']);
    exit;
}
