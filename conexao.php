<?php

$host = "sql103.infinityfree.com";
$user = "if0_41339057";
$pass = "alphasolution26";
$db   = "if0_41339057_alphasolutions";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

?>