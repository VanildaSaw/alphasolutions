<?php
$tz = @date_default_timezone_get();
if ($tz !== 'Africa/Maputo') {
    date_default_timezone_set('Africa/Maputo');
}

$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db   = getenv('DB_NAME');
$port = (int)getenv('DB_PORT');

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

?>