<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin'){
    header("Location: ../acesso.php"); exit;
}
include("../conexao.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$nome_produto = '';

if($id > 0){
    $stmt = $conn->prepare("SELECT nome FROM produtos WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    $nome_produto = $r['nome'] ?? 'Produto';

    $stmt2 = $conn->prepare("DELETE FROM produtos WHERE id=?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlphaSolutions - Produto Eliminado</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f0f4fb;--surface:#fff;--border:#e2e8f4;--accent:#1e6fe8;--accent2:#1a5bc4;
  --text:#0f1f35;--muted:#6b82a8;--light:#e8f0fe;--shadow:rgba(30,111,232,0.08);
  --red:#ef4444;--red-light:#fef2f2;}
body{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);
  display:flex;flex-direction:column;align-items:center;justify-content:center;}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:radial-gradient(circle,rgba(30,111,232,0.05) 1px,transparent 1px);background-size:28px 28px;}
.card{position:relative;z-index:1;background:var(--surface);border:1px solid var(--border);
  border-radius:16px;padding:48px 52px;box-shadow:0 8px 40px var(--shadow);
  text-align:center;max-width:440px;width:100%;animation:popIn 0.4s cubic-bezier(0.22,1,0.36,1) both;}
.icon-wrap{width:72px;height:72px;background:var(--red-light);border-radius:50%;
  display:flex;align-items:center;justify-content:center;margin:0 auto 22px;
  border:1px solid rgba(239,68,68,0.15);}
.icon-wrap i{font-size:32px;color:var(--red);}
h1{font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;letter-spacing:2px;
  text-transform:uppercase;color:var(--red);margin-bottom:10px;}
.produto-nome{background:var(--bg);border:1px solid var(--border);border-radius:8px;
  padding:10px 16px;font-size:15px;font-weight:600;color:var(--text);margin:14px 0 22px;
  display:inline-block;}
p{font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:28px;}
.divider{height:1px;background:var(--border);margin-bottom:28px;}
.btn-row{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
.btn-primary{padding:10px 24px;background:linear-gradient(135deg,var(--accent),var(--accent2));
  color:white;border:none;border-radius:8px;font-family:'Rajdhani',sans-serif;font-size:13px;
  font-weight:700;letter-spacing:1.5px;text-transform:uppercase;text-decoration:none;
  transition:all 0.2s;display:inline-flex;align-items:center;gap:7px;}
.btn-primary:hover{box-shadow:0 4px 14px rgba(30,111,232,0.35);transform:translateY(-1px);}
.btn-ghost{padding:10px 20px;background:transparent;color:var(--muted);border:1px solid var(--border);
  border-radius:8px;font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:600;
  letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:all 0.2s;
  display:inline-flex;align-items:center;gap:7px;}
.btn-ghost:hover{border-color:rgba(30,111,232,0.3);color:var(--accent);}
footer{position:relative;z-index:1;margin-top:32px;font-size:12px;color:var(--muted);opacity:0.6;}
@keyframes popIn{from{opacity:0;transform:scale(0.94)}to{opacity:1;transform:scale(1)}}
</style>
</head>
<body>
<div class="card">
  <div class="icon-wrap"><i class="bi bi-trash3"></i></div>
  <h1>Produto Eliminado</h1>
  <div class="produto-nome"><?php echo htmlspecialchars($nome_produto); ?></div>
  <p>O produto foi removido permanentemente do sistema e do catálogo de clientes.</p>
  <div class="divider"></div>
  <div class="btn-row">
    <a href="listar.php" class="btn-primary"><i class="bi bi-list-ul"></i> Ver Produtos</a>
    <a href="adicionar.php" class="btn-ghost"><i class="bi bi-plus-lg"></i> Novo Produto</a>
  </div>
</div>
<footer><p>© 2026 AlphaSolutions · Sistema de Gestão</p></footer>
</body>
</html>
