<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
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
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    * {
      box-sizing: border-box;
    }

    body {
      background-color: var(--background-color);
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      margin: 0;
      padding: 0;
      padding-bottom: 80px; /* Space for mobile bottom nav */
    }

    /* Desktop Header */
    .desktop-header {
      background: var(--surface-color);
      box-shadow: var(--shadow);
      padding: 1rem 0;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid var(--border-color);
    }
    
    .company-name {
      color: var(--text-primary);
      font-size: 1.5rem;
      font-weight: 600;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }
    
    .company-icon {
      width: 40px;
      height: 40px;
      background: var(--primary-color);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
    }
    
    .desktop-nav {
      display: flex;
      justify-content: center;
      gap: 0.5rem;
    }
    
    .desktop-nav .nav-link {
      color: var(--text-secondary);
      font-weight: 500;
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      transition: all 0.2s ease;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .desktop-nav .nav-link:hover {
      background: var(--background-color);
      color: var(--text-primary);
    }
    
    .desktop-nav .nav-link.active {
      background: var(--primary-color);
      color: white;
    }

    /* Mobile Header */
    .mobile-header {
      background: var(--surface-color);
      box-shadow: var(--shadow);
      padding: 1rem;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid var(--border-color);
      display: none;
    }

    .mobile-header-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .mobile-company-name {
      color: var(--text-primary);
      font-size: 1.25rem;
      font-weight: 600;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .mobile-company-icon {
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

    .menu-toggle {
      background: none;
      border: none;
      color: var(--text-primary);
      font-size: 1.5rem;
      padding: 0.5rem;
      border-radius: 6px;
      transition: background-color 0.2s;
    }

    .menu-toggle:hover {
      background: var(--background-color);
    }

    /* Mobile Drawer */
    .mobile-drawer {
      position: fixed;
      top: 0;
      left: -100%;
      width: 280px;
      height: 100vh;
      background: var(--surface-color);
      box-shadow: var(--shadow-lg);
      z-index: 2000;
      transition: left 0.3s ease;
      overflow-y: auto;
    }

    .mobile-drawer.open {
      left: 0;
    }

    .drawer-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1500;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }

    .drawer-overlay.show {
      opacity: 1;
      visibility: visible;
    }

    .drawer-header {
      padding: 1.5rem;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .drawer-close {
      background: none;
      border: none;
      color: var(--text-secondary);
      font-size: 1.5rem;
      padding: 0.5rem;
      border-radius: 6px;
      transition: all 0.2s;
    }

    .drawer-close:hover {
      background: var(--background-color);
      color: var(--text-primary);
    }

    .drawer-nav {
      padding: 1rem 0;
    }

    .drawer-nav .nav-link {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.5rem;
      color: var(--text-secondary);
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s;
      border-left: 3px solid transparent;
    }

    .drawer-nav .nav-link:hover {
      background: var(--background-color);
      color: var(--text-primary);
    }

    .drawer-nav .nav-link.active {
      background: rgba(37, 99, 235, 0.1);
      color: var(--primary-color);
      border-left-color: var(--primary-color);
    }

    .drawer-nav .nav-icon {
      font-size: 1.25rem;
      width: 24px;
      text-align: center;
    }

    /* Mobile Bottom Navigation */
    .mobile-bottom-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: var(--surface-color);
      border-top: 1px solid var(--border-color);
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
      display: none;
    }

    .bottom-nav-container {
      display: flex;
      justify-content: space-around;
      align-items: center;
      padding: 0.5rem 0;
      max-width: 100%;
    }

    .bottom-nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.25rem;
      padding: 0.5rem;
      color: var(--text-secondary);
      text-decoration: none;
      font-size: 0.75rem;
      font-weight: 500;
      transition: all 0.2s;
      min-width: 0;
      flex: 1;
    }

    .bottom-nav-item:hover {
      color: var(--primary-color);
    }

    .bottom-nav-item.active {
      color: var(--primary-color);
    }

    .bottom-nav-icon {
      font-size: 1.25rem;
    }

    .bottom-nav-text {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 100%;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .desktop-header {
        display: none;
      }

      .mobile-header {
        display: block;
      }

      .mobile-bottom-nav {
        display: block;
      }

      body {
        padding-bottom: 80px;
      }
    }

    @media (min-width: 769px) {
      .mobile-header {
        display: none;
      }

      .mobile-bottom-nav {
        display: none;
      }

      body {
        padding-bottom: 0;
      }
    }

    /* Content spacing */
    .main-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 1rem;
    }

    @media (max-width: 768px) {
      .main-container {
        padding: 0.75rem;
        margin-top: 0;
      }
    }

    /* Card styles for mobile */
    @media (max-width: 768px) {
      .header-card {
        border-radius: 12px;
        margin-bottom: 1rem;
        box-shadow: var(--shadow);
      }

      .table-container {
        border-radius: 12px;
        box-shadow: var(--shadow);
        margin-bottom: 1rem;
      }

      .modal-dialog {
        margin: 1rem;
        max-width: calc(100% - 2rem);
      }

      .modal-xl {
        max-width: calc(100% - 2rem);
      }
    }
  </style>
