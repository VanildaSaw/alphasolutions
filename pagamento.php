<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location: acesso.php"); exit; }
include("conexao.php");

$produto_id = isset($_GET['produto_id']) ? (int)$_GET['produto_id'] : 0;
if(!$produto_id){ header("Location: produtos.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM produtos WHERE id=? AND quantidade>0");
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();
if(!$produto){ header("Location: produtos.php"); exit; }

$cliente_id = $_SESSION['user']['id'];
$success = false;
$error = '';
$codigo_vendedor = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $numero_cartao   = preg_replace('/\s+/','',$_POST['numero_cartao'] ?? '');
    $validade        = $_POST['validade'] ?? '';
    $cvv             = $_POST['cvv'] ?? '';
    $nome_cartao     = trim($_POST['nome_cartao'] ?? '');
    $quantidade      = max(1,(int)($_POST['quantidade'] ?? 1));
    $codigo_vendedor = strtoupper(trim($_POST['codigo_vendedor'] ?? ''));

    $vendedor_id = null;
    $vendedor_codigo_db = null;

    if(strlen($numero_cartao) < 16)         { $error = 'Número de cartão inválido.'; }
    elseif(!preg_match('/^\d{2}\/\d{2}$/',$validade)){ $error = 'Validade inválida. Use MM/AA.'; }
    elseif(strlen($cvv) < 3)                { $error = 'CVV inválido.'; }
    elseif(empty($nome_cartao))             { $error = 'Nome no cartão é obrigatório.'; }
    elseif($quantidade > $produto['quantidade']){ $error = 'Quantidade superior ao estoque disponível.'; }
    else {
        // Se o cliente informou um código de consultor, validar no banco
        if($codigo_vendedor !== ''){
            $stmtVend = $conn->prepare("SELECT id, codigo FROM vendedores WHERE codigo = ? AND ativo = 1");
            $stmtVend->bind_param("s", $codigo_vendedor);
            $stmtVend->execute();
            $resVend = $stmtVend->get_result();
            if($resVend->num_rows === 0){
                $error = 'Código de consultor inválido.';
            } else {
                $vend = $resVend->fetch_assoc();
                $vendedor_id = (int)$vend['id'];
                $vendedor_codigo_db = $vend['codigo'];
            }
            $stmtVend->close();
        }

        if(!$error){
            $total = $quantidade * (float)$produto['preco'];
            $comissao_percentual = 5.0;
            $comissao_valor = $total * ($comissao_percentual / 100);

            $stmt2 = $conn->prepare("INSERT INTO vendas (cliente_id, produto_id, quantidade, data_venda, total_venda, vendedor_id, vendedor_codigo, comissao_percentual, comissao_valor) VALUES (?,?,?,?,?,?,?,?,?)");
            $data_venda = date('Y-m-d H:i:s');
            $stmt2->bind_param(
                "iiisdisdd",
                $cliente_id,            // i
                $produto_id,            // i
                $quantidade,            // i
                $data_venda,            // s
                $total,                 // d
                $vendedor_id,           // i
                $vendedor_codigo_db,    // s
                $comissao_percentual,   // d
                $comissao_valor         // d
            );

            if($stmt2->execute()){
                $novo_estoque = $produto['quantidade'] - $quantidade;
                $stmt3 = $conn->prepare("UPDATE produtos SET quantidade=? WHERE id=?");
                $stmt3->bind_param("ii", $novo_estoque, $produto_id);
                $stmt3->execute();
                $success = true;
            } else {
                $error = 'Erro ao registar venda. Tente novamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlphaSolutions - Pagamento</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f0f4fb;--surface:#fff;--border:#e2e8f4;--accent:#1e6fe8;--accent2:#1a5bc4;
  --text:#0f1f35;--muted:#6b82a8;--light:#e8f0fe;--shadow:rgba(30,111,232,0.08);
  --green:#16a34a;--green-light:#dcfce7;}
body{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:radial-gradient(circle,rgba(30,111,232,0.05) 1px,transparent 1px);background-size:28px 28px;}
header{position:sticky;top:0;z-index:100;background:var(--surface);border-bottom:1px solid var(--border);
  padding:0 40px;height:62px;display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 2px 16px rgba(30,111,232,0.07);}
.logo{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--text);}
.logo span{color:var(--accent);}
.btn-back{padding:7px 16px;background:var(--light);color:var(--accent);border-radius:7px;font-size:12px;
  font-weight:600;text-decoration:none;border:1px solid rgba(30,111,232,0.15);transition:all 0.2s;
  font-family:'Rajdhani',sans-serif;letter-spacing:1px;text-transform:uppercase;
  display:inline-flex;align-items:center;gap:6px;}
.btn-back:hover{background:#dbeafe;transform:translateY(-1px);}
.main{position:relative;z-index:1;max-width:860px;margin:0 auto;padding:40px 24px 60px;
  display:grid;grid-template-columns:1fr 340px;gap:28px;align-items:start;}
@media(max-width:700px){.main{grid-template-columns:1fr;}}
.product-summary{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  overflow:hidden;box-shadow:0 4px 20px var(--shadow);animation:fadeUp 0.4s ease both;}
.prod-img{height:180px;background:var(--bg);display:flex;align-items:center;
  justify-content:center;border-bottom:1px solid var(--border);}
.prod-img img{max-height:100%;max-width:100%;object-fit:contain;padding:16px;}
.prod-img .no-img{font-size:44px;opacity:0.2;color:var(--muted);}
.prod-info{padding:20px;}
.prod-tag{display:inline-block;padding:2px 8px;background:var(--light);color:var(--accent);
  border-radius:4px;font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;}
.prod-info h3{font-size:16px;font-weight:700;margin-bottom:6px;}
.prod-info p{font-size:13px;color:var(--muted);line-height:1.5;margin-bottom:12px;}
.prod-price{font-family:'Rajdhani',sans-serif;font-size:24px;font-weight:700;color:var(--accent);}
.prod-price small{font-size:13px;font-family:'DM Sans',sans-serif;color:var(--muted);}
.prod-stock{font-size:12px;color:var(--muted);margin-top:6px;display:flex;align-items:center;gap:5px;}
.prod-stock i{font-size:12px;color:var(--accent);opacity:0.6;}
.pay-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  overflow:hidden;box-shadow:0 4px 20px var(--shadow);animation:fadeUp 0.4s 0.1s ease both;}
.pay-header{background:linear-gradient(135deg,var(--accent),var(--accent2));padding:20px 24px;color:white;}
.pay-header h2{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;
  letter-spacing:2px;text-transform:uppercase;display:flex;align-items:center;gap:8px;}
.pay-header p{font-size:12px;opacity:0.8;margin-top:3px;}
.card-visual{margin:20px 24px;background:linear-gradient(135deg,#1e3a5f,#2c5f8a);
  border-radius:14px;padding:20px;color:white;position:relative;overflow:hidden;}
.card-visual::before{content:'';position:absolute;top:-30px;right:-30px;width:120px;height:120px;
  border-radius:50%;background:rgba(255,255,255,0.05);}
.card-visual::after{content:'';position:absolute;bottom:-20px;left:60px;width:80px;height:80px;
  border-radius:50%;background:rgba(255,255,255,0.04);}
.card-chip{width:36px;height:28px;background:linear-gradient(135deg,#d4a017,#f0c040);
  border-radius:5px;margin-bottom:16px;}
.card-number-display{font-family:'Rajdhani',sans-serif;font-size:18px;letter-spacing:3px;
  margin-bottom:14px;opacity:0.9;}
.card-bottom{display:flex;justify-content:space-between;font-size:11px;opacity:0.7;}
.card-bottom span{display:flex;flex-direction:column;gap:2px;}
.card-bottom strong{font-size:13px;letter-spacing:1px;opacity:1;}
.pay-body{padding:20px 24px;}
.field{margin-bottom:16px;}
.field label{display:flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:var(--muted);
  letter-spacing:0.5px;text-transform:uppercase;margin-bottom:5px;}
.field label i{font-size:12px;color:var(--accent);}
.field input{width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;
  background:var(--bg);font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text);
  outline:none;transition:border-color 0.2s;}
.field input:focus{border-color:rgba(30,111,232,0.5);box-shadow:0 0 0 3px rgba(30,111,232,0.07);}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.qty-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;}
.total-box{background:var(--bg);border:1px solid var(--border);border-radius:8px;
  padding:12px 16px;display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
.total-box .label{font-size:11px;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:0.5px;}
.total-box .value{font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;color:var(--accent);}
.btn-pay{width:100%;padding:13px;background:linear-gradient(135deg,var(--green),#0f7a38);
  color:white;border:none;border-radius:8px;font-family:'Rajdhani',sans-serif;font-size:14px;
  font-weight:700;letter-spacing:2px;text-transform:uppercase;cursor:pointer;transition:all 0.2s;
  display:flex;align-items:center;justify-content:center;gap:8px;}
.btn-pay:hover{box-shadow:0 4px 16px rgba(22,163,74,0.35);transform:translateY(-1px);}
.msg-error{background:#fef2f2;border:1px solid rgba(239,68,68,0.2);color:#dc2626;
  padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;
  display:flex;align-items:center;gap:7px;}
.secure{display:flex;align-items:center;justify-content:center;gap:5px;
  margin-top:12px;font-size:11px;color:var(--muted);}
.success-screen{position:fixed;inset:0;background:var(--bg);z-index:200;
  display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px;text-align:center;}
.success-icon{width:80px;height:80px;background:var(--green-light);border-radius:50%;
  display:flex;align-items:center;justify-content:center;margin-bottom:24px;animation:popIn 0.4s ease;}
.success-icon i{font-size:38px;color:var(--green);}
.success-screen h2{font-family:'Rajdhani',sans-serif;font-size:26px;font-weight:700;
  letter-spacing:2px;color:var(--green);margin-bottom:10px;}
.success-screen p{font-size:14px;color:var(--muted);max-width:340px;line-height:1.6;margin-bottom:24px;}
.btn-voltar{padding:10px 28px;background:linear-gradient(135deg,var(--accent),var(--accent2));
  color:white;border-radius:8px;text-decoration:none;font-family:'Rajdhani',sans-serif;
  font-size:13px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
  transition:all 0.2s;display:inline-flex;align-items:center;gap:7px;}
.btn-voltar:hover{box-shadow:0 4px 14px rgba(30,111,232,0.35);transform:translateY(-1px);}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
@keyframes popIn{from{transform:scale(0)}to{transform:scale(1)}}
</style>
</head>
<body>

<?php if($success): ?>
<div class="success-screen">
  <div class="success-icon"><i class="bi bi-check-lg"></i></div>
  <h2>Pagamento Aprovado!</h2>
  <p>A sua compra de <strong><?php echo htmlspecialchars($produto['nome']); ?></strong> foi registada com sucesso.</p>
  <a href="produtos.php" class="btn-voltar"><i class="bi bi-arrow-left"></i> Ver mais produtos</a>
</div>
<?php else: ?>

<header>
  <div class="logo">Alpha<span>Solutions</span></div>
  <a href="produtos.php" class="btn-back"><i class="bi bi-arrow-left"></i> Produtos</a>
</header>

<div class="main">
  <div class="product-summary">
    <div class="prod-img">
      <?php if(!empty($produto['foto'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($produto['foto']); ?>" alt="">
      <?php else: ?>
        <i class="bi bi-box-seam no-img"></i>
      <?php endif; ?>
    </div>
    <div class="prod-info">
      <span class="prod-tag">Produto</span>
      <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
      <p><?php echo htmlspecialchars($produto['descricao'] ?? 'Sem descrição.'); ?></p>
      <div class="prod-price"><small>MTS </small><?php echo number_format($produto['preco'],2,',','.'); ?></div>
      <div class="prod-stock"><i class="bi bi-stack"></i> Estoque: <?php echo (int)$produto['quantidade']; ?> un.</div>
    </div>
  </div>

  <div class="pay-card">
    <div class="pay-header">
      <h2><i class="bi bi-credit-card"></i> Pagamento</h2>
      <p>Simulação de pagamento por cartão</p>
    </div>
    <div class="card-visual">
      <div class="card-chip"></div>
      <div class="card-number-display" id="cardDisplay">•••• •••• •••• ••••</div>
      <div class="card-bottom">
        <span>TITULAR<strong id="nameDisplay">NOME NO CARTÃO</strong></span>
        <span>VALIDADE<strong id="validDisplay">MM/AA</strong></span>
      </div>
    </div>
    <div class="pay-body">
      <?php if($error): ?>
      <div class="msg-error"><i class="bi bi-exclamation-triangle"></i><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="field">
          <label><i class="bi bi-person-badge"></i> Você foi atendido por algum consultor? Digite o código aqui</label>
          <input type="text" name="codigo_vendedor" placeholder="Ex: VEND-101"
            value="<?php echo htmlspecialchars($codigo_vendedor); ?>">
        </div>
        <div class="field">
          <label><i class="bi bi-credit-card"></i> Número do Cartão</label>
          <input type="text" name="numero_cartao" maxlength="19" placeholder="1234 5678 9012 3456"
            oninput="formatCard(this)" required>
        </div>
        <div class="field">
          <label><i class="bi bi-person"></i> Nome no Cartão</label>
          <input type="text" name="nome_cartao" placeholder="NOME COMPLETO"
            oninput="document.getElementById('nameDisplay').textContent=this.value.toUpperCase()||'NOME NO CARTÃO'" required>
        </div>
        <div class="field-row">
          <div class="field" style="margin:0">
            <label><i class="bi bi-calendar3"></i> Validade</label>
            <input type="text" name="validade" maxlength="5" placeholder="MM/AA"
              oninput="formatValidade(this)" required>
          </div>
          <div class="field" style="margin:0">
            <label><i class="bi bi-lock"></i> CVV</label>
            <input type="text" name="cvv" maxlength="4" placeholder="•••" required>
          </div>
        </div>
        <div class="qty-row">
          <div class="field" style="margin:0">
            <label><i class="bi bi-stack"></i> Quantidade</label>
            <input type="number" name="quantidade" id="qtdInput" min="1"
              max="<?php echo $produto['quantidade']; ?>" value="1" oninput="calcTotal()" required>
          </div>
          <div></div>
        </div>
        <div class="total-box">
          <span class="label">Total a Pagar</span>
          <span class="value" id="totalDisplay">MTS <?php echo number_format($produto['preco'],2,',','.'); ?></span>
        </div>
        <button type="submit" class="btn-pay"><i class="bi bi-shield-check"></i> Confirmar Pagamento</button>
        <div class="secure"><i class="bi bi-lock-fill"></i> Pagamento simulado · Dados não são processados</div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
const preco=<?php echo $produto['preco']; ?>;
function formatCard(el){
  let v=el.value.replace(/\D/g,'').substring(0,16);
  el.value=v.match(/.{1,4}/g)?.join(' ')||v;
  const d=v.padEnd(16,'•').match(/.{1,4}/g).join(' ');
  document.getElementById('cardDisplay').textContent=d;
}
function formatValidade(el){
  let v=el.value.replace(/\D/g,'');
  if(v.length>=2)v=v.substring(0,2)+'/'+v.substring(2,4);
  el.value=v;
  document.getElementById('validDisplay').textContent=v||'MM/AA';
}
function calcTotal(){
  const qty=parseInt(document.getElementById('qtdInput').value)||1;
  const total=(preco*qty).toFixed(2).replace('.',',').replace(/\B(?=(\d{3})+(?!\d))/g,'.');
  document.getElementById('totalDisplay').textContent='MTS '+total;
}
</script>
</body>
</html>
