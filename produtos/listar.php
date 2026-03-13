<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin'){
    header("Location: ../acesso.php"); exit;
}
include("../conexao.php");

$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);

$total_produtos = $result->num_rows;
$total_estoque = 0; $total_valor = 0;
$result->data_seek(0);
while($r=$result->fetch_assoc()){
    $total_estoque += $r['quantidade'];
    $total_valor   += $r['preco'] * $r['quantidade'];
}
$result->data_seek(0);
function fmt($v){ return 'MTS '.number_format($v,2,',','.'); }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlphaSolutions - Produtos</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{
  --bg:#f0f4fb;--surface:#fff;--border:#e2e8f4;
  --accent:#1e6fe8;--accent2:#1a5bc4;--text:#0f1f35;
  --muted:#6b82a8;--light:#e8f0fe;--shadow:rgba(30,111,232,0.08);
  --green:#16a34a;--red:#ef4444;--orange:#f59e0b;
}
body{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:radial-gradient(circle,rgba(30,111,232,0.05) 1px,transparent 1px);
  background-size:28px 28px;}
header{position:sticky;top:0;z-index:100;background:var(--surface);
  border-bottom:1px solid var(--border);padding:0 40px;height:62px;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 2px 16px rgba(30,111,232,0.07);}
.logo{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;
  letter-spacing:3px;text-transform:uppercase;color:var(--text);}
.logo span{color:var(--accent);}
.header-right{display:flex;gap:8px;}
.btn-nav{padding:7px 14px;border-radius:7px;font-size:12px;font-weight:600;
  text-decoration:none;border:1px solid rgba(30,111,232,0.15);transition:all 0.2s;
  font-family:'Rajdhani',sans-serif;letter-spacing:1px;text-transform:uppercase;
  display:inline-flex;align-items:center;gap:6px;}
.btn-nav-blue{background:var(--light);color:var(--accent);}
.btn-nav-blue:hover{background:#dbeafe;transform:translateY(-1px);}
.btn-nav-green{background:#dcfce7;color:var(--green);border-color:rgba(22,163,74,0.15);}
.btn-nav-green:hover{background:#bbf7d0;transform:translateY(-1px);}

.main{position:relative;z-index:1;max-width:1200px;margin:0 auto;padding:36px 24px 60px;}
.page-top{display:flex;align-items:flex-start;justify-content:space-between;
  margin-bottom:24px;flex-wrap:wrap;gap:12px;}
.page-title{font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;
  letter-spacing:3px;text-transform:uppercase;}
.page-title span{display:block;font-family:'DM Sans',sans-serif;font-size:13px;
  font-weight:400;text-transform:none;color:var(--muted);margin-top:3px;letter-spacing:0;}

/* stats */
.stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:24px;}
@media(max-width:600px){.stats-row{grid-template-columns:1fr;}}
.stat{background:var(--surface);border:1px solid var(--border);border-radius:10px;
  padding:14px 18px;display:flex;align-items:center;gap:13px;
  box-shadow:0 2px 10px var(--shadow);}
.stat-icon{width:40px;height:40px;background:var(--light);border-radius:8px;
  display:flex;align-items:center;justify-content:center;font-size:18px;color:var(--accent);}
.stat h3{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;color:var(--accent);line-height:1;}
.stat p{font-size:11px;color:var(--muted);margin-top:2px;}

/* toolbar */
.toolbar{background:var(--surface);border:1px solid var(--border);border-radius:10px;
  padding:14px 20px;margin-bottom:20px;display:flex;align-items:center;
  justify-content:space-between;flex-wrap:wrap;gap:10px;
  box-shadow:0 2px 10px var(--shadow);}
.toolbar input{flex:1;min-width:200px;max-width:340px;padding:8px 14px;
  border:1px solid var(--border);border-radius:7px;background:var(--bg);
  font-family:'DM Sans',sans-serif;font-size:13px;color:var(--text);outline:none;}
.toolbar input:focus{border-color:rgba(30,111,232,0.4);}
.toolbar input::placeholder{color:var(--muted);}

/* grid cards */
.prod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:18px;}
.prod-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  overflow:hidden;transition:transform 0.22s,box-shadow 0.22s,border-color 0.22s;
  animation:fadeUp 0.4s ease both;}
