<?php

// Public/orcamento/index.php

// 1) conexão e models
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
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';

// —— 1) Geração de PDF —— 
if ($method === 'GET' && isset($_GET['pdf'], $_GET['id'])) {
    $id       = (int) $_GET['id'];
    $orc      = $orcModel->getById($id) ?: die('Orçamento não encontrado');
    $items    = $itemModel->getAllByOrcamento($id);
    $cliente  = $clienteModel->getById($orc['cliente_id']);
    $empresa  = $empresaModel->getById($orc['empresa_id']);

    require __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';

    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8');
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor($empresa['nome_fantasia']);
    $pdf->SetTitle("Orçamento #{$id}");
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->AddPage();
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetFont('helvetica','',10);

    // formata datas
    $dataEmissao   = date('d/m/Y', strtotime($orc['data_emissao']));
    $dataGeracao   = date('d/m/Y H:i:s');

    // monta todo o HTML com novo estilo
    $html = '
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
      <div class="header-company">'. strtoupper($empresa['razao_social']) .' – CNPJ: '. $empresa['cpf_cnpj'] .'</div>
      <div class="header-company">'. $empresa['rua'] .', '. $empresa['numero'] .' – '. $empresa['bairro'] .' | CEP: '. $empresa['cep'] .' | Tel: '. $empresa['telefone'] .'</div>
    </div>

    <div class="info-row clearfix">
      <div class="info-left">
        <strong>ORÇAMENTO Nº:</strong> '. $id .'<br>
        <strong>DATA DE EMISSÃO:</strong> '. $dataEmissao .'
      </div>
      <div class="info-right">
        <strong>VALIDADE:</strong> 30 DIAS
      </div>
    </div>

    <div class="section">DADOS DO CLIENTE</div>
    <div class="data-row"><strong>Nome/Razão Social:</strong> '. strtoupper($cliente['nome']) .'</div>
    <div class="data-row"><strong>CPF/CNPJ:</strong> '. $cliente['cpf_cnpj'] .'</div>
    <div class="data-row"><strong>Endereço:</strong> '. strtoupper($cliente['rua'] .', '. $cliente['numero'] .' – '. $cliente['bairro']) .'</div>

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
      <tbody>';
    
    foreach ($items as $i => $it) {
        if ($it['tipo_item'] === 'produto') {
            $p = $produtoModel->getById($it['item_id']);
            $desc = $p['nome'] ?? '—';
        } else {
            $desc = $it['descricao'];
        }
        $html .= '
        <tr>
          <td>'. ($i+1) .'</td>
          <td>'. strtoupper($desc) .'</td>
          <td>'. strtoupper($it['tipo_item']) .'</td>
          <td>'. $it['quantidade'] .'</td>
          <td>R$ '. number_format($it['valor_unit'],2,',','.') .'</td>
          <td>R$ '. number_format($it['valor_total'],2,',','.') .'</td>
        </tr>';
    }

    $html .= '
      </tbody>
    </table>

    <div class="summary">
      Subtotal Produtos: R$ '. number_format($orc['total_produtos'],2,',','.') .'
      <b>Subtotal Serviços:</b> R$ '. number_format($orc['total_servicos'],2,',','.') .'
      <b>TOTAL GERAL: R$ '. number_format($orc['total_geral'],2,',','.') .'</b>
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
      Documento gerado em: '. $dataGeracao .'
    </div>';

    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output("orcamento_{$id}.pdf", 'I');
    exit;
}

