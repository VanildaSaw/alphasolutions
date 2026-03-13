<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin'){
    header("Location: ../acesso.php"); exit;
}
include("../conexao.php");
$sql = "SELECT vendas.id, usuarios.nome AS cliente, usuarios.email,
    produtos.nome AS produto, produtos.preco, vendas.quantidade,
    (vendas.quantidade * produtos.preco) AS total, vendas.data_venda
FROM vendas
INNER JOIN usuarios ON vendas.cliente_id = usuarios.id
INNER JOIN produtos ON vendas.produto_id = produtos.id
ORDER BY vendas.data_venda DESC";
$result = $conn->query($sql);
if(!$result) die("Erro: ".$conn->error);
$total_vendas=$result->num_rows; $faturamento=0; $itens=0;
$result->data_seek(0);
while($r=$result->fetch_assoc()){ $faturamento+=$r['total']; $itens+=$r['quantidade']; }
$result->data_seek(0);
function fmt($v){ return 'MTS '.number_format($v,2,',','.'); }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlphaSolutions - Vendas</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f0f4fb;--surface:#fff;--border:#e2e8f4;--accent:#1e6fe8;--accent2:#1a5bc4;
  --text:#0f1f35;--muted:#6b82a8;--light:#e8f0fe;--shadow:rgba(30,111,232,0.08);--green:#16a34a;}
body{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:radial-gradient(circle,rgba(30,111,232,0.05) 1px,transparent 1px);background-size:28px 28px;}
header{position:sticky;top:0;z-index:100;background:var(--surface);border-bottom:1px solid var(--border);
  padding:0 32px;height:62px;display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 2px 16px rgba(30,111,232,0.07);}
.logo{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--text);}
.logo span{color:var(--accent);}
.header-right{display:flex;gap:8px;}
.btn-nav{padding:7px 14px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;
  border:1px solid var(--border);transition:all 0.2s;font-family:'Rajdhani',sans-serif;
  letter-spacing:1px;text-transform:uppercase;display:inline-flex;align-items:center;gap:6px;}
.btn-nav-blue{background:var(--light);color:var(--accent);border-color:rgba(30,111,232,0.15);}
.btn-nav-blue:hover{background:#dbeafe;transform:translateY(-1px);}
.btn-nav-red{background:#fef2f2;color:#dc2626;border-color:rgba(239,68,68,0.15);}
.btn-nav-red:hover{background:#fee2e2;transform:translateY(-1px);}
.main{position:relative;z-index:1;max-width:1200px;margin:0 auto;padding:36px 24px 60px;}
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px;}
@media(max-width:600px){.stats-row{grid-template-columns:1fr;}}
.stat{background:var(--surface);border:1px solid var(--border);border-radius:10px;
  padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 10px var(--shadow);}
.stat-icon{width:42px;height:42px;border-radius:8px;background:var(--light);
  display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--accent);}
.stat-info h3{font-family:'Rajdhani',sans-serif;font-size:20px;font-weight:700;color:var(--accent);}
.stat-info p{font-size:11px;color:var(--muted);margin-top:2px;}
.page-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;}
.page-title{font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;letter-spacing:3px;text-transform:uppercase;}
.table-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  overflow:hidden;box-shadow:0 4px 20px var(--shadow);animation:fadeUp 0.4s ease both;}
.table-toolbar{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px;}
.table-toolbar input{flex:1;max-width:320px;padding:8px 14px;border:1px solid var(--border);
  border-radius:7px;background:var(--bg);font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);outline:none;}
.table-toolbar input:focus{border-color:rgba(30,111,232,0.4);}
.table-toolbar input::placeholder{color:var(--muted);}
.tbl{width:100%;border-collapse:collapse;}
.tbl thead th{background:#f8faff;border-bottom:1px solid var(--border);padding:12px 16px;
  font-family:'Rajdhani',sans-serif;font-size:11px;font-weight:700;letter-spacing:1.5px;
  text-transform:uppercase;color:var(--muted);white-space:nowrap;}
