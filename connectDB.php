<?php
$hostname = "localhost";
$bancodedados = "jordan";
$usuario = "root";
$senha = "";

// Conecta com o banco de dados
$mysqli = new mysqli($hostname, $usuario, $senha, $bancodedados);

// Verifica conexão 
if ($mysqli->connect_error) {
    die("Falha ao conectar no banco de dados: " . $mysqli->connect_error);
}

// Definir o charset para evitar problemas com acentuação
$mysqli->set_charset("utf8");

?>