// —— 2) Interface HTML + API JSON —— 
if ($method === 'GET' && strpos($accept, 'application/json') === false):
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orçamentos - Ponto Eletricistas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #f59e0b;
      --primary-hover: #d97706;
    }
    
    body {
      background-color: #f8fafc;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .main-container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 1rem;
    }
    
    .header-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      border: 1px solid #e2e8f0;
    }
    
    .page-title h1 {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin: 0;
      font-size: 1.5rem;
      font-weight: 600;
      color: #1e293b;
    }
    
    .icon-box {
      width: 32px;
      height: 32px;
      background: var(--primary-color);
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1rem;
    }
    
    .btn-primary-custom {
      background: var(--primary-color);
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      transition: all 0.2s;
    }
    
    .btn-primary-custom:hover {
      background: var(--primary-hover);
      transform: translateY(-1px);
    }
    
    .table-container {
      background: white;
      border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 1px solid #e2e8f0;
      overflow: hidden;
    }
    
    .table-responsive {
        overflow: hidden;
      overflow-x: none;
      -webkit-overflow-scrolling: touch;
    }
    
    .table {
      margin: 0;
      min-width: 600px;
    }
    
    .table thead th {
      background: #f8fafc;
      border: none;
      padding: 1rem 0.75rem;
      font-weight: 600;
      color: #475569;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.025em;
      white-space: nowrap;
    }
    
    .table tbody td {
      padding: 1rem 0.75rem;
      border-color: #e2e8f0;
      vertical-align: middle;
    }
    
    .table tbody tr:hover {
      background-color: #f8fafc;
    }
    
    .id-badge {
      background: var(--primary-color);
      color: white;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-weight: 500;
      font-size: 0.875rem;
      display: inline-block;
      min-width: 2rem;
      text-align: center;
    }
    
    .action-buttons {
      display: flex;
      gap: 0.5rem;
      justify-content: center;
    }
    
    .btn-action {
      width: 36px;
      height: 36px;
      border-radius: 6px;
      border: 1px solid #e2e8f0;
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      cursor: pointer;
    }
    
    .btn-action:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .btn-pdf {
      color: #10b981;
      border-color: #d1fae5;
    }
    
    .btn-pdf:hover {
      background: #d1fae5;
    }
    
    .btn-edit {
      color: #3b82f6;
      border-color: #dbeafe;
    }
    
    .btn-edit:hover {
      background: #dbeafe;
    }
    
    .btn-delete {
      color: #ef4444;
      border-color: #fecaca;
    }
    
    .btn-delete:hover {
      background: #fecaca;
    }
    
    .scroll-hint {
      display: none;
      color: #6b7280;
      font-size: 0.8rem;
      text-align: center;
      padding: 0.5rem;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
    }
    
    /* Mobile optimizations */
    @media (max-width: 768px) {
      .main-container {
        padding: 0.75rem;
      }
      
      .header-card {
        padding: 1rem;
        margin-bottom: 1rem;
      }
      
      .page-title h1 {
        font-size: 1.25rem;
      }
      
      .btn-primary-custom {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
      }
      
      .table {
        font-size: 0.85rem;
        max-width: 800px;
        
      }
      
      .table thead th {
        padding: 0.75rem 0.5rem;
      }
      
      .table strong{
          font-size:1rem !important;
      }
      
      .table tbody td {
        padding: 0.5rem 0.5rem;
      }
    
      
      .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
      }
      
      .btn-action {
        width: 32px;
        height: 32px;
      }
      
      .scroll-hint {
        display: block;
      }
    }
    @media (max-width: 768px) {
  .table {
    font-size: 0.75rem;
    /* diminui a fonte */
  }
  .table th, 
  .table td {
    padding: 0.4rem;     /* diminui espaçamento */
  }
  .id-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
  }
}

    
    @media (max-width: 576px) {
        
      .table {
        max-width: 100px;
      }
      .table-responsive{
          overflow-x: hidden !important;
    
      }
      
    }
    
    .modal-header {
      border-bottom: 1px solid #e2e8f0;
      padding: 1.5rem;
    }
    
    .modal-title {
      font-weight: 600;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .modal-body {
      padding: 1.5rem;
      max-height: 70vh;
      overflow-y: auto;
    }
    
    .form-label {
      font-weight: 500;
      color: #374151;
      margin-bottom: 0.5rem;
    }
    
    .form-control, .form-select {
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: 0.75rem;
      transition: all 0.2s;
    }
    
    .form-control:focus, .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    
    .btn-secondary-custom {
      background: #6b7280;
      border: none;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      color: white;
      transition: all 0.2s;
    }
    
    .btn-secondary-custom:hover {
      background: #4b5563;
      color: white;
    }
    
    .item-controls {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 1.5rem;
      margin-bottom: 1rem;
    }
    
    .section-title {
      font-size: 1.1rem;
      font-weight: 600;
      color: #1e293b;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .btn-add {
      background: var(--primary-color);
      border: none;
      border-radius: 6px;
      padding: 0.5rem 1rem;
      color: white;
      font-weight: 500;
      transition: all 0.2s;
    }
    
    .btn-add:hover {
      background: var(--primary-hover);
      color: white;
    }
    
    .total-section {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 8px;
      padding: 1.5rem;
      margin-top: 1rem;
    }
    
    .total-info {
      color: #6b7280;
      font-size: 0.875rem;
      padding: 1rem 1.5rem;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
    }
  </style>
</head>
<body>
  <div class="main-container">
    <!-- Header -->
     <?php include '../../header.php'; ?>
    <div class="header-card">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="page-title">
          <h1>
            <div class="icon-box">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            Orçamentos
          </h1>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#orcamentoModal" onclick="openNewModal()">
          <i class="bi bi-plus-lg me-2"></i>Novo Orçamento
        </button>
      </div>
    </div>

    <!-- Orçamentos Table -->
    <div class="table-container">
      <div class="scroll-hint">
        <i class="bi bi-arrow-left-right me-1"></i>
        Deslize horizontalmente para ver mais informações
      </div>
      <div class="table-responsive">
        <table class="table" id="orc-table">
          <thead>
            <tr>
              <!--<th style="width:60px;" >ID</th>-->
              <th  style="width:200px;"   class="d-none d-md-table-cell">Data</th>
              <th style="width:170px;" >Cliente</th>
              <th  style="width:240px;"   class="d-none d-lg-table-cell">Empresa</th>
              <th style="width:120px;" >Total</th>
              <th >Ações</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="total-info">
        <span id="total-orcamentos">Total: 0 orçamento(s)</span>
      </div>
    </div>
  </div>

  <!-- Modal e scripts continuam iguais... -->
  <div class="modal fade" id="orcamentoModal" tabindex="-1" aria-labelledby="orcamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="orcamentoModalLabel">
            <i class="bi bi-plus-circle"></i>
            Novo Orçamento
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="orc-form">
            <input type="hidden" id="orc-id">
            
            <div class="section-title">
              <i class="bi bi-info-circle"></i>
              Dados do Orçamento
            </div>
            
            <div class="row mb-4">
              <div class="col-md-4 mb-3">
                <label for="cliente_id" class="form-label">Cliente *</label>
                <select class="form-select" id="cliente_id" required>
                  <option value="">Selecione um cliente</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="empresa_id" class="form-label">Empresa *</label>
                <select class="form-select" id="empresa_id" required>
                  <option value="">Selecione uma empresa</option>
                </select>
              </div>
              <div class="col-md-4 mb-3">
                <label for="data_emissao" class="form-label">Data de Emissão *</label>
                <input type="date" class="form-control" id="data_emissao" required>
              </div>
            </div>

            <div class="section-title">
              <i class="bi bi-list-ul"></i>
              Itens do Orçamento
            </div>

            <div class="table-container mb-3">
              <table class="table" id="items-table">
                <thead>
                  <tr>
                    <!--<th>Tipo</th>-->
                    <th>Produto/Serviço</th>
                    <th>Qtd</th>
                    <th>Valor Unit.</th>
                    <th>Total</th>
                    <th>Ação</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <!-- Add Product Section -->
            <div class="item-controls">
              <h5 class="mb-3">
                <i class="bi bi-box-seam me-2"></i>Adicionar Produto
              </h5>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="product-select" class="form-label">Produto</label>
                  <select class="form-select" id="product-select">
                    <option value="">-- selecione --</option>
                  </select>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="product-qty" class="form-label">Quantidade</label>
                  <input type="number" class="form-control" id="product-qty" value="1" min="1">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">&nbsp;</label>
                  <button type="button" class="btn btn-add w-100" id="btn-add-product">
                    <i class="bi bi-plus-lg me-2"></i>Adicionar
                  </button>
                </div>
              </div>
            </div>

            <!-- Add Service Section -->
            <div class="item-controls">
              <h5 class="mb-3">
                <i class="bi bi-tools me-2"></i>Adicionar Serviço
              </h5>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="service-desc" class="form-label">Descrição</label>
                  <textarea class="form-control" id="service-desc" rows="2"></textarea>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="service-value" class="form-label">Valor</label>
                  <input type="number" step="0.01" class="form-control" id="service-value" value="0.00">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label">&nbsp;</label>
                  <button type="button" class="btn btn-add w-100" id="btn-add-service">
                    <i class="bi bi-plus-lg me-2"></i>Adicionar
                  </button>
                </div>
              </div>
            </div>

            <!-- Totals Section -->
            <div class="total-section">
              <div class="section-title">
                <i class="bi bi-calculator"></i>
                Resumo Financeiro
              </div>
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label for="total_produtos" class="form-label">Total Produtos</label>
                  <input type="number" step="0.01" class="form-control" id="total_produtos" readonly value="0.00">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="total_servicos" class="form-label">Total Serviços</label>
                  <input type="number" step="0.01" class="form-control" id="total_servicos" readonly value="0.00">
                </div>
                <div class="col-md-4 mb-3">
                  <label for="total_geral" class="form-label">Total Geral</label>
                  <input type="number" step="0.01" class="form-control fw-bold" id="total_geral" readonly value="0.00">
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-2"></i>Cancelar
          </button>
          <button type="button" class="btn btn-primary-custom" onclick="saveOrcamento()">
            <i class="bi bi-check-lg me-2"></i>Salvar Orçamento
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const apiUrl = '';
    const clientesApi = '../clientes/index.php';
    const empresasApi = '../empresas/index.php';
    const produtosApi = '../produtos/index.php';

    const orcTable = document.querySelector('#orc-table tbody');
    const itemsTable = document.querySelector('#items-table tbody');
    const form = document.getElementById('orc-form');
    const modalTitle = document.getElementById('orcamentoModalLabel');
    const totalOrcamentos = document.getElementById('total-orcamentos');
    const modal = new bootstrap.Modal(document.getElementById('orcamentoModal'));
    
    const selClient = document.getElementById('cliente_id');
    const selEmpresa = document.getElementById('empresa_id');
    const selProduct = document.getElementById('product-select');
    const qtyInput = document.getElementById('product-qty');
    const btnAddProd = document.getElementById('btn-add-product');
    const svcDesc = document.getElementById('service-desc');
    const svcValue = document.getElementById('service-value');
    const btnAddSvc = document.getElementById('btn-add-service');
    const totalProd = document.getElementById('total_produtos');
    const totalSvc = document.getElementById('total_servicos');
    const totalAll = document.getElementById('total_geral');

    let clientsMap = {}, empMap = {}, prodMap = {}, items = [];

    async function loadMetadata() {
      // Carrega clientes
      let res = await fetch(clientesApi, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({})
      });
      (await res.json()).forEach(c => {
        clientsMap[c.id] = c.nome;
        selClient.add(new Option(c.nome, c.id));
      });

      // Carrega empresas
      res = await fetch(empresasApi, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({})
      });
      (await res.json()).forEach(e => {
        empMap[e.id] = e.nome_fantasia;
        selEmpresa.add(new Option(e.nome_fantasia, e.id));
      });

      // Carrega produtos
      res = await fetch(produtosApi, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({})
      });
      (await res.json()).forEach(p => {
        prodMap[p.id] = p;
        selProduct.add(new Option(
          `${p.nome} (R$ ${parseFloat(p.valor).toFixed(2)})`,
          p.id
        ));
      });

      loadOrcamentos();
    }
