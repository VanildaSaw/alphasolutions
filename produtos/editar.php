<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin'){
    header("Location: ../acesso.php"); exit;
}
include("../conexao.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!$id){ header("Location: listar.php"); exit; }

$stmt = $conn->prepare("SELECT * FROM produtos WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$produto = $stmt->get_result()->fetch_assoc();
if(!$produto){ header("Location: listar.php"); exit; }

$sucesso = false;
$erro = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $nome      = trim($_POST['nome'] ?? '');
    $preco     = floatval($_POST['preco'] ?? 0);
    $quantidade= intval($_POST['quantidade'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');

    if(empty($nome))  { $erro = 'O nome do produto é obrigatório.'; }
    elseif($preco<=0) { $erro = 'O preço deve ser maior que zero.'; }
    elseif($quantidade<0){ $erro = 'A quantidade não pode ser negativa.'; }
    else {
        $nova_foto = $produto['foto'];

        if(!empty($_FILES['foto']['name'])){
            $pasta = "../uploads/";
            if(!is_dir($pasta)) mkdir($pasta, 0777, true);
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if(!in_array($ext, ['jpg','jpeg','png','gif','webp'])){
                $erro = 'Formato não permitido. Use JPG, PNG, GIF ou WEBP.';
            } elseif($_FILES['foto']['size'] > 5*1024*1024){
                $erro = 'A imagem não pode ter mais de 5MB.';
            } else {
                $novo_nome = uniqid('prod_', true).'.'.$ext;
                if(move_uploaded_file($_FILES['foto']['tmp_name'], $pasta.$novo_nome)){
                    if(!empty($produto['foto']) && file_exists($pasta.$produto['foto'])){
                        unlink($pasta.$produto['foto']);
                    }
                    $nova_foto = $novo_nome;
                } else { $erro = 'Falha no upload da imagem.'; }
            }
        }

        if(empty($erro)){
            $stmt2 = $conn->prepare("UPDATE produtos SET nome=?, preco=?, quantidade=?, descricao=?, foto=? WHERE id=?");
            $stmt2->bind_param("sdissi", $nome, $preco, $quantidade, $descricao, $nova_foto, $id);
            if($stmt2->execute()){
                $sucesso = true;
                // Recarregar dados actualizados
                $stmt3 = $conn->prepare("SELECT * FROM produtos WHERE id=?");
                $stmt3->bind_param("i", $id);
                $stmt3->execute();
                $produto = $stmt3->get_result()->fetch_assoc();
            } else { $erro = 'Erro ao actualizar: '.$conn->error; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlphaSolutions - Editar Produto</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
:root{--bg:#f0f4fb;--surface:#fff;--border:#e2e8f4;--accent:#1e6fe8;--accent2:#1a5bc4;
  --text:#0f1f35;--muted:#6b82a8;--light:#e8f0fe;--shadow:rgba(30,111,232,0.08);
  --green:#16a34a;--green-light:#dcfce7;--red:#ef4444;--red-light:#fef2f2;}
body{background:var(--bg);min-height:100vh;font-family:'DM Sans',sans-serif;color:var(--text);}
body::before{content:'';position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:radial-gradient(circle,rgba(30,111,232,0.05) 1px,transparent 1px);background-size:28px 28px;}
header{position:sticky;top:0;z-index:100;background:var(--surface);border-bottom:1px solid var(--border);
  padding:0 40px;height:62px;display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 2px 16px rgba(30,111,232,0.07);}
.logo{font-family:'Rajdhani',sans-serif;font-size:18px;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--text);}
.logo span{color:var(--accent);}
.header-right{display:flex;gap:8px;}
.btn-nav{padding:7px 14px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;
  border:1px solid rgba(30,111,232,0.15);transition:all 0.2s;font-family:'Rajdhani',sans-serif;
  letter-spacing:1px;text-transform:uppercase;display:inline-flex;align-items:center;gap:6px;
  background:var(--light);color:var(--accent);}
.btn-nav:hover{background:#dbeafe;transform:translateY(-1px);}
.main{position:relative;z-index:1;max-width:760px;margin:0 auto;padding:36px 24px 60px;}
.page-top{margin-bottom:24px;}
.page-title{font-family:'Rajdhani',sans-serif;font-size:22px;font-weight:700;letter-spacing:3px;text-transform:uppercase;}
.page-title span{display:block;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:400;
  text-transform:none;color:var(--muted);margin-top:3px;letter-spacing:0;}
.form-card{background:var(--surface);border:1px solid var(--border);border-radius:12px;
  overflow:hidden;box-shadow:0 4px 20px var(--shadow);animation:fadeUp 0.4s ease both;}
.card-header{padding:16px 24px;border-bottom:1px solid var(--border);background:#f8faff;
  display:flex;align-items:center;gap:10px;}
.card-header i{font-size:17px;color:var(--accent);}
.card-header h3{font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:700;letter-spacing:2px;text-transform:uppercase;}
.form-body{padding:28px 24px;}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
@media(max-width:540px){.grid-2{grid-template-columns:1fr;}}
.field{margin-bottom:18px;}
.field:last-child{margin-bottom:0;}
.field label{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;
  color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:6px;}
.field label i{font-size:13px;color:var(--accent);}
.field input,.field textarea{width:100%;padding:10px 14px;border:1px solid var(--border);
  border-radius:8px;background:var(--bg);font-family:'DM Sans',sans-serif;font-size:14px;
  color:var(--text);outline:none;transition:border-color 0.2s,box-shadow 0.2s;}
.field input:focus,.field textarea:focus{border-color:rgba(30,111,232,0.45);box-shadow:0 0 0 3px rgba(30,111,232,0.07);}
.field input::placeholder,.field textarea::placeholder{color:var(--muted);}
.field textarea{min-height:96px;resize:vertical;}
.input-prefix{display:flex;}
.input-prefix .pfx{padding:10px 12px;background:var(--border);border:1px solid var(--border);
  border-right:none;border-radius:8px 0 0 8px;font-size:11px;font-weight:700;color:var(--muted);
  display:flex;align-items:center;}
.input-prefix input{border-radius:0 8px 8px 0;}
.divider{height:1px;background:var(--border);margin:22px 0;}
/* foto atual */
.current-img{border:1px solid var(--border);border-radius:10px;overflow:hidden;
  background:var(--bg);margin-bottom:14px;}
.current-img img{width:100%;max-height:160px;object-fit:contain;padding:12px;display:block;}
.current-img-label{padding:8px 12px;border-top:1px solid var(--border);background:#f8faff;
  font-size:11px;color:var(--muted);display:flex;align-items:center;gap:6px;}
/* upload */
.upload-zone{border:2px dashed var(--border);border-radius:10px;background:var(--bg);
  text-align:center;padding:22px 20px;cursor:pointer;transition:all 0.2s;position:relative;}
.upload-zone:hover,.upload-zone.dragover{border-color:rgba(30,111,232,0.4);background:var(--light);}
.upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.upload-zone i{font-size:26px;color:var(--accent);opacity:0.45;display:block;margin-bottom:7px;}
.upload-title{font-size:13px;font-weight:600;margin-bottom:2px;}
.upload-sub{font-size:11px;color:var(--muted);}
.preview-wrap{display:none;margin-top:12px;position:relative;}
.preview-wrap img{width:100%;max-height:160px;object-fit:contain;border-radius:8px;
  border:1px solid var(--border);background:var(--bg);padding:8px;}
.btn-remove-img{position:absolute;top:5px;right:5px;width:24px;height:24px;background:var(--red);
  color:white;border:none;border-radius:50%;font-size:11px;cursor:pointer;
  display:flex;align-items:center;justify-content:center;}
.file-label{margin-top:7px;font-size:12px;color:var(--green);display:none;align-items:center;gap:5px;}
/* messages */
.msg-success{background:var(--green-light);border:1px solid rgba(22,163,74,0.2);color:#15803d;
  padding:11px 14px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.msg-error{background:var(--red-light);border:1px solid rgba(239,68,68,0.2);color:#dc2626;
  padding:11px 14px;border-radius:8px;font-size:13px;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
.btn-row{display:flex;gap:10px;flex-wrap:wrap;}
.btn-primary{padding:11px 26px;background:linear-gradient(135deg,var(--accent),var(--accent2));
  color:white;border:none;border-radius:8px;font-family:'Rajdhani',sans-serif;font-size:13px;
  font-weight:700;letter-spacing:1.5px;text-transform:uppercase;cursor:pointer;
  transition:all 0.2s;display:inline-flex;align-items:center;gap:7px;}
.btn-primary:hover{box-shadow:0 4px 14px rgba(30,111,232,0.35);transform:translateY(-1px);}
.btn-ghost{padding:11px 18px;background:transparent;color:var(--muted);border:1px solid var(--border);
  border-radius:8px;font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:600;
  letter-spacing:1px;text-transform:uppercase;text-decoration:none;transition:all 0.2s;
  display:inline-flex;align-items:center;gap:7px;}
.btn-ghost:hover{border-color:rgba(30,111,232,0.3);color:var(--accent);}
footer{text-align:center;margin-top:36px;font-size:12px;color:var(--muted);opacity:0.6;}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<header>
  <div class="logo">Alpha<span>Solutions</span></div>
  <div class="header-right">
    <a href="listar.php" class="btn-nav"><i class="bi bi-list-ul"></i> Ver Produtos</a>
    <a href="../indexAdmin.php" class="btn-nav"><i class="bi bi-arrow-left"></i> Menu</a>
  </div>
</header>

<div class="main">
  <div class="page-top">
    <div class="page-title">Editar Produto
      <span>Actualizar dados do produto no sistema</span>
    </div>
  </div>

  <div class="form-card">
    <div class="card-header">
      <i class="bi bi-pencil-square"></i>
      <h3>Dados do Produto</h3>
    </div>
    <div class="form-body">

      <?php if($sucesso): ?>
      <div class="msg-success"><i class="bi bi-check-circle"></i> Produto actualizado com sucesso!</div>
      <?php endif; ?>
      <?php if($erro): ?>
      <div class="msg-error"><i class="bi bi-exclamation-circle"></i><?php echo htmlspecialchars($erro); ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" id="frmEdit">

        <div class="field">
          <label><i class="bi bi-tag"></i> Nome do Produto</label>
          <input type="text" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
        </div>

        <div class="grid-2">
          <div class="field" style="margin:0">
            <label><i class="bi bi-cash-coin"></i> Preço</label>
            <div class="input-prefix">
              <span class="pfx">MTS</span>
              <input type="number" name="preco" step="0.01" min="0.01"
                value="<?php echo htmlspecialchars($produto['preco']); ?>" required>
            </div>
          </div>
          <div class="field" style="margin:0">
            <label><i class="bi bi-stack"></i> Qtd. em Estoque</label>
            <input type="number" name="quantidade" min="0"
              value="<?php echo htmlspecialchars($produto['quantidade']); ?>" required>
          </div>
        </div>

        <div class="divider"></div>

        <div class="field">
          <label><i class="bi bi-card-text"></i> Descrição</label>
          <textarea name="descricao"><?php echo htmlspecialchars($produto['descricao'] ?? ''); ?></textarea>
        </div>

        <div class="field">
          <label><i class="bi bi-image"></i> Foto do Produto</label>

          <?php if(!empty($produto['foto'])): ?>
          <div class="current-img">
            <img src="../uploads/<?php echo htmlspecialchars($produto['foto']); ?>" alt="Foto actual">
            <div class="current-img-label"><i class="bi bi-image"></i> Foto actual — seleccione abaixo para substituir</div>
          </div>
          <?php endif; ?>

          <div class="upload-zone" id="uploadZone">
            <input type="file" name="foto" id="fotoInput" accept="image/jpeg,image/png,image/gif,image/webp">
            <div id="uploadPH">
              <i class="bi bi-cloud-arrow-up"></i>
              <div class="upload-title">Nova foto (opcional)</div>
              <div class="upload-sub">JPG · PNG · GIF · WEBP · máx. 5MB</div>
            </div>
          </div>
          <div class="preview-wrap" id="previewWrap">
            <img id="previewImg" src="#" alt="Preview">
            <button type="button" class="btn-remove-img" onclick="clearImg()"><i class="bi bi-x"></i></button>
          </div>
          <div class="file-label" id="fileLabel">
            <i class="bi bi-check-circle-fill"></i><span id="fName"></span>
          </div>
        </div>

        <div class="divider"></div>

        <div class="btn-row">
          <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Guardar Alterações</button>
          <a href="listar.php" class="btn-ghost"><i class="bi bi-x-lg"></i> Cancelar</a>
        </div>

      </form>
    </div>
  </div>
  <footer><p>© 2026 AlphaSolutions · Sistema de Gestão</p></footer>
</div>

<script>
const inp=document.getElementById('fotoInput');
const zone=document.getElementById('uploadZone');
const pw=document.getElementById('previewWrap');
const pi=document.getElementById('previewImg');
const fl=document.getElementById('fileLabel');
const fn=document.getElementById('fName');
const ph=document.getElementById('uploadPH');
inp.addEventListener('change',()=>{ if(inp.files[0]) show(inp.files[0]); });
zone.addEventListener('dragover',e=>{e.preventDefault();zone.classList.add('dragover');});
zone.addEventListener('dragleave',()=>zone.classList.remove('dragover'));
zone.addEventListener('drop',e=>{
  e.preventDefault();zone.classList.remove('dragover');
  const f=e.dataTransfer.files[0];
  if(f){inp.files=e.dataTransfer.files;show(f);}
});
function show(file){
  const r=new FileReader();
  r.onload=e=>{pi.src=e.target.result;pw.style.display='block';fl.style.display='flex';
    fn.textContent=file.name+' ('+Math.round(file.size/1024)+' KB)';ph.style.display='none';};
  r.readAsDataURL(file);
}
function clearImg(){
  inp.value='';pw.style.display='none';fl.style.display='none';ph.style.display='block';
}
</script>
</body>
</html>
