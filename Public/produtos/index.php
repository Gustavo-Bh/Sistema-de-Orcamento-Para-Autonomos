<?php
// Public/produtos/index.php

// 1) Conexão e Model
require __DIR__ . '/../../app/Models/Database.php';
require __DIR__ . '/../../app/Models/Produto.php';

use App\Models\Produto;

$produto = new Produto();
$method  = $_SERVER['REQUEST_METHOD'];
$accept  = $_SERVER['HTTP_ACCEPT'] ?? '';

// 2) Se for GET no navegador, exibe a UI
if ($method === 'GET' && strpos($accept, 'application/json') === false):
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produtos - Ponto Eletricistas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

<style>
  /* Cores neutras para produtos */
  :root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --secondary-color: #64748b;
    --background-color: #f8fafc;
    --surface-color: #ffffff;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --border-color: #e2e8f0;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
  }
  
  body {
    background-color: var(--background-color);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  }
  
  .main-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
  }
  
  .header-card {
    background: var(--surface-color);
    border-radius: 12px;
    box-shadow: var(--shadow);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid var(--border-color);
  }
  
  .page-title h1 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
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
    color: white;
  }
  
  .btn-primary-custom:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    color: white;
  }
  
  .table-container {
    background: var(--surface-color);
    border-radius: 12px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border-color);
    overflow: hidden;
  }
  
  .table {
    margin: 0;
  }
  
  .table thead th {
    background: var(--background-color);
    border: none;
    padding: 1rem;
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
  }
  
  .table tbody td {
    padding: 1rem;
    border-color: var(--border-color);
    vertical-align: middle;
  }
  
  .table tbody tr:hover {
    background-color: var(--background-color);
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
  }
  
  .btn-action {
    width: 36px;
    height: 36px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    background: var(--surface-color);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    cursor: pointer;
  }
  
  .btn-action:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow);
  }
  
  .btn-edit {
    color: var(--primary-color);
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
    border-bottom: 1px solid var(--border-color);
    padding: 1.5rem;
  }
  
  .modal-title {
    font-weight: 600;
    color: var(--text-primary);
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
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
  }
  
  .form-control {
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 0.75rem;
    transition: all 0.2s;
  }
  
  .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  }
  
  .btn-secondary-custom {
    background: var(--secondary-color);
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    color: white;
    transition: all 0.2s;
  }
  
  .btn-secondary-custom:hover {
    background: #475569;
    color: white;
  }
  
  .total-info {
    color: var(--text-secondary);
    font-size: 0.875rem;
    padding: 1rem 1.5rem;
    background: var(--background-color);
    border-top: 1px solid var(--border-color);
  }

  .price-display {
    font-weight: 600;
    color: #059669;
    font-size: 1.1rem;
  }

  .barcode-display {
    font-family: 'Courier New', monospace;
    background: var(--background-color);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.9rem;
    color: var(--text-secondary);
  }

  .product-name {
    font-weight: 600;
    color: var(--text-primary);
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

    .table thead th {
      padding: 0.75rem 0.5rem;
      font-size: 0.8rem;
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

    .price-display {
      font-size: 1rem;
    }

    .barcode-display {
      font-size: 0.8rem;
      padding: 0.2rem 0.4rem;
    }
    .product-name{
        font-size: 1.1rem !important;
    }
    .table-container th {
        width: 60px;
    }
  }
</style>

<div class="main-container">
     <?php include '../../header.php'; ?>
  <!-- Header -->
  <div class="header-card">
    <div class="d-flex justify-content-between align-items-center">
      <div class="page-title">
        <h1>
          <div class="icon-box">
            <i class="bi bi-box-seam"></i>
          </div>
          Produtos
        </h1>
      </div>
      <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#produtoModal" onclick="openNewModal()">
        <i class="bi bi-plus-lg me-2"></i>Novo Produto
      </button>
    </div>
  </div>

  <!-- Products Table -->
  <div class="table-container">
    <table class="table" id="produtos-table">
      <thead>
        <tr>
          <th>Nome</th>
          <th>Valor</th>
          <th>Cód. Barras</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
    <div class="total-info">
      <span id="total-produtos">Total: 0 produto(s)</span>
    </div>
  </div>
</div>

<!-- Product Modal -->
<!--<div class="modal fade" id="produtoModal" tabindex="-1" aria-labelledby="produtoModalLabel" aria-hidden="true">-->
<!--  <div class="modal-dialog modal-xl">-->
<!--    <div class="modal-content">-->
<!--      <div class="modal-header">-->
<!--        <h5 class="modal-title" id="produtoModalLabel">-->
<!--          <i class="bi bi-plus-circle"></i>-->
<!--          Novo Produto-->
<!--        </h5>-->
<!--        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--      </div>-->
<!--      <div class="modal-body">-->
<!--        <form id="produto-form">-->
<!--          <input type="hidden" id="produto-id">-->
          
<!--          <div class="row">-->
<!--            <div class="col-md-6 mb-3">-->
<!--              <label for="nome" class="form-label">Nome *</label>-->
<!--              <input type="text" class="form-control" id="nome" required>-->
<!--            </div>-->
<!--            <div class="col-md-6 mb-3">-->
<!--              <label for="valor" class="form-label">Valor *</label>-->
<!--              <input type="number" step="0.01" class="form-control" id="valor" required>-->
<!--            </div>-->
<!--          </div>-->

<!--          <div class="row">-->
<!--            <div class="col-md-6 mb-3">-->
<!--              <label for="cfop_int" class="form-label">CFOP Interno *</label>-->
<!--              <input type="text" class="form-control" id="cfop_int" required value="5102">-->
<!--            </div>-->
<!--            <div class="col-md-6 mb-3">-->
<!--              <label for="cfop_ext" class="form-label">CFOP Externo *</label>-->
<!--              <input type="text" class="form-control" id="cfop_ext" required value="6102">-->
<!--            </div>-->
<!--          </div>-->

<!--          <div class="row">-->
<!--            <div class="col-md-6 mb-3">-->
<!--              <label for="ncm" class="form-label">NCM *</label>-->
<!--              <input type="text" class="form-control" id="ncm" required>-->
<!--            </div>-->
<!--            <div class="col-md-6 mb-3">-->
<!--              <label for="categoria_id" class="form-label">Categoria ID</label>-->
<!--              <input type="number" class="form-control" id="categoria_id">-->
<!--            </div>-->
<!--          </div>-->

<!--          <div class="row">-->
<!--            <div class="col-md-6 mb-3">-->
<!--              <label for="codigo_barras" class="form-label">Código de Barras</label>-->
<!--              <input type="text" class="form-control" id="codigo_barras">-->
<!--            </div>-->
<!--            <div class="col-md-6 mb-3">-->
<!--              <label for="unidade_venda" class="form-label">Unidade de Venda</label>-->
<!--              <input type="text" class="form-control" id="unidade_venda" placeholder="UN">-->
<!--            </div>-->
<!--          </div>-->

<!--          <div class="row">-->
<!--            <div class="col-md-3 mb-3">-->
<!--              <label for="perc_icms" class="form-label">% ICMS</label>-->
<!--              <input type="number" step="0.01" class="form-control" id="perc_icms" placeholder="18.00">-->
<!--            </div>-->
<!--            <div class="col-md-3 mb-3">-->
<!--              <label for="perc_pis" class="form-label">% PIS</label>-->
<!--              <input type="number" step="0.01" class="form-control" id="perc_pis" placeholder="1.65">-->
<!--            </div>-->
<!--            <div class="col-md-3 mb-3">-->
<!--              <label for="perc_cofins" class="form-label">% COFINS</label>-->
<!--              <input type="number" step="0.01" class="form-control" id="perc_cofins" placeholder="7.60">-->
<!--            </div>-->
<!--            <div class="col-md-3 mb-3">-->
<!--              <label for="perc_ipi" class="form-label">% IPI</label>-->
<!--              <input type="number" step="0.01" class="form-control" id="perc_ipi" placeholder="0.00">-->
<!--            </div>-->
<!--          </div>-->

<!--          <div class="row">-->
<!--            <div class="col-md-3 mb-3">-->
<!--              <label for="cst_csosn" class="form-label">CST/CSOSN</label>-->
<!--              <input type="text" class="form-control" id="cst_csosn" placeholder="102">-->
<!--            </div>-->
<!--            <div class="col-md-3 mb-3">-->
<!--              <label for="cst_pis" class="form-label">CST PIS</label>-->
<!--              <input type="text" class="form-control" id="cst_pis" placeholder="01">-->
<!--            </div>-->
<!--            <div class="col-md-3 mb-3">-->
<!--              <label for="cst_cofins" class="form-label">CST COFINS</label>-->
<!--              <input type="text" class="form-control" id="cst_cofins" placeholder="01">-->
<!--            </div>-->
<!--            <div class="col-md-3 mb-3">-->
<!--              <label for="cst_ipi" class="form-label">CST IPI</label>-->
<!--              <input type="text" class="form-control" id="cst_ipi" placeholder="50">-->
<!--            </div>-->
<!--          </div>-->
<!--        </form>-->
<!--      </div>-->
<!--      <div class="modal-footer">-->
<!--        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">-->
<!--          <i class="bi bi-x-lg me-2"></i>Cancelar-->
<!--        </button>-->
<!--        <button type="button" class="btn btn-primary-custom" onclick="saveProduto()">-->
<!--          <i class="bi bi-check-lg me-2"></i>Salvar-->
<!--        </button>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->
<!-- Product Modal -->
<div class="modal fade" id="produtoModal" tabindex="-1" aria-labelledby="produtoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="produtoModalLabel">
          <i class="bi bi-plus-circle"></i>
          Novo Produto
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body">
        <form id="produto-form">
          <input type="hidden" id="produto-id">

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nome" class="form-label">Nome *</label>
              <input type="text" class="form-control" id="nome" required>
            </div>

            <div class="col-md-6 mb-3">
              <label for="valor" class="form-label">Valor *</label>
              <input type="number" step="0.01" class="form-control" id="valor" required>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">
          <i class="bi bi-x-lg me-2"></i>Cancelar
        </button>
        <button type="button" class="btn btn-primary-custom" onclick="saveProduto()">
          <i class="bi bi-check-lg me-2"></i>Salvar
        </button>
      </div>

    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const apiUrl = '';
  const tableBody = document.querySelector('#produtos-table tbody');
  const form = document.getElementById('produto-form');
  const modalTitle = document.getElementById('produtoModalLabel');
  const totalProdutos = document.getElementById('total-produtos');
  const modal = new bootstrap.Modal(document.getElementById('produtoModal'));

//   const fields = [
//     'nome','valor','cfop_int','cfop_ext','ncm','categoria_id',
//     'codigo_barras','unidade_venda','perc_icms','perc_pis',
//     'perc_cofins','perc_ipi','cst_csosn','cst_pis','cst_cofins','cst_ipi'
//   ];

const fields = ['nome', 'valor'];


  async function load() {
    const res = await fetch(apiUrl, {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({})
    });
    const data = await res.json();
    tableBody.innerHTML = '';

    data.forEach(p => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>
          <div class="product-name">${p.nome}</div>
        </td>
        <td>
          <span class="price-display">R$ ${parseFloat(p.valor).toFixed(2)}</span>
        </td>
        <td>
          ${p.codigo_barras ? 
            `<span class="barcode-display">${p.codigo_barras}</span>` : 
            '<span class="text-muted">-</span>'
          }
        </td>
        <td>
          <div class="action-buttons">
            <button class="btn-action btn-edit" onclick="edit(${p.id})" title="Editar">
              <i class="bi bi-pencil"></i>
            </button>
            <button class="btn-action btn-delete" onclick="del(${p.id})" title="Excluir">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </td>
      `;
      tableBody.appendChild(tr);
    });

    totalProdutos.textContent = `Total: ${data.length} produto(s)`;
  }

  function openNewModal() {
    resetForm();
    modalTitle.innerHTML = '<i class="bi bi-plus-circle"></i> Novo Produto';
  }

  function resetForm() {
    form.reset();
    form['produto-id'].value = '';
    
    // Preenche defaults
    form['cfop_int'].value = '5102';
    form['cfop_ext'].value = '6102';
    form['unidade_venda'].value = 'UN';
    form['perc_icms'].value = '18.00';
    form['perc_pis'].value = '1.65';
    form['perc_cofins'].value = '7.60';
    form['perc_ipi'].value = '0.00';
    form['cst_csosn'].value = '102';
    form['cst_pis'].value = '01';
    form['cst_cofins'].value = '01';
    form['cst_ipi'].value = '50';
  }

  window.edit = async id => {
    const res = await fetch(apiUrl, {
      method:'POST',
      headers:{ 'Content-Type':'application/json' },
      body: JSON.stringify({ id })
    });
    const p = await res.json();

    form['produto-id'].value = p.id;
    fields.forEach(f => form[f].value = p[f] ?? form[f].value);
    modalTitle.innerHTML = `<i class="bi bi-pencil"></i> Editando Produto #${id}`;
    
    modal.show();
  };

  window.del = async id => {
    if (!confirm('Excluir produto #' + id + '?')) return;
    await fetch(`${apiUrl}?id=${id}`, { method:'DELETE' });
    load();
  };

  async function saveProduto() {
    const id = form['produto-id'].value;
    const body = {};
    fields.forEach(f => body[f] = form[f].value);

    const opts = {
      method: id ? 'PUT' : 'POST',
      headers: { 'Content-Type':'application/json' },
      body: JSON.stringify(body)
    };

    const url = id ? `${apiUrl}?id=${id}` : apiUrl;
    await fetch(url, opts);
    modal.hide();
    load();
  }

  // Reset form when modal is closed
  document.getElementById('produtoModal').addEventListener('hidden.bs.modal', resetForm);

  load();
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
    $body = json_decode(file_get_contents('php://input'), true) ?? [];
    if (empty($body)) {
      // lista tudo
      echo json_encode($produto->getAll());
    } elseif (isset($body['id']) && count($body) === 1) {
      // retorna só um
      $item = $produto->getById((int)$body['id']);
      if ($item) echo json_encode($item);
      else {
        http_response_code(404);
        echo json_encode(['error'=>'Produto não encontrado']);
      }
    } else {
      // cria novo
      $id = $produto->create($body);
      http_response_code(201);
      echo json_encode(['id'=>$id]);
    }
    break;

  case 'PUT':
    $body = json_decode(file_get_contents('php://input'), true);
    if ($body === null) parse_str(file_get_contents('php://input'), $body);
    $id   = (int)($_GET['id'] ?? 0);
    $ok   = $produto->update($id, $body);
    echo json_encode(['updated'=>$ok]);
    break;

  case 'DELETE':
    $id = (int)($_GET['id'] ?? 0);
    $ok = $produto->delete($id);
    echo json_encode(['deleted'=>$ok]);
    break;

  default:
    http_response_code(405);
    echo json_encode(['error'=>'Método não permitido']);
    break;
}