// <td>
//             <span class="badge ${it.tipo_item === 'produto' ? 'bg-primary' : 'bg-success'}">
//               ${it.tipo_item}
//             </span>
//           </td>
    function renderItems() {
      itemsTable.innerHTML = '';
      let sumP = 0, sumS = 0;

      items.forEach((it, i) => {
        const tr = document.createElement('tr');
        const label = it.tipo_item === 'produto'
          ? prodMap[it.item_id].nome
          : it.descricao;

        tr.innerHTML = `
          
          <td><strong>${label}</strong></td>
          <td>${it.quantidade}</td>
          <td>R$ ${parseFloat(it.valor_unit).toFixed(2)}</td>
          <td><strong>R$ ${parseFloat(it.valor_total).toFixed(2)}</strong></td>
          <td>
            <button class="btn-action btn-delete" onclick="removeItem(${i})" title="Remover">
              <i class="bi bi-trash"></i>
            </button>
          </td>
        `;

        itemsTable.appendChild(tr);

        if (it.tipo_item === 'produto') sumP += parseFloat(it.valor_total);
        else sumS += parseFloat(it.valor_total);
      });

      totalProd.value = sumP.toFixed(2);
      totalSvc.value = sumS.toFixed(2);
      totalAll.value = (sumP + sumS).toFixed(2);
    }

    window.removeItem = i => { items.splice(i,1); renderItems(); };

    btnAddProd.addEventListener('click', () => {
      const id = selProduct.value;
      const qt = parseInt(qtyInput.value) || 1;
      if (!id) return alert('Selecione um produto');

      const p = prodMap[id];
      items.push({
        tipo_item: 'produto',
        item_id: id,
        descricao: '',
        quantidade: qt,
        valor_unit: p.valor,
        valor_total: (p.valor * qt).toFixed(2)
      });

      selProduct.value = '';
      qtyInput.value = '1';
      renderItems();
    });

    btnAddSvc.addEventListener('click', () => {
      const desc = svcDesc.value.trim();
      const val = parseFloat(svcValue.value) || 0;
      if (!desc) return alert('Descreva o serviço');

      items.push({
        tipo_item: 'servico',
        item_id: 0,
        descricao: desc,
        quantidade: 1,
        valor_unit: val,
        valor_total: val.toFixed(2)
      });

      svcDesc.value = '';
      svcValue.value = '0.00';
      renderItems();
    });