.tbl tbody tr{border-bottom:1px solid var(--border);transition:background 0.15s;}
.tbl tbody tr:last-child{border-bottom:none;}
.tbl tbody tr:hover{background:#f8faff;}
.tbl td{padding:13px 16px;font-size:13px;vertical-align:middle;}
.badge-id{display:inline-block;padding:3px 9px;background:var(--light);color:var(--accent);
  border-radius:20px;font-family:'Rajdhani',sans-serif;font-size:11px;font-weight:700;}
.client-name{font-weight:600;}
.client-email{font-size:11px;color:var(--muted);display:flex;align-items:center;gap:4px;margin-top:2px;}
.client-email i{font-size:10px;opacity:0.6;}
.price-badge{display:inline-block;padding:3px 10px;background:#dcfce7;color:#15803d;
  border-radius:6px;font-family:'Rajdhani',sans-serif;font-size:12px;font-weight:700;}
.total-badge{display:inline-block;padding:3px 10px;background:var(--light);color:var(--accent);
  border-radius:6px;font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:700;}
.qty-badge{display:inline-block;padding:3px 10px;background:#ede9fe;color:#7c3aed;
  border-radius:6px;font-family:'Rajdhani',sans-serif;font-size:12px;font-weight:700;}
.empty-state{text-align:center;padding:70px 40px;color:var(--muted);}
.empty-state i{font-size:44px;opacity:0.2;display:block;margin-bottom:14px;color:var(--accent);}
.empty-state h3{font-family:'Rajdhani',sans-serif;font-size:16px;letter-spacing:2px;text-transform:uppercase;opacity:0.4;}
footer{text-align:center;margin-top:40px;font-size:12px;color:var(--muted);opacity:0.6;}
@keyframes fadeUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<header>
  <div class="logo">Alpha<span>Solutions</span></div>
  <div class="header-right">
    <a href="relatorio_vendas.php" class="btn-nav btn-nav-blue"><i class="bi bi-bar-chart-line"></i> Relatório</a>
    <a href="gerar_pdf.php" class="btn-nav btn-nav-red"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
    <a href="../indexAdmin.php" class="btn-nav btn-nav-blue"><i class="bi bi-arrow-left"></i> Voltar</a>
  </div>
</header>
<div class="main">
  <div class="stats-row">
    <div class="stat">
      <div class="stat-icon"><i class="bi bi-receipt"></i></div>
      <div class="stat-info"><h3><?php echo $total_vendas; ?></h3><p>Total de Vendas</p></div>
    </div>
    <div class="stat">
      <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
      <div class="stat-info"><h3><?php echo fmt($faturamento); ?></h3><p>Faturamento Total</p></div>
    </div>
    <div class="stat">
      <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
      <div class="stat-info"><h3><?php echo $itens; ?></h3><p>Itens Vendidos</p></div>
    </div>
  </div>
  <div class="page-top">
    <div class="page-title">Lista de Vendas</div>
  </div>
  <div class="table-card">
    <div class="table-toolbar">
      <input type="text" id="searchInput" placeholder="Pesquisar por cliente ou produto..." oninput="filterRows()">
    </div>
    <?php if($total_vendas > 0): ?>
    <div style="overflow-x:auto">
    <table class="tbl" id="vendasTable">
      <thead>
        <tr><th>ID</th><th>Cliente</th><th>Produto</th><th>Preço Unit.</th><th>Qtd</th><th>Total</th><th>Data</th></tr>
      </thead>
      <tbody>
        <?php while($row=$result->fetch_assoc()): $dt=new DateTime($row['data_venda']); ?>
        <tr>
          <td><span class="badge-id">#<?php echo str_pad($row['id'],4,'0',STR_PAD_LEFT); ?></span></td>
          <td>
            <div class="client-name"><?php echo htmlspecialchars($row['cliente']); ?></div>
            <div class="client-email"><i class="bi bi-envelope"></i><?php echo htmlspecialchars($row['email']); ?></div>
          </td>
          <td><?php echo htmlspecialchars($row['produto']); ?></td>
          <td><span class="price-badge"><?php echo fmt($row['preco']); ?></span></td>
          <td><span class="qty-badge"><?php echo $row['quantidade']; ?></span></td>
          <td><span class="total-badge"><?php echo fmt($row['total']); ?></span></td>
          <td><?php echo $dt->format('d/m/Y').'<br><small style="color:var(--muted)">'.$dt->format('H:i').'</small>'; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    </div>
    <?php else: ?>
    <div class="empty-state"><i class="bi bi-cart-x"></i><h3>Nenhuma venda registada</h3></div>
    <?php endif; ?>
  </div>
  <footer><p>© 2026 AlphaSolutions · Sistema de Gestão</p></footer>
</div>
<script>
function filterRows(){
  const q=document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('#vendasTable tbody tr').forEach(r=>{
    r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
  });
}
</script>
</body>
</html>
