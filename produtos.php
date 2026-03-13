<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location: acesso.php"); exit; }
include("conexao.php");
$sql = "SELECT * FROM produtos WHERE quantidade > 0 ORDER BY id DESC";
$result = $conn->query($sql);
$nome_cliente = htmlspecialchars($_SESSION['user']['nome']);
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
:root{--bg:#f0f4fb;--surface:#fff;--border:#e2e8f4;--accent:#1e6fe8;--accent2:#1a5bc4;
  --text:#0f1f35;--muted:#6b82a8;--light:#e8f0fe;--shadow:rgba(30,111,232,0.08);}
body{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:radial-gradient(circle,rgba(30,111,232,0.06) 1px,transparent 1px);background-size:28px 28px;}
header{position:sticky;top:0;z-index:100;background:var(--surface);border-bottom:1px solid var(--border);
  padding:0 40px;height:62px;display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 2px 16px rgba(30,111,232,0.07);}
.logo{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--text);}
.logo span{color:var(--accent);}
nav{display:flex;align-items:center;gap:10px;}
.nav-user{font-size:13px;color:var(--muted);}
.nav-user strong{color:var(--text);}
.btn-logout{padding:6px 14px;border-radius:6px;border:1px solid var(--border);background:transparent;
  color:var(--muted);font-size:13px;text-decoration:none;transition:all 0.2s;
  display:inline-flex;align-items:center;gap:5px;}
.btn-logout:hover{border-color:#fca5a5;color:#ef4444;}
.search-wrap{position:relative;z-index:1;background:var(--surface);border-bottom:1px solid var(--border);
  padding:14px 40px;display:flex;align-items:center;gap:12px;}
.search-wrap .s-icon{position:absolute;left:55px;font-size:14px;color:var(--muted);
  pointer-events:none;top:50%;transform:translateY(-50%);}
.search-wrap input{flex:1;max-width:480px;padding:9px 14px 9px 36px;border:1px solid var(--border);
  border-radius:8px;background:var(--bg);font-family:'DM Sans',sans-serif;font-size:14px;
  color:var(--text);outline:none;transition:border-color 0.2s;}
.search-wrap input:focus{border-color:rgba(30,111,232,0.4);}
.search-wrap input::placeholder{color:var(--muted);}
.main{position:relative;z-index:1;padding:32px 40px 60px;max-width:1300px;margin:0 auto;}
.section-title{font-family:'Rajdhani',sans-serif;font-size:20px;font-weight:700;
  letter-spacing:2px;text-transform:uppercase;margin-bottom:24px;}
.section-title span{font-size:13px;font-weight:400;letter-spacing:0.5px;color:var(--muted);
  margin-left:8px;text-transform:none;font-family:'DM Sans',sans-serif;}
.products-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;}
.product{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  overflow:hidden;display:flex;flex-direction:column;
  transition:transform 0.22s,box-shadow 0.22s,border-color 0.22s;animation:fadeUp 0.4s ease both;}
.product:hover{transform:translateY(-4px);box-shadow:0 12px 32px var(--shadow);border-color:rgba(30,111,232,0.2);}
.product-img{background:var(--bg);height:155px;display:flex;align-items:center;
  justify-content:center;border-bottom:1px solid var(--border);overflow:hidden;}
.product-img img{width:100%;height:100%;object-fit:contain;padding:14px;transition:transform 0.3s;}
.product:hover .product-img img{transform:scale(1.04);}
.product-img .no-img{font-size:36px;opacity:0.2;color:var(--muted);}
.product-body{padding:14px;flex:1;display:flex;flex-direction:column;}
.product-tag{display:inline-block;padding:2px 8px;background:var(--light);color:var(--accent);
  border-radius:4px;font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:7px;}