// <td><span class="id-badge">#${o.id}</span></td>
    async function loadOrcamentos() {
      const res = await fetch(apiUrl, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({})
      });
      const data = await res.json();
      orcTable.innerHTML = '';
      
      data.forEach(o => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          
          <td class="d-none d-md-table-cell">${new Date(o.data_emissao).toLocaleDateString('pt-BR')}</td>
          <td><strong>${clientsMap[o.cliente_id]}</strong></td>
          <td class="d-none d-lg-table-cell">${empMap[o.empresa_id]}</td>
          <td><strong class="text-success">R$ ${parseFloat(o.total_geral).toFixed(2)}</strong></td>
          <td>
            <div class="action-buttons">
              <button class="btn-action btn-pdf" onclick="window.open('?pdf=1&id=${o.id}','_blank')" title="Gerar PDF">
                <i class="bi bi-file-earmark-pdf"></i>
              </button>
              <button class="btn-action btn-edit" onclick="editOrc(${o.id})" title="Editar">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn-action btn-delete" onclick="delOrc(${o.id})" title="Excluir">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        `;
        orcTable.appendChild(tr);
      });

      totalOrcamentos.textContent = `Total: ${data.length} orçamento(s)`;
    }

    function openNewModal() {
      resetForm();
      modalTitle.innerHTML = '<i class="bi bi-plus-circle"></i> Novo Orçamento';
    }

    function resetForm() {
      form.reset();
      items = [];
      renderItems();
      form['data_emissao'].value = new Date().toISOString().slice(0,10);
    }

    window.editOrc = async id => {
      const res = await fetch(apiUrl, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ id })
      });
      const o = await res.json();

      form['orc-id'].value = o.id;
      form['cliente_id'].value = o.cliente_id;
      form['empresa_id'].value = o.empresa_id;
      form['data_emissao'].value = o.data_emissao;

      modalTitle.innerHTML = `<i class="bi bi-pencil"></i> Editando Orçamento #${id}`;

      items = o.items || [];
      renderItems();
      
      modal.show();
    };

    window.delOrc = async id => {
      if (!confirm('Excluir orçamento #' + id + '?')) return;
      await fetch(`${apiUrl}?id=${id}`, { method:'DELETE' });
      loadOrcamentos();
    };

    async function saveOrcamento() {
      const id = form['orc-id'].value;
      const payload = {
        cliente_id: form['cliente_id'].value,
        empresa_id: form['empresa_id'].value,
        data_emissao: form['data_emissao'].value,
        total_produtos: totalProd.value,
        total_servicos: totalSvc.value,
        total_geral: totalAll.value,
        items
      };

      const opts = {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify(payload)
      };

      const url = id ? `${apiUrl}?id=${id}` : apiUrl;
      await fetch(url, opts);
      modal.hide();
      loadOrcamentos();
    }

    // Reset form when modal is closed
    document.getElementById('orcamentoModal').addEventListener('hidden.bs.modal', resetForm);

    loadMetadata();
  </script>
