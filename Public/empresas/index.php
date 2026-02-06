<?php

// Public/empresas/index.php

// 1) conexão e model
require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Empresa.php';

use App\Models\Empresa;

$empresa = new Empresa();
$method  = $_SERVER['REQUEST_METHOD'];
$accept  = $_SERVER['HTTP_ACCEPT'] ?? '';

// 2) Interface HTML
if ($method === 'GET' && strpos($accept, 'application/json') === false):
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Empresas - Ponto Eletricistas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    :root {
      --primary-color: #10b981;
      --primary-hover: #059669;
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
      overflow-x: hidden;
      -webkit-overflow-scrolling: touch;
    }
    
    .table {
      margin: 0;
      min-width: 800px; /* Garante largura mínima */
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
    
    .company-info {
      line-height: 1.4;
    }
    
    .company-name {
      font-weight: 600;
      color: #1e293b;
      display: block;
    }
    
    .company-fantasy {
      color: #6b7280;
      font-size: 0.9rem;
    }
    
    .address-info {
      font-size: 0.85rem;
      color: #6b7280;
      line-height: 1.3;
    }
    
    .contact-info {
      font-size: 0.9rem;
      color: #374151;
    }
    
    .doc-info {
      font-family: 'Courier New', monospace;
      font-size: 0.85rem;
      background: #f8fafc;
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      color: #374151;
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
        font-size: 0.5rem;
        min-width: 700px;
      }
      
      .table thead th {
        padding: 0.75rem 0.5rem;
        font-size: 0.875rem;
      }
      
      .table tbody td {
        padding: 0.75rem 0.5rem;
      }
      
      .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
      }
      
      .btn-action {
        width: 32px;
        height: 32px;
      }
      
      
      .company-fantasy {
        font-size: 0.8rem;
      }
      
      .address-info {
        font-size: 0.75rem;
      }
      
      .contact-info {
        font-size: 0.8rem;
      }
      
      .doc-info {
        font-size: 0.75rem;
        padding: 0.2rem 0.4rem;
      }
    }
    @media (max-width: 768px) {
  .company-name {
    font-size: 1rem !important;
  }
      
}
/* Ajuste de tabela apenas no mobile */
@media (max-width: 768px) {
  .table-responsive {
    overflow-x: hidden !important; /* tira o scroll */
  }

  .table {
    width: 100% !important;
    min-width: auto !important; /* derruba a largura mínima que causava scroll */
    font-size: 0.75rem;          /* fonte menor */
  }

  .table thead th,
  .table tbody td {
    padding: 0.45rem 0.6rem;     /* células mais compactas */
    white-space: normal;         /* permite quebra de linha */
  }

  .company-name {
    font-size: 1rem;
  }

  .company-fantasy,
  .address-info,
  .contact-info,
  .doc-info {
    font-size: 0.7rem;
  }

  .btn-action {
    width: 28px;
    height: 28px;
    font-size: 0.75rem;
  }

  .id-badge {
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
  }
}

    
    @media (max-width: 576px) {
      .table {
        min-width: 600px;
      }
      table tbody{
          width: 200px ;
      }
      
      .table thead th,
      .table tbody td {
        padding: 0.5rem 0.25rem;
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
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
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
    
    .back-button {
      position: fixed;
      bottom: 2rem;
      right: 2rem;
      background: white;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 0.75rem 1rem;
      color: #6b7280;
      text-decoration: none;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: all 0.2s;
      z-index: 1000;
    }
    
    .back-button:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      color: #374151;
      text-decoration: none;
    }
    
    .total-info {
      color: #6b7280;
      font-size: 0.875rem;
      padding: 1rem 1.5rem;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
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
    
    @media (max-width: 768px) {
      .scroll-hint {
        display: block;
      }
    }
  </style>
</head>
<body>
  <div class="main-container">
    <?php include '../../header.php'; ?>
    <!-- Header -->
    <div class="header-card">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="page-title">
          <h1>
            <div class="icon-box">
            <i class="fa-solid fa-building" style="color: #ffffff;"></i>
            </div>
            Empresas
          </h1>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#empresaModal" onclick="openNewModal()">
          <i class="bi bi-plus-lg me-2"></i>Nova Empresa
        </button>
      </div>
    </div>

    <!-- Empresas Table -->
    <div class="table-container">
      <div class="scroll-hint">
        <i class="bi bi-arrow-left-right me-1"></i>
        Deslize horizontalmente para ver mais informações
      </div>
      <div class="table-responsive">
        <table class="table" id="emp-table">
          <thead>
            <tr>
              <!--<th style="width: 3px;">ID</th>-->
              <th style="width: 400px;">Empresa</th>
              <th style="width: 180px;" class="d-none d-md-table-cell">Endereço</th>
              <th style="width: 140px;" class="d-none d-lg-table-cell">Telefone</th>
              <th style="width: 200px;" class="d-none d-lg-table-cell">CPF/CNPJ</th>
              <!--<th style="width: 100px;" class="d-none d-md-table-cell">Ambiente</th>-->
              <th style="width: 100px;">Ações</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <div class="total-info">
        <span id="total-empresas">Total: 0 empresa(s)</span>
      </div>
    </div>
  </div>

  <!-- Empresa Modal -->
  <div class="modal fade" id="empresaModal" tabindex="-1" aria-labelledby="empresaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="empresaModalLabel">
            <i class="bi bi-plus-circle"></i>
            Nova Empresa
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="empresa-form">
            <input type="hidden" id="empresa-id" name="empresa-id">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="razao_social" class="form-label">Razão Social *</label>
                <input type="text" class="form-control" id="razao_social" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="nome_fantasia" class="form-label">Nome Fantasia *</label>
                <input type="text" class="form-control" id="nome_fantasia" required>
              </div>
            </div>
            <div class="row">
              <div class="col-md-8 mb-3">
                <label for="rua" class="form-label">Rua</label>
                <input type="text" class="form-control" id="rua">
              </div>
              <div class="col-md-4 mb-3">
                <label for="numero" class="form-label">Número</label>
                <input type="text" class="form-control" id="numero">
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" class="form-control" id="bairro">
              </div>
              <div class="col-md-4 mb-3">
                <label for="cep" class="form-label">CEP</label>
                <input type="text" class="form-control" id="cep">
              </div>
              <div class="col-md-4 mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="complemento" class="form-label">Complemento</label>
                <input type="text" class="form-control" id="complemento">
              </div>
              <div class="col-md-6 mb-3">
                <label for="ie_rg" class="form-label">IE / RG</label>
                <input type="text" class="form-control" id="ie_rg">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="cpf_cnpj" class="form-label">CPF / CNPJ</label>
                <input type="text" class="form-control" id="cpf_cnpj">
              </div>
              <!--<div class="col-md-6 mb-3">-->
              <!--  <label for="cidade_id" class="form-label">Cidade ID</label>-->
              <!--  <input type="number" class="form-control" id="cidade_id">-->
              <!--</div>-->
            </div>
            <!--<div class="row">-->
              <!--<div class="col-md-6 mb-3">-->
              <!--  <label for="numero_serie_nfe" class="form-label">Nº Série NFE</label>-->
              <!--  <input type="text" class="form-control" id="numero_serie_nfe">-->
              <!--</div>-->
              <!--<div class="col-md-6 mb-3">-->
              <!--  <label for="ultimo_numero_nfe" class="form-label">Último Nº NFE</label>-->
              <!--  <input type="number" class="form-control" id="ultimo_numero_nfe" value="0">-->
              <!--</div>-->
            <!--</div>-->
            <!--<div class="row">-->
              <!--<div class="col-md-6 mb-3">-->
              <!--  <label for="ambiente" class="form-label">Ambiente</label>-->
              <!--  <select class="form-select" id="ambiente">-->
              <!--    <option value="homologacao">Homologação</option>-->
              <!--    <option value="producao">Produção</option>-->
              <!--  </select>-->
              <!--</div>-->
            <!--  <div class="col-md-6 mb-3">-->
            <!--    <label for="senha" class="form-label">Senha</label>-->
            <!--    <input type="text" class="form-control" id="senha">-->
            <!--  </div>-->
            <!--</div>-->
        <!--    <div class="mb-3">-->
        <!--      <label for="certificado" class="form-label">Certificado</label>-->
        <!--      <textarea class="form-control" id="certificado" rows="3"></textarea>-->
        <!--    </div>-->
        <!--  </form>-->
        <!--</div>-->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-2"></i>Cancelar
          </button>
          <button type="button" class="btn btn-primary-custom" onclick="saveEmpresa()">
            <i class="bi bi-check-lg me-2"></i>Salvar
          </button>
        </div>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const apiUrl = '';
    const tableBody = document.querySelector('#emp-table tbody');
    const form = document.getElementById('empresa-form');
    const modalTitle = document.getElementById('empresaModalLabel');
    const totalEmpresas = document.getElementById('total-empresas');
    const modal = new bootstrap.Modal(document.getElementById('empresaModal'));

    const fields = [
      'razao_social','nome_fantasia','rua','numero','bairro','cep',
      'telefone','complemento','ie_rg','cpf_cnpj','numero_serie_nfe'
    ];
    //  <td class="d-none d-md-table-cell">
    //         <span class="badge ${e.ambiente === 'producao' ? 'bg-success' : 'bg-warning'}">
    //           ${e.ambiente === 'producao' ? 'Prod.' : 'Homol.'}
    //         </span>
    //       </td>
//  <td><span class="id-badge">${e.id}</span></td>
    async function loadEmpresas() {
      const res = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Accept':'application/json',
          'Content-Type':'application/json'
        },
        body: JSON.stringify({})
      });
      const data = await res.json();
      tableBody.innerHTML = '';

      data.forEach(e => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
         
          <td>
            <div class="company-info">
              <span class="company-name">${e.razao_social}</span>
              <span class="company-fantasy">${e.nome_fantasia}</span>
            </div>
          </td>
          <td class="d-none d-md-table-cell">
            <div class="address-info">
              ${e.rua ? e.rua + ', ' + (e.numero || '') : '-'}<br>
              ${e.bairro || ''} ${e.cep ? '- ' + e.cep : ''}
            </div>
          </td>
          <td class="d-none d-lg-table-cell">
            <span class="contact-info">${e.telefone || '-'}</span>
          </td>
          <td class="d-none d-lg-table-cell">
            <span class="doc-info">${e.cpf_cnpj || '-'}</span>
          </td>
          <td>
            <div class="action-buttons">
              <button class="btn-action btn-edit" onclick="editEmpresa(${e.id})" title="Editar">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn-action btn-delete" onclick="deleteEmpresa(${e.id})" title="Excluir">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });

      totalEmpresas.textContent = `Total: ${data.length} empresa(s)`;
    }

    function openNewModal() {
      resetForm();
      modalTitle.innerHTML = '<i class="bi bi-plus-circle"></i> Nova Empresa';
    }

    function resetForm() {
      form.reset();
      form['empresa-id'].value = '';
    }

    async function editEmpresa(id) {
      const res = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Accept':'application/json',
          'Content-Type':'application/json'
        },
        body: JSON.stringify({ id })
      });
      const e = await res.json();

      form['empresa-id'].value = e.id;
    fields.forEach(f => {
  if (form[f]) form[f].value = e[f] ?? '';
});


      modalTitle.innerHTML = `<i class="bi bi-pencil"></i> Editando Empresa #${id}`;
      
      modal.show();
    }

    async function deleteEmpresa(id) {
      if (!confirm('Excluir empresa #' + id + '?')) return;
      await fetch(`${apiUrl}?id=${id}`, { method: 'DELETE' });
      loadEmpresas();
    }

    async function saveEmpresa() {
      const id = form['empresa-id'].value;
      const body = {};
   fields.forEach(f => {
  if (form[f]) body[f] = form[f].value;
});


      const opts = {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify(body)
      };

      const url = id ? `${apiUrl}?id=${id}` : apiUrl;
      await fetch(url, opts);
      modal.hide();
      loadEmpresas();
    }

    // Reset form when modal is closed
    document.getElementById('empresaModal').addEventListener('hidden.bs.modal', resetForm);

    loadEmpresas();
    
    // Função para buscar endereço pelo CEP via API ViaCEP