</head>
<body>
  <!-- Desktop Header -->
  <header class="desktop-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-md-3">
          <h1 class="company-name">
            <div class="company-icon">
              <i class="bi bi-building-gear"></i>
            </div>
            <span>Ponto Eletricistas</span>
          </h1>
        </div>
        
        <div class="col-md-6">
          <nav class="desktop-nav">
            <a href="../orcamento/index.php" class="nav-link" id="desktop-nav-orcamento">
              <i class="bi bi-file-earmark-text"></i>
              <span>Orçamentos</span>
            </a>
            <a href="../clientes/index.php" class="nav-link" id="desktop-nav-clientes">
              <i class="bi bi-people"></i>
              <span>Clientes</span>
            </a>
            <a href="../produtos/index.php" class="nav-link" id="desktop-nav-produtos">
              <i class="bi bi-box-seam"></i>
              <span>Produtos</span>
            </a>
            <a href="../empresas/index.php" class="nav-link" id="desktop-nav-empresas">
              <i class="bi bi-building"></i>
              <span>Empresas</span>
            </a>
          </nav>
        </div>
        
        <div class="col-md-3 text-end">
          <span class="text-muted small">
            <i class="bi bi-clock me-1"></i>
            <span id="current-time"></span>
          </span>
        </div>
      </div>
    </div>
  </header>

  <!-- Mobile Header -->
  <header class="mobile-header">
    <div class="mobile-header-content">
      <h1 class="mobile-company-name">
        <div class="mobile-company-icon">
          <i class="bi bi-building-gear"></i>
        </div>
        <span>Sistema</span>
      </h1>
      <button class="menu-toggle" id="menuToggle">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </header>

  <!-- Mobile Drawer -->
  <div class="drawer-overlay" id="drawerOverlay"></div>
  <nav class="mobile-drawer" id="mobileDrawer">
    <div class="drawer-header">
      <h2 class="mobile-company-name">
        <div class="mobile-company-icon">
          <i class="bi bi-building-gear"></i>
        </div>
        <span>Ponto Eletricistas</span>
      </h2>
      <button class="drawer-close" id="drawerClose">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    
    <div class="drawer-nav">
      <a href="../orcamento/index.php" class="nav-link" id="drawer-nav-orcamento">
        <i class="bi bi-file-earmark-text nav-icon"></i>
        <span>Orçamentos</span>
      </a>
      <a href="../clientes/index.php" class="nav-link" id="drawer-nav-clientes">
        <i class="bi bi-people nav-icon"></i>
        <span>Clientes</span>
      </a>
      <a href="../produtos/index.php" class="nav-link" id="drawer-nav-produtos">
        <i class="bi bi-box-seam nav-icon"></i>
        <span>Produtos</span>
      </a>
      <a href="../empresas/index.php" class="nav-link" id="drawer-nav-empresas">
        <i class="bi bi-building nav-icon"></i>
        <span>Empresas</span>
      </a>
    </div>
  </nav>

  <!-- Mobile Bottom Navigation -->
  <nav class="mobile-bottom-nav">
    <div class="bottom-nav-container">
      <a href="../orcamento/index.php" class="bottom-nav-item" id="bottom-nav-orcamento">
        <i class="bi bi-file-earmark-text bottom-nav-icon"></i>
        <span class="bottom-nav-text">Orçamentos</span>
      </a>
      <a href="../clientes/index.php" class="bottom-nav-item" id="bottom-nav-clientes">
        <i class="bi bi-people bottom-nav-icon"></i>
        <span class="bottom-nav-text">Clientes</span>
      </a>
      <a href="../produtos/index.php" class="bottom-nav-item" id="bottom-nav-produtos">
        <i class="bi bi-box-seam bottom-nav-icon"></i>
        <span class="bottom-nav-text">Produtos</span>
      </a>
      <a href="../empresas/index.php" class="bottom-nav-item" id="bottom-nav-empresas">
        <i class="bi bi-building bottom-nav-icon"></i>
        <span class="bottom-nav-text">Empresas</span>
      </a>
    </div>
  </nav>

  <script>
    // Mobile menu functionality
    const menuToggle = document.getElementById('menuToggle');
    const drawerClose = document.getElementById('drawerClose');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const drawerOverlay = document.getElementById('drawerOverlay');

    function openDrawer() {
      mobileDrawer.classList.add('open');
      drawerOverlay.classList.add('show');
      document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
      mobileDrawer.classList.remove('open');
      drawerOverlay.classList.remove('show');
      document.body.style.overflow = '';
    }

    menuToggle.addEventListener('click', openDrawer);
    drawerClose.addEventListener('click', closeDrawer);
    drawerOverlay.addEventListener('click', closeDrawer);

    // Close drawer on link click
    document.querySelectorAll('.drawer-nav .nav-link').forEach(link => {
      link.addEventListener('click', closeDrawer);
    });

    // Update current time
    function updateTime() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('pt-BR', { 
        hour: '2-digit', 
        minute: '2-digit' 
      });
      const timeElement = document.getElementById('current-time');
      if (timeElement) {
        timeElement.textContent = timeString;
      }
    }
    
    updateTime();
    setInterval(updateTime, 60000);
    
    // Set active navigation items
    document.addEventListener('DOMContentLoaded', function() {
      const currentPath = window.location.pathname;
      
      // Remove all active classes
      document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
      });
      document.querySelectorAll('.bottom-nav-item').forEach(item => {
        item.classList.remove('active');
      });
      
      // Set active based on current path
      if (currentPath.includes('orcamento')) {
        document.getElementById('desktop-nav-orcamento')?.classList.add('active');
        document.getElementById('drawer-nav-orcamento')?.classList.add('active');
        document.getElementById('bottom-nav-orcamento')?.classList.add('active');
      } else if (currentPath.includes('clientes')) {
        document.getElementById('desktop-nav-clientes')?.classList.add('active');
        document.getElementById('drawer-nav-clientes')?.classList.add('active');
        document.getElementById('bottom-nav-clientes')?.classList.add('active');
      } else if (currentPath.includes('produtos')) {
        document.getElementById('desktop-nav-produtos')?.classList.add('active');
        document.getElementById('drawer-nav-produtos')?.classList.add('active');
        document.getElementById('bottom-nav-produtos')?.classList.add('active');
      } else if (currentPath.includes('empresas')) {
        document.getElementById('desktop-nav-empresas')?.classList.add('active');
        document.getElementById('drawer-nav-empresas')?.classList.add('active');
        document.getElementById('bottom-nav-empresas')?.classList.add('active');
      }
    });

    // Handle swipe gestures for drawer
    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    document.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      isDragging = true;
    });

    document.addEventListener('touchmove', (e) => {
      if (!isDragging) return;
      currentX = e.touches[0].clientX;
    });

    document.addEventListener('touchend', () => {
      if (!isDragging) return;
      isDragging = false;
      
      const diffX = currentX - startX;
      
      // Swipe right to open drawer (from left edge)
      if (startX < 50 && diffX > 100) {
        openDrawer();
      }
      
      // Swipe left to close drawer
      if (mobileDrawer.classList.contains('open') && diffX < -100) {
        closeDrawer();
      }
    });
  </script>
</body>
</html>