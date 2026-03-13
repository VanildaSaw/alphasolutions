<?php
include("../conexao.php");

$id = $_GET['id'];

// Busca os dados do cliente
$sql = "SELECT * FROM clientes WHERE id = $id";
$result = $conn->query($sql);
$cliente = $result->fetch_assoc();

$mensagem_sucesso = '';
$mensagem_erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $telefone = mysqli_real_escape_string($conn, $_POST['telefone']);

    $sql = "UPDATE clientes 
            SET nome='$nome', email='$email', telefone='$telefone'
            WHERE id=$id";

    if ($conn->query($sql)) {
        $mensagem_sucesso = "Cliente atualizado com sucesso!";

        // Atualiza os dados exibidos
        $sql = "SELECT * FROM clientes WHERE id=$id";
        $result = $conn->query($sql);
        $cliente = $result->fetch_assoc();
    } else {
        $mensagem_erro = "Erro ao atualizar cliente: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>AlphaSolutions - Editar Cliente</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-dark: #0a2647;
            --secondary-dark: #1b3a5c;
            --accent-blue: #2c5f8a;
            --light-bg: #f8faff;
            --white: #ffffff;
            --text-dark: #1e293b;
            --text-light: #f1f5f9;
            --success: #1e7e34;
            --success-hover: #16632a;
            --warning: #ffc107;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary-dark)) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .navbar-brand {
            font-weight: 600;
            letter-spacing: 0.5px;
            color: var(--text-light) !important;
        }

        .page-header {
            background: var(--white);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-left: 5px solid var(--primary-dark);
        }

        .page-title {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--text-dark);
            opacity: 0.7;
            font-size: 1rem;
        }

        .form-container {
            background: var(--white);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
            border: 1px solid rgba(10, 38, 71, 0.05);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-label {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--accent-blue);
            font-size: 1.1rem;
            width: 20px;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: var(--white);
        }

        .form-control:focus {
            border-color: var(--primary-dark);
            box-shadow: 0 0 0 0.2rem rgba(10, 38, 71, 0.1);
            outline: none;
        }

        .form-control:hover {
            border-color: var(--accent-blue);
        }

        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .input-group-text {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary-dark));
            color: var(--white);
            border: none;
            border-radius: 12px 0 0 12px;
            padding: 0.75rem 1.2rem;
        }

        .input-group .form-control {
            border-radius: 0 12px 12px 0;
        }

        .alert {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 5px solid var(--success);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 5px solid #dc3545;
        }

        .btn-update {
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary-dark));
            color: var(--white);
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            box-shadow: 0 4px 15px rgba(10, 38, 71, 0.3);
            margin-right: 0.5rem;
        }

        .btn-update:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(10, 38, 71, 0.4);
            color: var(--white);
            background: linear-gradient(135deg, var(--secondary-dark), var(--primary-dark));
        }

        .btn-cancel {
            background-color: transparent;
            color: var(--primary-dark);
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 2px solid var(--primary-dark);
        }

        .btn-cancel:hover {
            background-color: var(--primary-dark);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(10, 38, 71, 0.2);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .info-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid var(--border-color);
        }

        .info-icon {
            background: var(--white);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-dark);
            font-size: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .info-content h4 {
            font-size: 0.9rem;
            color: var(--text-dark);
            opacity: 0.7;
            margin: 0 0 0.2rem 0;
        }

        .info-content p {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin: 0;
        }

        footer {
            margin-top: auto;
            text-align: center;
            padding: 2rem 0;
            color: var(--text-dark);
            opacity: 0.6;
            font-size: 0.9rem;
        }

        .container {
            max-width: 800px;
            flex: 1;
        }

        /* Animações */
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

        .form-container {
            animation: fadeInUp 0.5s ease;
        }

        /* Validação visual */
        .form-control.is-valid {
            border-color: var(--success);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%231e7e34' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .feedback-message {
            font-size: 0.85rem;
            margin-top: 0.3rem;
            display: block;
        }

        .valid-feedback {
            color: var(--success);
        }

        .invalid-feedback {
            color: #dc3545;
        }

        /* Breadcrumb */
        .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }

        .breadcrumb-item a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: var(--text-dark);
            opacity: 0.7;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="container">
            <span class="navbar-brand mb-0 h1">
                <i class="bi bi-people-fill me-2"></i>
                AlphaSolutions - Gestão de Clientes
            </span>
        </div>
    </nav>

    <div class="container">

        <!-- Breadcrumb (navegação) -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../indexAdmin.php"><i class="bi bi-house-door"></i> Menu</a></li>
                <li class="breadcrumb-item"><a href="listar.php"><i class="bi bi-people"></i> Clientes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar Cliente #<?php echo str_pad($id, 3, '0', STR_PAD_LEFT); ?></li>
            </ol>
        </nav>

        <!-- Cabeçalho da Página -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-pencil-square me-2"></i>
                Editar Cliente
            </h1>
            <p class="page-subtitle">
                Atualize as informações do cliente no sistema
            </p>
        </div>

        <!-- Mensagens de feedback -->
        <?php if ($mensagem_sucesso): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $mensagem_sucesso; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($mensagem_erro): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $mensagem_erro; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Card de informações do cliente -->
        <div class="info-card">
            <div class="info-icon">
                <i class="bi bi-person-badge"></i>
            </div>
            <div class="info-content">
                <h4>Editando cliente</h4>
                <p>ID #<?php echo str_pad($id, 3, '0', STR_PAD_LEFT); ?> • Cadastrado em:
                    <?php
                    if (isset($cliente['data_cadastro'])) {
                        echo date('d/m/Y', strtotime($cliente['data_cadastro']));
                    } else {
                        echo 'Data não disponível';
                    }
                    ?>
                </p>
            </div>
        </div>

        <!-- Formulário de Edição -->
        <div class="form-container">
            <form method="POST" id="formEditarCliente" onsubmit="return validarFormulario()">

                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-person-circle"></i>
                        Nome Completo
                    </label>
                    <input type="text"
                        name="nome"
                        id="nome"
                        class="form-control"
                        value="<?php echo htmlspecialchars($cliente['nome']); ?>"
                        placeholder="Digite o nome completo"
                        required
                        minlength="3"
                        maxlength="100">
                    <small class="feedback-message valid-feedback" id="nomeValid">Nome válido!</small>
                    <small class="feedback-message invalid-feedback" id="nomeInvalid">O nome deve ter pelo menos 3 caracteres</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-envelope"></i>
                        E-mail
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-at"></i>
                        </span>
                        <input type="email"
                            name="email"
                            id="email"
                            class="form-control"
                            value="<?php echo htmlspecialchars($cliente['email']); ?>"
                            placeholder="exemplo@email.com"
                            required>
                    </div>
                    <small class="feedback-message valid-feedback" id="emailValid">E-mail válido!</small>
                    <small class="feedback-message invalid-feedback" id="emailInvalid">Digite um e-mail válido</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">
                        <i class="bi bi-telephone"></i>
                        Telefone
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-phone"></i>
                        </span>
                        <input type="tel"
                            name="telefone"
                            id="telefone"
                            class="form-control"
                            value="<?php echo htmlspecialchars($cliente['telefone']); ?>"
                            placeholder="(XX) XXXXX-XXXX"
                            required
                            maxlength="15">
                    </div>
                    <small class="feedback-message valid-feedback" id="telefoneValid">Telefone válido!</small>
                    <small class="feedback-message invalid-feedback" id="telefoneInvalid">Digite um telefone válido</small>
                </div>

                <!-- Botões -->
                <div class="button-group">
                    <button type="submit" class="btn-update">
                        <i class="bi bi-check-circle"></i>
                        Atualizar Cliente
                    </button>
                    <a href="listar.php" class="btn-cancel">
                        <i class="bi bi-x-circle"></i>
                        Cancelar
                    </a>
                </div>

            </form>
        </div>

        <!-- Dicas de preenchimento -->
        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Todos os campos são obrigatórios. Os dados serão atualizados imediatamente.
            </small>
        </div>

    </div>

    <footer>
        <p>© 2026 AlphaSolutions - Sistema de Gestão de Clientes</p>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Validação em tempo real
        document.addEventListener('DOMContentLoaded', function() {
            const nomeInput = document.getElementById('nome');
            const emailInput = document.getElementById('email');
            const telefoneInput = document.getElementById('telefone');

            // Validação do nome
            nomeInput.addEventListener('input', function() {
                if (this.value.length >= 3) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                    document.getElementById('nomeValid').style.display = 'block';
                    document.getElementById('nomeInvalid').style.display = 'none';
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    document.getElementById('nomeValid').style.display = 'none';
                    document.getElementById('nomeInvalid').style.display = 'block';
                }
            });

            // Validação do email
            emailInput.addEventListener('input', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailRegex.test(this.value)) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                    document.getElementById('emailValid').style.display = 'block';
                    document.getElementById('emailInvalid').style.display = 'none';
                } else {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    document.getElementById('emailValid').style.display = 'none';
                    document.getElementById('emailInvalid').style.display = 'block';
                }
            });

            // Validação do telefone (formatação automática)
            telefoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.length <= 2) {
                        value = `(${value}`;
                    } else if (value.length <= 7) {
                        value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
                    } else {
                        value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7, 11)}`;
                    }
                    this.value = value;

                    // Valida se tem pelo menos 10 dígitos
                    const digitos = this.value.replace(/\D/g, '');
                    if (digitos.length >= 10) {
                        this.classList.add('is-valid');
                        this.classList.remove('is-invalid');
                        document.getElementById('telefoneValid').style.display = 'block';
                        document.getElementById('telefoneInvalid').style.display = 'none';
                    } else {
                        this.classList.add('is-invalid');
                        this.classList.remove('is-valid');
                        document.getElementById('telefoneValid').style.display = 'none';
                        document.getElementById('telefoneInvalid').style.display = 'block';
                    }
                }
            });

            // Trigger inicial para validar campos pré-preenchidos
            setTimeout(() => {
                nomeInput.dispatchEvent(new Event('input'));
                emailInput.dispatchEvent(new Event('input'));
                telefoneInput.dispatchEvent(new Event('input'));
            }, 100);
        });

        // Validação final antes do envio
        function validarFormulario() {
            const nome = document.getElementById('nome').value;
            const email = document.getElementById('email').value;
            const telefone = document.getElementById('telefone').value.replace(/\D/g, '');

            if (nome.length < 3) {
                alert('O nome deve ter pelo menos 3 caracteres');
                return false;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Digite um e-mail válido');
                return false;
            }

            if (telefone.length < 10) {
                alert('Digite um telefone válido com DDD');
                return false;
            }

            return confirm('Tem certeza que deseja atualizar este cliente?');
        }

        // Animação de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('.form-control, .btn-update, .btn-cancel');
            formElements.forEach((el, index) => {
                el.style.animation = `fadeInUp 0.3s ease forwards ${index * 0.05}s`;
                el.style.opacity = '0';
            });
        });
    </script>

</body>

</html>