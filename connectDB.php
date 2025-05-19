<?php
$hostname = "localhost";
$bancodedados = "jordan";
$usuario = "root";
$senha = "";
$porta = 3306; // Defina a porta correta, se necessário

$mysqli = new mysqli($hostname, $usuario, $senha, $bancodedados, $porta);

// Verifica se houve erro na conexão
if ($mysqli->connect_error) {
    die("Falha na conexão com o banco de dados: " . $mysqli->connect_error);
}

// Definir charset para evitar problemas com acentuação
$mysqli->set_charset("utf8");
?>