.product h3{font-size:14px;font-weight:600;margin-bottom:5px;line-height:1.4;}
.product p{font-size:12px;color:var(--muted);line-height:1.5;flex:1;margin-bottom:12px;}
.product-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:auto;}
.product-price{font-family:'Rajdhani',sans-serif;font-size:19px;font-weight:700;color:var(--accent);}
.product-price small{font-size:11px;font-weight:500;color:var(--muted);font-family:'DM Sans',sans-serif;}
.stock{font-size:11px;color:var(--muted);margin-bottom:8px;display:flex;align-items:center;gap:4px;}
.stock i{font-size:11px;color:var(--accent);opacity:0.6;}
.btn-buy{padding:7px 14px;background:linear-gradient(135deg,var(--accent),var(--accent2));color:white;
  border:none;border-radius:7px;font-family:'Rajdhani',sans-serif;font-size:12px;font-weight:700;
  letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;
  white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;gap:5px;}
.btn-buy:hover{box-shadow:0 4px 14px rgba(30,111,232,0.35);transform:translateY(-1px);}
.empty{grid-column:1/-1;text-align:center;padding:80px 40px;color:var(--muted);}
.empty i{font-size:44px;opacity:0.2;display:block;margin-bottom:16px;color:var(--accent);}
.empty h2{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;
  letter-spacing:2px;text-transform:uppercase;margin-bottom:8px;opacity:0.5;}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.products-grid .product:nth-child(1){animation-delay:0.04s}
.products-grid .product:nth-child(2){animation-delay:0.08s}
.products-grid .product:nth-child(3){animation-delay:0.12s}
.products-grid .product:nth-child(4){animation-delay:0.16s}
.products-grid .product:nth-child(5){animation-delay:0.20s}
.products-grid .product:nth-child(6){animation-delay:0.24s}
</style>
</head>
<body>
<header>
  <div class="logo">Alpha<span>Solutions</span></div>
  <nav>
    <span class="nav-user">Olá, <strong><?php echo $nome_cliente; ?></strong></span>
    <a href="logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Sair</a>
  </nav>
</header>
<div class="search-wrap">
  <i class="bi bi-search s-icon"></i>
  <input type="text" id="searchInput" placeholder="Pesquisar produtos..." oninput="filterProducts()">
</div>
<div class="main">
  <h2 class="section-title">Produtos <span id="countLabel"></span></h2>
  <div class="products-grid" id="productsGrid">
    <?php if($result && $result->num_rows > 0):
      $count = $result->num_rows;
      while($row = $result->fetch_assoc()):
        $img_src = !empty($row['foto']) ? 'uploads/'.htmlspecialchars($row['foto']) : '';
    ?>
    <div class="product" data-name="<?php echo strtolower(htmlspecialchars($row['nome'])); ?>">
      <div class="product-img">
        <?php if($img_src): ?>
          <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($row['nome']); ?>">
        <?php else: ?>
          <i class="bi bi-box-seam no-img"></i>
        <?php endif; ?>
      </div>
      <div class="product-body">
        <span class="product-tag">Produto</span>
        <h3><?php echo htmlspecialchars($row['nome']); ?></h3>
        <p><?php echo htmlspecialchars($row['descricao'] ?? ''); ?></p>
        <div class="stock"><i class="bi bi-stack"></i> Estoque: <?php echo (int)$row['quantidade']; ?> un.</div>
        <div class="product-footer">
          <div class="product-price"><small>MTS </small><?php echo number_format($row['preco'],2,',','.'); ?></div>
          <a href="pagamento.php?produto_id=<?php echo $row['id']; ?>" class="btn-buy"><i class="bi bi-cart-plus"></i> Comprar</a>
        </div>
      </div>
    </div>
    <?php endwhile; else: ?>
    <div class="empty">
      <i class="bi bi-box-seam"></i>
      <h2>Nenhum produto disponível</h2>
      <p>Os produtos aparecerão aqui quando forem adicionados ao sistema.</p>
    </div>
    <?php endif; ?>
  </div>
</div>
<script>
<?php if(isset($count)): ?>
document.getElementById('countLabel').textContent='<?php echo $count; ?> produto(s)';
<?php endif; ?>
function filterProducts(){
  const q=document.getElementById('searchInput').value.toLowerCase();
  let v=0;
  document.querySelectorAll('.product[data-name]').forEach(c=>{
    const show=c.dataset.name.includes(q);
    c.style.display=show?'':'none';
    if(show)v++;
  });
  document.getElementById('countLabel').textContent=v+' produto(s)';
}
</script>
</body>
</html>