async function buscarCep(cep) {
  cep = cep.replace(/\D/g, ''); // remove tudo que não for número
  if (cep.length !== 8) return;

  try {
    const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
    const data = await res.json();

    if (data.erro) {
      alert('CEP não encontrado.');
      return;
    }

    // Preenche os campos
    document.getElementById('rua').value = data.logradouro || '';
    document.getElementById('bairro').value = data.bairro || '';
    document.getElementById('complemento').value = data.complemento || '';
    // Caso queira futuramente usar cidade/UF:
    // document.getElementById('cidade').value = data.localidade;
    // document.getElementById('uf').value = data.uf;

  } catch (error) {
    console.error('Erro ao buscar CEP:', error);
    alert('Erro ao consultar o CEP. Tente novamente.');
  }
}

// Dispara quando o usuário sair do campo CEP
document.getElementById('cep').addEventListener('blur', (e) => {
  buscarCep(e.target.value);
});
    
    
  </script>
</body>
</html>

<?php
  exit;
endif;

// 3) API JSON
header('Content-Type: application/json; charset=utf-8');

switch ($method) {
  case 'POST':
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($data)) {
      // POST vazio → lista
      echo json_encode($empresa->getAll());
    } elseif (isset($data['id']) && count($data) === 1) {
      // POST { id } → getById
      $item = $empresa->getById((int)$data['id']);
      if ($item) echo json_encode($item);
      else {
        http_response_code(404);
        echo json_encode(['error'=>'Empresa não encontrada']);
      }
    } else {
      // POST com dados → create
      $id = $empresa->create($data);
      http_response_code(201);
      echo json_encode(['id'=>$id]);
    }
    break;

  case 'PUT':
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $id   = (int)($_GET['id'] ?? 0);
    $ok   = $empresa->update($id, $data);
    echo json_encode(['updated'=>$ok]);
    break;

  case 'DELETE':
    $id = (int)($_GET['id'] ?? 0);
    $ok = $empresa->delete($id);
    echo json_encode(['deleted'=>$ok]);
    break;

  default:
    http_response_code(405);
    echo json_encode(['error'=>'Método não permitido']);
    break;
}
?>