</body>
</html>

<?php
  exit;
endif;

// —— 3) API JSON ——
header('Content-Type: application/json; charset=utf-8');

switch ($method) {
  case 'POST':
    $body = json_decode(file_get_contents('php://input'), true) ?: [];

    // getById + itens
    if (isset($body['id']) && count($body) === 1) {
      $orc = $orcModel->getById((int)$body['id']);
      if (! $orc) {
        http_response_code(404);
        echo json_encode(['error' => 'Orçamento não encontrado']);
        break;
      }
      $orc['items'] = $itemModel->getAllByOrcamento($orc['id']);
      echo json_encode($orc);
      break;
    }

    // lista todos
    if (empty($body)) {
      echo json_encode($orcModel->getAll());
      break;
    }

    // criar
    $itemsPayload = $body['items'] ?? [];
    unset($body['items']);
    $newId = $orcModel->create($body);

    foreach ($itemsPayload as $it) {
      $it['orcamento_id'] = $newId;
      $itemModel->create($it);
    }

    http_response_code(201);
    echo json_encode(['id' => $newId]);
    break;

  case 'PUT':
    $body = json_decode(file_get_contents('php://input'), true) ?: [];
    $id   = (int)($_GET['id'] ?? 0);

    $itemsPayload = $body['items'] ?? [];
    unset($body['items']);

    $orcModel->update($id, $body);
    $itemModel->deleteByOrcamento($id);

    foreach ($itemsPayload as $it) {
      $it['orcamento_id'] = $id;
      $itemModel->create($it);
    }

    echo json_encode(['updated' => true]);
    break;

  case 'DELETE':
    $id = (int)($_GET['id'] ?? 0);
    $orcModel->delete($id);
    $itemModel->deleteByOrcamento($id);
    echo json_encode(['deleted' => true]);
    break;

  default:
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    break;
}
?>