.prod-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px var(--shadow);
  border-color:rgba(30,111,232,0.2);}
.prod-card:nth-child(1){animation-delay:.04s}
.prod-card:nth-child(2){animation-delay:.08s}
.prod-card:nth-child(3){animation-delay:.12s}
.prod-card:nth-child(4){animation-delay:.16s}
.prod-card:nth-child(5){animation-delay:.20s}
.prod-card:nth-child(6){animation-delay:.24s}

.prod-img{height:150px;background:var(--bg);border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;}
.prod-img img{width:100%;height:100%;object-fit:contain;padding:12px;transition:transform 0.3s;}
.prod-card:hover .prod-img img{transform:scale(1.04);}
.prod-img .no-img{font-size:36px;color:var(--muted);opacity:0.3;}
.prod-img .stock-pill{position:absolute;top:8px;right:8px;
  padding:2px 8px;border-radius:20px;font-size:10px;font-weight:700;
  font-family:'Rajdhani',sans-serif;letter-spacing:1px;}
.stock-low{background:#fee2e2;color:#dc2626;}
.stock-med{background:#fef3c7;color:#d97706;}
.stock-ok{background:#dcfce7;color:#15803d;}

.prod-body{padding:14px;}
.prod-id{font-size:10px;color:var(--muted);font-weight:600;letter-spacing:1px;
  text-transform:uppercase;margin-bottom:4px;}
.prod-name{font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.prod-desc{font-size:12px;color:var(--muted);line-height:1.4;margin-bottom:10px;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.prod-price{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;
  color:var(--accent);margin-bottom:12px;}
.prod-price small{font-size:11px;font-family:'DM Sans',sans-serif;color:var(--muted);}

.prod-actions{display:flex;gap:6px;}
.btn-edit{flex:1;padding:7px 0;background:var(--light);color:var(--accent);
  border:1px solid rgba(30,111,232,0.15);border-radius:7px;text-decoration:none;
  font-size:12px;font-weight:600;text-align:center;transition:all 0.2s;
  font-family:'Rajdhani',sans-serif;letter-spacing:1px;display:flex;
  align-items:center;justify-content:center;gap:5px;}
.btn-edit:hover{background:#dbeafe;transform:translateY(-1px);}
.btn-del{flex:1;padding:7px 0;background:#fef2f2;color:var(--red);
  border:1px solid rgba(239,68,68,0.15);border-radius:7px;text-decoration:none;
  font-size:12px;font-weight:600;text-align:center;transition:all 0.2s;
  font-family:'Rajdhani',sans-serif;letter-spacing:1px;display:flex;
  align-items:center;justify-content:center;gap:5px;}
.btn-del:hover{background:#fee2e2;transform:translateY(-1px);}

/* empty */
.empty{text-align:center;padding:70px 40px;color:var(--muted);}
.empty i{font-size:48px;opacity:0.25;display:block;margin-bottom:14px;}
.empty h3{font-family:'Rajdhani',sans-serif;font-size:16px;letter-spacing:2px;
  text-transform:uppercase;opacity:0.45;margin-bottom:16px;}
.btn-add-empty{display:inline-flex;align-items:center;gap:7px;padding:10px 22px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));color:white;
  border-radius:8px;font-family:'Rajdhani',sans-serif;font-size:12px;font-weight:700;
  letter-spacing:1.5px;text-transform:uppercase;text-decoration:none;transition:all 0.2s;}
.btn-add-empty:hover{box-shadow:0 4px 14px rgba(30,111,232,0.35);transform:translateY(-1px);}

footer{text-align:center;margin-top:36px;font-size:12px;color:var(--muted);opacity:0.6;}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>

<header>
  <div class="logo">Alpha<span>Solutions</span></div>
  <div class="header-right">
    <a href="adicionar.php" class="btn-nav btn-nav-green"><i class="bi bi-plus-lg"></i> Novo Produto</a>
    <a href="../indexAdmin.php" class="btn-nav btn-nav-blue"><i class="bi bi-arrow-left"></i> Menu</a>
  </div>
</header>

<div class="main">

  <div class="page-top">
    <div class="page-title">Produtos
      <span>Gerencie o catálogo de produtos do sistema</span>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat">
      <div class="stat-icon"><i class="bi bi-boxes"></i></div>
      <div><h3><?php echo $total_produtos; ?></h3><p>Produtos</p></div>
    </div>
    <div class="stat">
      <div class="stat-icon"><i class="bi bi-stack"></i></div>
      <div><h3><?php echo $total_estoque; ?></h3><p>Itens em Estoque</p></div>
    </div>
    <div class="stat">
      <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
      <div><h3><?php echo fmt($total_valor); ?></h3><p>Valor em Estoque</p></div>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="toolbar">
    <input type="text" id="searchInput" placeholder="Pesquisar produto..." oninput="filterCards()">
  </div>

  <!-- Cards -->
  <?php if($total_produtos > 0): ?>
  <div class="prod-grid" id="prodGrid">
    <?php while($row=$result->fetch_assoc()):
      $q = (int)$row['quantidade'];
      $stock_class = $q <= 0 ? 'stock-low' : ($q < 5 ? 'stock-med' : 'stock-ok');
      $stock_label = $q <= 0 ? 'Esgotado' : ($q < 5 ? 'Baixo' : 'OK');
      $img_src = !empty($row['foto']) ? '../uploads/'.htmlspecialchars($row['foto']) : '';
    ?>
    <div class="prod-card" data-name="<?php echo strtolower(htmlspecialchars($row['nome'])); ?>">
      <div class="prod-img">
        <?php if($img_src): ?>
          <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['nome']); ?>">
        <?php else: ?>
          <i class="bi bi-box-seam no-img"></i>
        <?php endif; ?>
        <span class="stock-pill <?php echo $stock_class; ?>"><?php echo $q; ?> un · <?php echo $stock_label; ?></span>
      </div>
      <div class="prod-body">
        <div class="prod-id">#<?php echo str_pad($row['id'],3,'0',STR_PAD_LEFT); ?></div>
        <div class="prod-name" title="<?php echo htmlspecialchars($row['nome']); ?>"><?php echo htmlspecialchars($row['nome']); ?></div>
        <?php if(!empty($row['descricao'])): ?>
        <div class="prod-desc"><?php echo htmlspecialchars($row['descricao']); ?></div>
        <?php endif; ?>
        <div class="prod-price"><small>MTS </small><?php echo number_format($row['preco'],2,',','.'); ?></div>
        <div class="prod-actions">
          <a href="editar.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="bi bi-pencil"></i> Editar</a>
          <a href="apagar.php?id=<?php echo $row['id']; ?>" class="btn-del"
            onclick="return confirm('Apagar <?php echo addslashes(htmlspecialchars($row['nome'])); ?>?')">
            <i class="bi bi-trash3"></i> Apagar</a>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
  <?php else: ?>
  <div class="empty">
    <i class="bi bi-box-seam"></i>
    <h3>Nenhum produto cadastrado</h3>
    <a href="adicionar.php" class="btn-add-empty"><i class="bi bi-plus-lg"></i> Adicionar Produto</a>
  </div>
  <?php endif; ?>

  <footer><p>© 2025 AlphaSolutions · Sistema de Gestão</p></footer>
</div>

<script>
function filterCards(){
  const q=document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.prod-card[data-name]').forEach(c=>{
    c.style.display=c.dataset.name.includes(q)?'':'none';
  });
}
</script>
</body>
</html>
