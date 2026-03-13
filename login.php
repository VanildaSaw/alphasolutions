<?php
session_start();
header("Content-Type: application/json");
include "conexao.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$senha = $data['senha'];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE email=? AND senha=?");
$stmt->bind_param("ss", $email,$senha);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 1){

    $user = $result->fetch_assoc();

    $_SESSION['user'] = $user;

    if($user['tipo'] == "admin"){
        echo json_encode(["redirect"=>"indexAdmin.php"]);
    } else {
        echo json_encode(["redirect"=>"produtos.php"]);
    }

} else {
    echo json_encode(["error"=>"Email ou senha inválidos"]);
}

$stmt->close();
$conn->close();
?>