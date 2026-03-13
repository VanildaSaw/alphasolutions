<?php
header("Content-Type: application/json");
include "conexao.php";

$data = json_decode(file_get_contents("php://input"), true);

$nome = $data['nome'];
$sobrenome = $data['sobrenome'];
$email = $data['email'];
$senha = $data['senha'];

$stmt = $conn->prepare("INSERT INTO usuarios (nome,sobrenome,email,senha,tipo) VALUES (?,?,?,?, 'cliente')");
$stmt->bind_param("ssss", $nome,$sobrenome,$email,$senha);

if($stmt->execute()){
    echo json_encode(["message"=>"Cadastro feito com sucesso"]);
} else {
    echo json_encode(["error"=>"Erro ao cadastrar"]);
}

$stmt->close();
$conn->close();
?>