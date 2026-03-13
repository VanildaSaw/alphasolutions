<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin') {
    header("Location: acesso.php");
    exit;
}

include "conexao.php";

// URL da API externa de RH
$apiUrl = "https://srh-api.onrender.com/api/vendedores/todos";

$json = @file_get_contents($apiUrl);
if ($json === false) {
    // Falha ao chamar API externa
    $_SESSION['sync_msg'] = "Erro ao contactar API de RH.";
    header("Location: indexAdmin.php");
    exit;
}

$data = json_decode($json, true);
if (!is_array($data)) {
    $_SESSION['sync_msg'] = "Resposta inválida da API de RH.";
    header("Location: indexAdmin.php");
    exit;
}

// Insert/Update em vendedores com base no codigo (UNIQUE)
$sql = "INSERT INTO vendedores (codigo, nome, email, ativo)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
          nome = VALUES(nome),
          email = VALUES(email),
          ativo = VALUES(ativo)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['sync_msg'] = "Erro ao preparar query de sincronização.";
    header("Location: indexAdmin.php");
    exit;
}

foreach ($data as $emp) {
    // Campos vindos da nova API de RH
    // Exemplo de item:
    // {
    //   "id":101,
    //   "nomeCompleto":"Ana Souza",
    //   "email":"...",
    //   "codigo":"VEND-1",
    //   "ativo":true,
    //   "vendedor":true,
    //   ...
    // }

    $codigo = $emp['codigo'] ?? null;          // Ex.: "VEND-1"
    $nome   = $emp['nomeCompleto'] ?? '';
    $email  = $emp['email'] ?? null;

    if (!$codigo || trim($nome) === '') {
        continue; // ignora registros incompletos
    }

    // Considera ativo somente se a API marcar como vendedor e ativo
    $isVendedor = !empty($emp['vendedor']);
    $isAtivo    = array_key_exists('ativo', $emp) ? (bool)$emp['ativo'] : true;
    $ativo      = ($isVendedor && $isAtivo) ? 1 : 0;

    $stmt->bind_param("sssi", $codigo, $nome, $email, $ativo);
    $stmt->execute();
}

$stmt->close();
$conn->close();

$_SESSION['sync_msg'] = "Sincronização de vendedores concluída.";
header("Location: indexAdmin.php");
exit;

