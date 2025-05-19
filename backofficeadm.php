<?php
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
</head>
<body>
    <h2>Principal</h2>
    <ul>
        <li><a href="listarprodutos.php">Listar Produtos</a></li>
        <li><a href="listarusuarios.php">Listar Usuários</a></li>
        <a href="home.php?logout=true">Logoff</a>
    </ul>
</body>
</html>
