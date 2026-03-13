<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AlphaSolutions - Acesso</title>
<link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --bg:       #07111f;
  --surface:  #0d1a2e;
  --border:   rgba(30,136,229,0.18);
  --accent:   #1e6fe8;
  --glow:     #64b5f6;
  --text:     #e8f0fe;
  --muted:    #7a9cc4;
  --error:    #ff6b6b;
  --success:  #4dd9ac;
}

body {
  background: var(--bg);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  overflow: hidden;
}

body::before, body::after {
  content: '';
  position: fixed;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
  z-index: 0;
}
body::before {
  width: 500px; height: 500px;
  background: radial-gradient(circle, rgba(21,101,192,0.2), transparent 70%);
  top: -100px; left: -100px;
}
body::after {
  width: 400px; height: 400px;
  background: radial-gradient(circle, rgba(30,136,229,0.12), transparent 70%);
  bottom: -80px; right: -80px;
}

.corner { position:fixed; width:36px; height:36px; opacity:0.25; }
.corner-tl { top:20px; left:20px; border-top:1px solid var(--glow); border-left:1px solid var(--glow); }
.corner-tr { top:20px; right:20px; border-top:1px solid var(--glow); border-right:1px solid var(--glow); }
.corner-bl { bottom:20px; left:20px; border-bottom:1px solid var(--glow); border-left:1px solid var(--glow); }
.corner-br { bottom:20px; right:20px; border-bottom:1px solid var(--glow); border-right:1px solid var(--glow); }

.card {
  position: relative;
  z-index: 1;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  padding: 40px 36px;
  width: 360px;
  box-shadow: 0 0 60px rgba(30,136,229,0.08), 0 20px 40px rgba(0,0,0,0.4);
  animation: fadeUp 0.6s cubic-bezier(0.22,1,0.36,1) both;
}
@keyframes fadeUp {
  from { opacity:0; transform:translateY(20px); }
  to   { opacity:1; transform:translateY(0); }
}
.card::before {
  content: '';
  position: absolute;
  top: 0; left: 24px; right: 24px;
  height: 1px;
  background: linear-gradient(90deg, transparent, var(--accent), var(--glow), var(--accent), transparent);
}

.tabs {
  display: flex;
  gap: 4px;
  background: rgba(255,255,255,0.04);
  border-radius: 8px;
  padding: 4px;
  margin-bottom: 28px;
}
.tab {
  flex: 1;
  padding: 8px;
  background: none;
  border: none;
  color: var(--muted);
  font-family: 'Rajdhani', sans-serif;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 2px;
  text-transform: uppercase;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.25s;
}
.tab.active {
  background: rgba(30,136,229,0.15);
  color: var(--glow);
  box-shadow: 0 0 12px rgba(30,136,229,0.15);
}

.form-panel { display: none; }
.form-panel.active { display: block; animation: fadeUp 0.3s ease both; }

label {
  display: block;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 6px;
  margin-top: 16px;
}

input {
  width: 100%;
  padding: 10px 13px;
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--border);
  border-radius: 6px;
  color: var(--text);
  font-family: 'DM Sans', sans-serif;
  font-size: 14px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
input:focus {
  border-color: rgba(30,136,229,0.5);
  box-shadow: 0 0 0 3px rgba(30,136,229,0.08);
}
input::placeholder { color: rgba(122,156,196,0.5); }

.row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

.btn {
  width: 100%;
  margin-top: 24px;
  padding: 11px;
  background: linear-gradient(135deg, var(--accent), #1a5bc4);
  border: 1px solid rgba(100,181,246,0.25);
  border-radius: 6px;
  color: white;
  font-family: 'Rajdhani', sans-serif;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 3px;
  text-transform: uppercase;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}
.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(30,136,229,0.3);
}
.btn::after {
  content: '';
  position: absolute;
  top: -50%; left: -60%;
  width: 40%; height: 200%;
  background: rgba(255,255,255,0.08);
  transform: skewX(-20deg);
  transition: left 0.5s;
}
.btn:hover::after { left: 140%; }

#msg {
  min-height: 20px;
  font-size: 13px;
  margin-bottom: 4px;
  text-align: center;
}
.error   { color: var(--error); }
.success { color: var(--success); }

.divider {
  display: flex;
  align-items: center;
  gap: 12px;
  margin: 20px 0 0;
  color: var(--muted);
  font-size: 11px;
  letter-spacing: 1px;
}
.divider::before, .divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--border);
}
</style>
</head>
<body>

<div class="corner corner-tl"></div>
<div class="corner corner-tr"></div>
<div class="corner corner-bl"></div>
<div class="corner corner-br"></div>

<div class="card">
  <div id="msg"></div>

  <div class="tabs">
    <button class="tab active" onclick="switchTab('login')">Login</button>
    <button class="tab" onclick="switchTab('register')">Cadastro</button>
  </div>

  <!-- LOGIN -->
  <div class="form-panel active" id="panel-login">
    <form id="loginForm">
      <label>Email</label>
      <input type="email" id="email" placeholder="seu@email.com" required>
      <label>Senha</label>
      <input type="password" id="senha" placeholder="••••••••" required>
      <button class="btn" type="submit">Entrar →</button>
    </form>
  </div>

  <!-- CADASTRO -->
  <div class="form-panel" id="panel-register">
    <form id="registerForm">
      <div class="row-2">
        <div>
          <label>Nome</label>
          <input type="text" id="nome" placeholder="Nome" required>
        </div>
        <div>
          <label>Sobrenome</label>
          <input type="text" id="sobrenome" placeholder="Sobrenome" required>
        </div>
      </div>
      <label>Email</label>
      <input type="email" id="regEmail" placeholder="seu@email.com" required>
      <label>Senha</label>
      <input type="password" id="regSenha" placeholder="••••••••" required>
      <button class="btn" type="submit">Criar Conta →</button>
    </form>
  </div>

  <div class="divider">AlphaSolutions · Vendas</div>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.tab').forEach((t,i) => t.classList.toggle('active', (tab==='login'?0:1)===i));
  document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('panel-'+tab).classList.add('active');
  document.getElementById('msg').innerHTML = '';
}

function showMsg(html) { document.getElementById('msg').innerHTML = html; }

document.getElementById("loginForm").addEventListener("submit", function(e){
  e.preventDefault();
  fetch("login.php", {
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body: JSON.stringify({
      email: document.getElementById("email").value,
      senha: document.getElementById("senha").value
    })
  })
  .then(r=>r.json())
  .then(data=>{
    if(data.error)    showMsg("<span class='error'>"+data.error+"</span>");
    if(data.redirect) window.location.href = data.redirect;
  });
});

document.getElementById("registerForm").addEventListener("submit", function(e){
  e.preventDefault();
  fetch("register.php", {
    method:"POST",
    headers:{"Content-Type":"application/json"},
    body: JSON.stringify({
      nome:      document.getElementById("nome").value,
      sobrenome: document.getElementById("sobrenome").value,
      email:     document.getElementById("regEmail").value,
      senha:     document.getElementById("regSenha").value
    })
  })
  .then(r=>r.json())
  .then(data=>{
    if(data.error) showMsg("<span class='error'>"+data.error+"</span>");
    else           showMsg("<span class='success'>"+data.message+"</span>");
  });
});
</script>
</body>
</html>