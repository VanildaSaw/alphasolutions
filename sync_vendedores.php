<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] != 'admin') {
    header("Location: acesso.php");
    exit;
}

include "conexao.php";

// URL da API externa de RH
$apiUrl = "https://hr-system-apii.onrender.com/api/employees";

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
    // Montar campos locais a partir da API externa
    $idRh   = $emp['id'] ?? null;
    $nome   = trim(($emp['firstName'] ?? '') . ' ' . ($emp['lastName'] ?? ''));
    $email  = $emp['email'] ?? null;
    $cargo  = $emp['cargo'] ?? '';

    if (!$idRh || $nome === '') {
        continue;
    }

    // Código padrão VEND-{id}
    $codigo = 'VEND-' . $idRh;

    // Ativo apenas se cargo for Vendedor (ajuste conforme necessidade)
    $ativo = ($cargo === 'Vendedor') ? 1 : 0;

    $stmt->bind_param("sssi", $codigo, $nome, $email, $ativo);
    $stmt->execute();
}

$stmt->close();
$conn->close();

$_SESSION['sync_msg'] = "Sincronização de vendedores concluída.";
header("Location: indexAdmin.php");
exit;

