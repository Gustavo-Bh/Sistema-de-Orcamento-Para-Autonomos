<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ponto Eletricistas - Carregando...</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #2563eb;
      --background-color: #f8fafc;
      --surface-color: #ffffff;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
    }

    body {
      background: linear-gradient(135deg, var(--background-color) 0%, #e2e8f0 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      margin: 0;
      padding: 1rem;
    }
    
    .loading-container {
      text-align: center;
      color: var(--text-primary);
      max-width: 400px;
      width: 100%;
      background: var(--surface-color);
      padding: 3rem 2rem;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .loading-icon {
      width: 80px;
      height: 80px;
      background: var(--primary-color);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 2rem;
      animation: pulse 2s infinite;
    }
    
    .loading-icon i {
      font-size: 2.5rem;
      color: white;
    }
    
    .loading-title {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: var(--text-primary);
      opacity: 0;
      animation: fadeInUp 1s ease forwards 0.5s;
    }
    
    .loading-subtitle {
      font-size: 1.1rem;
      color: var(--text-secondary);
      margin-bottom: 2rem;
      opacity: 0;
      animation: fadeInUp 1s ease forwards 0.7s;
    }
    
    .loading-progress {
      width: 100%;
      height: 4px;
      background: #e2e8f0;
      border-radius: 2px;
      overflow: hidden;
      margin-bottom: 1rem;
      opacity: 0;
      animation: fadeInUp 1s ease forwards 0.9s;
    }
    
    .loading-bar {
      height: 100%;
      background: var(--primary-color);
      border-radius: 2px;
      animation: loading 2s ease-in-out;
      transform: translateX(-100%);
    }
    
    .loading-text {
      font-size: 0.9rem;
      color: var(--text-secondary);
      opacity: 0;
      animation: fadeInUp 1s ease forwards 1.1s;
    }
    
    .redirect-info {
      position: fixed;
      bottom: 2rem;
      left: 50%;
      transform: translateX(-50%);
      background: var(--surface-color);
      padding: 1rem 2rem;
      border-radius: 50px;
      color: var(--text-secondary);
      font-size: 0.9rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      opacity: 0;
      animation: fadeInUp 1s ease forwards 1.5s;
      border: 1px solid #e2e8f0;
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @keyframes loading {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100vw); }
    }
    
    @media (max-width: 576px) {
      .loading-container {
        padding: 2rem 1.5rem;
        margin: 1rem;
      }
      
      .loading-title {
        font-size: 1.5rem;
      }
      
      .loading-subtitle {
        font-size: 1rem;
      }
      
      .loading-icon {
        width: 60px;
        height: 60px;
      }
      
      .loading-icon i {
        font-size: 2rem;
      }

      .redirect-info {
        bottom: 1rem;
        left: 1rem;
        right: 1rem;
        transform: none;
        border-radius: 12px;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="loading-container">
    <div class="loading-icon">
      <i class="bi bi-building-gear"></i>
    </div>
    
    <h1 class="loading-title">Ponto Eletricistas</h1>
    <p class="loading-subtitle">Inicializando sistema...</p>
    
    <div class="loading-progress">
      <div class="loading-bar"></div>
    </div>
    
    <p class="loading-text">Redirecionando para orçamentos</p>
  </div>
  
  <div class="redirect-info">
    <i class="bi bi-info-circle me-2"></i>
    Você será redirecionado automaticamente em <span id="countdown">3</span> segundos
  </div>

  <script>
    let countdown = 3;
    const countdownElement = document.getElementById('countdown');
    
    const timer = setInterval(() => {
      countdown--;
      countdownElement.textContent = countdown;
      
      if (countdown <= 0) {
        clearInterval(timer);
        window.location.href = './Public/orcamento/index.php';
      }
    }, 1000);
  </script>
</body>
</html>