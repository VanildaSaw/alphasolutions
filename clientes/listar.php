<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin'){
    header("Location: ../acesso.php"); exit;
}
include("../conexao.php");
$sql = "SELECT * FROM usuarios WHERE tipo='cliente' ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlphaSolutions - Clientes</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f0f4fb;--surface:#fff;--border:#e2e8f4;--accent:#1e6fe8;--accent2:#1a5bc4;
  --text:#0f1f35;--muted:#6b82a8;--light:#e8f0fe;--danger:#ef4444;--shadow:rgba(30,111,232,0.08);}
body{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:radial-gradient(circle,rgba(30,111,232,0.05) 1px,transparent 1px);background-size:28px 28px;}
header{position:sticky;top:0;z-index:100;background:var(--surface);border-bottom:1px solid var(--border);
  padding:0 40px;height:62px;display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 2px 16px rgba(30,111,232,0.07);}
.logo{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;letter-spacing:3px;
  text-transform:uppercase;color:var(--text);}
.logo span{color:var(--accent);}
.btn-back{padding:7px 16px;background:var(--light);color:var(--accent);border-radius:7px;
  font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(30,111,232,0.15);
  transition:all 0.2s;font-family:'Rajdhani',sans-serif;letter-spacing:1px;text-transform:uppercase;
  display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:#dbeafe;transform:translateY(-1px);}
.main{position:relative;z-index:1;max-width:1100px;margin:0 auto;padding:40px 24px 60px;}
.page-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:16px;}
.page-title{font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;letter-spacing:3px;text-transform:uppercase;}
.page-title span{display:block;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:400;
  text-transform:none;color:var(--muted);margin-top:2px;letter-spacing:0;}
.stat-badge{display:flex;align-items:center;gap:14px;background:var(--surface);
  border:1px solid var(--border);border-radius:10px;padding:12px 20px;box-shadow:0 2px 10px var(--shadow);}
.stat-icon{width:40px;height:40px;background:var(--light);border-radius:8px;
  display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--accent);}
.stat-info h3{font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;color:var(--accent);line-height:1;}
.stat-info p{font-size:12px;color:var(--muted);margin-top:2px;}
.table-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  overflow:hidden;box-shadow:0 4px 20px var(--shadow);animation:fadeUp 0.4s ease both;}
.table-toolbar{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;}
.table-toolbar input{flex:1;max-width:320px;padding:8px 14px;border:1px solid var(--border);
  border-radius:7px;background:var(--bg);font-family:'DM Sans',sans-serif;font-size:13px;
  color:var(--text);outline:none;}
.table-toolbar input:focus{border-color:rgba(30,111,232,0.4);}
.table-toolbar input::placeholder{color:var(--muted);}
table{width:100%;border-collapse:collapse;}
thead th{background:#f8faff;border-bottom:1px solid var(--border);padding:13px 18px;
  font-family:'Rajdhani',sans-serif;font-size:11px;font-weight:700;letter-spacing:1.5px;
  text-transform:uppercase;color:var(--muted);white-space:nowrap;}
thead th:first-child{padding-left:24px;}
tbody tr{border-bottom:1px solid var(--border);transition:background 0.15s;}
tbody tr:last-child{border-bottom:none;}
tbody tr:hover{background:#f8faff;}
tbody td{padding:14px 18px;font-size:14px;vertical-align:middle;}
tbody td:first-child{padding-left:24px;}
.badge-id{display:inline-block;padding:3px 10px;background:var(--light);color:var(--accent);
  border-radius:20px;font-family:'Rajdhani',sans-serif;font-size:12px;font-weight:700;letter-spacing:1px;}
.td-name{font-weight:600;}
.td-meta{font-size:13px;color:var(--muted);}
.email-cell{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);}
.email-cell i{font-size:13px;color:var(--accent);opacity:0.6;}
.empty-state{text-align:center;padding:72px 40px;color:var(--muted);}
.empty-state i{font-size:44px;opacity:0.25;display:block;margin-bottom:14px;color:var(--accent);}
.empty-state h3{font-family:'Rajdhani',sans-serif;font-size:17px;font-weight:700;
  letter-spacing:2px;text-transform:uppercase;opacity:0.45;}
footer{text-align:center;margin-top:40px;font-size:12px;color:var(--muted);opacity:0.6;}
@keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<header>
  <div class="logo">Alpha<span>Solutions</span></div>
  <a href="../indexAdmin.php" class="btn-back"><i class="bi bi-arrow-left"></i> Voltar</a>
</header>
<div class="main">
  <div class="page-top">
    <div class="page-title">Clientes<span>Clientes registados no sistema</span></div>
    <div class="stat-badge">
      <div class="stat-icon"><i class="bi bi-people"></i></div>
      <div class="stat-info">
        <h3><?php echo $result ? $result->num_rows : 0; ?></h3>
        <p>Total de Clientes</p>
      </div>
    </div>
  </div>
  <div class="table-card">
    <div class="table-toolbar">
      <input type="text" id="searchInput" placeholder="Pesquisar cliente..." oninput="filterRows()">
    </div>
    <?php if($result && $result->num_rows > 0): ?>
    <table id="clientTable">
      <thead>
        <tr><th>ID</th><th>Nome</th><th>Sobrenome</th><th>Email</th><th>Tipo</th></tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><span class="badge-id">#<?php echo str_pad($row['id'],3,'0',STR_PAD_LEFT); ?></span></td>
          <td class="td-name"><?php echo htmlspecialchars($row['nome']); ?></td>
          <td class="td-meta"><?php echo htmlspecialchars($row['sobrenome'] ?? ''); ?></td>
          <td><div class="email-cell"><i class="bi bi-envelope"></i><?php echo htmlspecialchars($row['email']); ?></div></td>
          <td><span class="badge-id"><?php echo htmlspecialchars($row['tipo']); ?></span></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
      <i class="bi bi-people"></i>
      <h3>Nenhum cliente cadastrado</h3>
    </div>
    <?php endif; ?>
  </div>
  <footer><p>© 2026 AlphaSolutions · Sistema de Gestão</p></footer>
</div>
<script>
function filterRows(){
  const q=document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('#clientTable tbody tr').forEach(r=>{
    r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
  });
}
</script>
</body>
</html>
