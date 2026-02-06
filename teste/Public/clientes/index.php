<?php
// Public/clientes/index.php

// 1) conexão e models
require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Cliente.php';

use App\Models\Cliente;

$cliente = new Cliente();
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
  <title>Clientes - Ponto Eletricistas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #3b82f6;
      --primary-hover: #2563eb;
    }
    
    body {
      background-color: #f8fafc;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .main-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem;
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
      /* overflow: hidden;  remova esta linha */
      overflow-x: auto;             /* adicione esta */
      -webkit-overflow-scrolling: touch; /* para rolagem suave no iOS */
    }
    
    .table thead th {
      background: #f8fafc;
      border: none;
      padding: 1rem;
      font-weight: 600;
      color: #475569;
      font-size: 0.875rem;
      text-transform: uppercase;
      letter-spacing: 0.025em;
    }
    
    .table tbody td {
      padding: 1rem;
      border-color: #e2e8f0;
      vertical-align: middle;
    }
    
    .table tbody tr:hover {
      background-color: #f8fafc;
    }
    
    .id-badge {
      background: #6b7280;
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
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
  </style>
</head>
<body>
  <div class="main-container">
    <!-- Header -->
     <?php include '../../header.php'; ?>
    <div class="header-card">
      <div class="d-flex justify-content-between align-items-center">
        <div class="page-title">
          <h1>
            <div class="icon-box">
              <i class="bi bi-people"></i>
            </div>
            Clientes
          </h1>
        </div>
        <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#clienteModal" onclick="openNewModal()">
          <i class="bi bi-plus-lg me-2"></i>Novo Cliente
        </button>
      </div>
    </div>

    <!-- Clientes Table -->
    <div class="table-container">
      <table class="table" id="clientes-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Telefone</th>
            <th>Endereço</th>
            <th>CPF/CNPJ</th>
            <th>Contribuinte</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <div class="total-info">
        <span id="total-clientes">Total: 0 cliente(s)</span>
      </div>
    </div>
  </div>

  <!-- Cliente Modal -->
  <div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="clienteModalLabel">
            <i class="bi bi-plus-circle"></i>
            Novo Cliente
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="cliente-form">
            <input type="hidden" id="cliente-id">
            
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome *</label>
                <input type="text" class="form-control" id="nome" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone">
              </div>
            </div>

            <div class="row">
              <div class="col-md-8 mb-3">
                <label for="rua" class="form-label">Rua *</label>
                <input type="text" class="form-control" id="rua" required>
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
                <label for="complemento" class="form-label">Complemento</label>
                <input type="text" class="form-control" id="complemento">
              </div>
            </div>

            <div class="row">
              <div class="col-md-4 mb-3">
                <label for="ie_rg" class="form-label">IE / RG</label>
                <input type="text" class="form-control" id="ie_rg">
              </div>
              <div class="col-md-4 mb-3">
                <label for="cpf_cnpj" class="form-label">CPF / CNPJ</label>
                <input type="text" class="form-control" id="cpf_cnpj">
              </div>
              <div class="col-md-4 mb-3">
                <label for="cidade_id" class="form-label">Cidade ID</label>
                <input type="number" class="form-control" id="cidade_id">
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="contribuinte" class="form-label">Contribuinte</label>
                <select class="form-select" id="contribuinte">
                  <option value="0">Não</option>
                  <option value="1">Sim</option>
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-2"></i>Cancelar
          </button>
          <button type="button" class="btn btn-primary-custom" onclick="saveCliente()">
            <i class="bi bi-check-lg me-2"></i>Salvar
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const apiUrl = '';
    const tableBody = document.querySelector('#clientes-table tbody');
    const form = document.getElementById('cliente-form');
    const modalTitle = document.getElementById('clienteModalLabel');
    const totalClientes = document.getElementById('total-clientes');
    const modal = new bootstrap.Modal(document.getElementById('clienteModal'));

    const fields = [
      'nome','rua','numero','bairro','cep',
      'telefone','complemento','ie_rg','cpf_cnpj',
      'contribuinte','cidade_id'
    ];

    async function loadClientes() {
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

      data.forEach(c => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><span class="id-badge">${c.id}</span></td>
          <td><strong>${c.nome}</strong></td>
          <td>${c.telefone || '-'}</td>
          <td>
            <small class="text-muted">
              ${c.rua ? c.rua + ', ' + (c.numero || '') : '-'}<br>
              ${c.bairro || ''} ${c.cep ? '- ' + c.cep : ''}
            </small>
          </td>
          <td><code>${c.cpf_cnpj || '-'}</code></td>
          <td>
            <span class="badge ${c.contribuinte ? 'bg-success' : 'bg-secondary'}">
              ${c.contribuinte ? 'Sim' : 'Não'}
            </span>
          </td>
          <td>
            <div class="action-buttons">
              <button class="btn-action btn-edit" onclick="editCliente(${c.id})" title="Editar">
                <i class="bi bi-pencil"></i>
              </button>
              <button class="btn-action btn-delete" onclick="deleteCliente(${c.id})" title="Excluir">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </td>
        `;
        tableBody.appendChild(tr);
      });

      totalClientes.textContent = `Total: ${data.length} cliente(s)`;
    }

    function openNewModal() {
      resetForm();
      modalTitle.innerHTML = '<i class="bi bi-plus-circle"></i> Novo Cliente';
    }

    function resetForm() {
      form.reset();
      form['cliente-id'].value = '';
    }

    async function editCliente(id) {
      const res = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Accept':'application/json',
          'Content-Type':'application/json'
        },
        body: JSON.stringify({ id })
      });
      const c = await res.json();

      form['cliente-id'].value = c.id;
      fields.forEach(f => form[f].value = c[f] ?? '');
      modalTitle.innerHTML = `<i class="bi bi-pencil"></i> Editando Cliente #${id}`;
      
      modal.show();
    }

    async function deleteCliente(id) {
      if (!confirm('Excluir cliente #' + id + '?')) return;
      await fetch(`${apiUrl}?id=${id}`, {
        method: 'DELETE'
      });
      loadClientes();
    }

    async function saveCliente() {
      const id = form['cliente-id'].value;
      const body = {};
      fields.forEach(f => {
        if (f === 'contribuinte') {
          body[f] = Number(form[f].value);
        } else {
          body[f] = form[f].value;
        }
      });

      const opts = {
        method: id ? 'PUT' : 'POST',
        headers: { 'Content-Type':'application/json' },
        body: JSON.stringify(body)
      };

      const url = id ? `${apiUrl}?id=${id}` : apiUrl;
      const res = await fetch(url, opts);
      if (res.ok) {
        modal.hide();
        loadClientes();
      }
    }

    // Reset form when modal is closed
    document.getElementById('clienteModal').addEventListener('hidden.bs.modal', resetForm);

    loadClientes();
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
      echo json_encode($cliente->getAll());
    } elseif (isset($data['id']) && count($data) === 1) {
      $item = $cliente->getById((int)$data['id']);
      if ($item) echo json_encode($item);
      else {
        http_response_code(404);
        echo json_encode(['error'=>'Cliente não encontrado']);
      }
    } else {
      $id = $cliente->create($data);
      http_response_code(201);
      echo json_encode(['id'=>$id]);
    }
    break;

  case 'PUT':
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $id   = (int)($_GET['id'] ?? 0);
    $ok   = $cliente->update($id, $data);
    echo json_encode(['updated'=>$ok]);
    break;

  case 'DELETE':
    $id = (int)($_GET['id'] ?? 0);
    $ok = $cliente->delete($id);
    echo json_encode(['deleted'=>$ok]);
    break;

  default:
    http_response_code(405);
    echo json_encode(['error'=>'Método não permitido']);
    break;
}
