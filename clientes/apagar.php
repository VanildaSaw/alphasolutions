<?php
include("../conexao.php");

$id = $_GET['id'];

$sql = "DELETE FROM clientes WHERE id=$id";

$conn->query($sql);

echo "Cliente apagado com sucesso!";

?>

<br><br>

<a href="listar.php">Voltar para lista</a>