<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != "admin"){
    header("Location: acesso.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlphaSolutions - Painel Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{
  --bg:#07111f;--surface:#0d1a2e;--card:#0f1f35;
  --border:rgba(30,136,229,0.15);--accent:#1e6fe8;--glow:#64b5f6;
  --text:#e8f0fe;--muted:#7a9cc4;--green:#4dd9ac;--teal:#38bdf8;
}
body{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:linear-gradient(rgba(30,136,229,0.03) 1px,transparent 1px),
    linear-gradient(90deg,rgba(30,136,229,0.03) 1px,transparent 1px);
  background-size:50px 50px;}
body::after{content:'';position:fixed;width:600px;height:600px;border-radius:50%;
  background:radial-gradient(circle,rgba(21,101,192,0.12),transparent 70%);
  top:-150px;left:-150px;pointer-events:none;z-index:0;}
.navbar{position:relative;z-index:10;background:var(--surface);border-bottom:1px solid var(--border);
  padding:0 32px;height:60px;display:flex;align-items:center;justify-content:space-between;}
.navbar::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;
  background:linear-gradient(90deg,transparent,var(--accent),var(--glow),var(--accent),transparent);opacity:0.6;}
.nav-brand{font-family:'Rajdhani',sans-serif;font-size:17px;font-weight:700;
  letter-spacing:3px;text-transform:uppercase;color:var(--text);}
.nav-brand span{color:var(--glow);}
.nav-right{display:flex;align-items:center;gap:20px;}
.nav-user{font-size:13px;color:var(--muted);}
.nav-user strong{color:var(--text);}
.btn-logout{padding:6px 16px;background:transparent;border:1px solid var(--border);
  border-radius:6px;color:var(--muted);font-family:'Rajdhani',sans-serif;font-size:12px;
  font-weight:600;letter-spacing:2px;text-transform:uppercase;text-decoration:none;transition:all 0.2s;}
.btn-logout:hover{border-color:rgba(255,107,107,0.4);color:#ff6b6b;}
.main{position:relative;z-index:1;max-width:1100px;margin:0 auto;padding:52px 24px 60px;}
.page-header{text-align:center;margin-bottom:48px;}
.page-header h1{font-family:'Rajdhani',sans-serif;font-size:28px;font-weight:700;
  letter-spacing:4px;text-transform:uppercase;color:var(--text);}
.page-header p{margin-top:8px;font-size:13px;color:var(--muted);letter-spacing:1px;}
.header-line{width:80px;height:1px;background:linear-gradient(90deg,transparent,var(--accent),transparent);
  margin:14px auto 0;}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;}
@media(max-width:768px){.grid{grid-template-columns:1fr;}}
.card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:32px 28px;
  position:relative;overflow:hidden;transition:transform 0.25s,box-shadow 0.25s,border-color 0.25s;
  animation:fadeUp 0.5s cubic-bezier(0.22,1,0.36,1) both;}
.card:nth-child(1){animation-delay:0.05s}
.card:nth-child(2){animation-delay:0.12s}
.card:nth-child(3){animation-delay:0.19s}
.card:hover{transform:translateY(-5px);box-shadow:0 16px 40px rgba(30,136,229,0.12);
  border-color:rgba(30,136,229,0.35);}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;}
.card-blue::before{background:linear-gradient(90deg,var(--accent),var(--glow));}
.card-green::before{background:linear-gradient(90deg,#16a34a,var(--green));}
.card-teal::before{background:linear-gradient(90deg,#0891b2,var(--teal));}
.card::after{content:'';position:absolute;bottom:-40px;right:-40px;width:120px;height:120px;
  border-radius:50%;opacity:0.04;transition:opacity 0.3s;}
.card-blue::after{background:var(--accent);}
.card-green::after{background:var(--green);}
.card-teal::after{background:var(--teal);}
.card:hover::after{opacity:0.09;}
.card-icon{width:44px;height:44px;border-radius:10px;display:flex;align-items:center;
  justify-content:center;margin-bottom:20px;font-size:22px;}
.card-blue .card-icon{background:rgba(30,136,229,0.12);color:var(--accent);}
.card-green .card-icon{background:rgba(77,217,172,0.12);color:var(--green);}
.card-teal .card-icon{background:rgba(56,189,248,0.12);color:var(--teal);}
.card h3{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;letter-spacing:2px;
  text-transform:uppercase;color:var(--text);margin-bottom:8px;}
.card p{font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:24px;}
.card-divider{height:1px;background:var(--border);margin-bottom:20px;}
.btn-row{display:flex;flex-wrap:wrap;gap:8px;}
.btn{padding:7px 16px;border-radius:6px;font-family:'Rajdhani',sans-serif;font-size:12px;
  font-weight:600;letter-spacing:1.5px;text-transform:uppercase;text-decoration:none;
  border:1px solid transparent;cursor:pointer;transition:all 0.2s;display:inline-flex;
  align-items:center;gap:6px;}
.btn-primary{background:linear-gradient(135deg,var(--accent),#1a5bc4);color:white;
  border-color:rgba(100,181,246,0.2);}
.btn-primary:hover{box-shadow:0 4px 14px rgba(30,136,229,0.35);transform:translateY(-1px);color:white;}
.btn-success{background:linear-gradient(135deg,#16a34a,#0f7a38);color:white;
  border-color:rgba(77,217,172,0.2);}
.btn-success:hover{box-shadow:0 4px 14px rgba(77,217,172,0.25);transform:translateY(-1px);color:white;}
.btn-outline{background:transparent;color:var(--teal);border-color:rgba(56,189,248,0.25);}
.btn-outline:hover{background:rgba(56,189,248,0.08);transform:translateY(-1px);}
footer{text-align:center;margin-top:52px;font-size:12px;color:var(--muted);letter-spacing:1px;opacity:0.6;}
.corner{position:fixed;width:32px;height:32px;opacity:0.18;z-index:0;}
.corner-tl{top:18px;left:18px;border-top:1px solid var(--glow);border-left:1px solid var(--glow);}
.corner-tr{top:18px;right:18px;border-top:1px solid var(--glow);border-right:1px solid var(--glow);}
.corner-bl{bottom:18px;left:18px;border-bottom:1px solid var(--glow);border-left:1px solid var(--glow);}
.corner-br{bottom:18px;right:18px;border-bottom:1px solid var(--glow);border-right:1px solid var(--glow);}
@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<div class="corner corner-tl"></div>
<div class="corner corner-tr"></div>
<div class="corner corner-bl"></div>
<div class="corner corner-br"></div>
<nav class="navbar">
  <div class="nav-brand">Alpha<span>Solutions</span></div>
  <div class="nav-right">
    <span class="nav-user">Olá, <strong><?php echo htmlspecialchars($_SESSION['user']['nome'] ?? 'Admin'); ?></strong></span>
    <a href="logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Sair</a>
  </div>
</nav>
<div class="main">
  <div class="page-header">
    <h1>Painel Administrativo</h1>
    <p>Gerencie clientes, produtos e vendas</p>
    <div class="header-line"></div>
  </div>
  <div class="grid">
    <div class="card card-blue">
      <div class="card-icon"><i class="bi bi-people"></i></div>
      <h3>Clientes</h3>
      <p>Visualize e gerencie os clientes cadastrados no sistema.</p>
      <div class="card-divider"></div>
      <div class="btn-row">
        <a href="clientes/listar.php" class="btn btn-primary"><i class="bi bi-list-ul"></i> Ver Clientes</a>
      </div>
    </div>
    <div class="card card-green">
      <div class="card-icon"><i class="bi bi-box-seam"></i></div>
      <h3>Produtos</h3>
      <p>Adicione novos produtos ou consulte o catálogo existente.</p>
      <div class="card-divider"></div>
      <div class="btn-row">
        <a href="produtos/adicionar.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Adicionar</a>
        <a href="produtos/listar.php" class="btn btn-primary"><i class="bi bi-grid"></i> Ver Produtos</a>
      </div>
    </div>
    <div class="card card-teal">
      <div class="card-icon"><i class="bi bi-graph-up"></i></div>
      <h3>Vendas</h3>
      <p>Consulte o histórico de vendas e gere relatórios detalhados.</p>
      <div class="card-divider"></div>
      <div class="btn-row">
        <a href="vendas/listar_vendas.php" class="btn btn-primary"><i class="bi bi-receipt"></i> Ver Vendas</a>
        <a href="vendas/relatorio_vendas.php" class="btn btn-outline"><i class="bi bi-bar-chart-line"></i> Relatório</a>
        <a href="sync_vendedores.php" class="btn btn-outline"><i class="bi bi-arrow-repeat"></i> Sincronizar Vendedores</a>
      </div>
    </div>
  </div>
  <footer><p>© 2026 AlphaSolutions · Departamento de Vendas</p></footer>
</div>
</body>
</html>
