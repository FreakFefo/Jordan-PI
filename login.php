<?php
session_start();
include "connectDB.php"; // Conexão com o banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $pwd = trim($_POST['pwd']);

    $sql = "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $mysqli->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && hash('sha256', $pwd) === $usuario['senha']) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo'];

        if ($usuario['tipo'] == 'admin') {
            header("Location: backofficeadm.php");
        } elseif ($usuario['tipo'] == 'estoquista') {
            header("Location: backestoquista.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        header("Location: login.php?errcode=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f8f8;
        }
        .logo {
            height: 50px;
        }
        .nav-link {
            text-decoration: none;
            color: #000;
            font-size: 16px;
            padding: 8px 12px;
            transition: color 0.2s;
        }
        .nav-link:hover {
            color: #4CAF50;
        }
    </style>
</head>
<body>

<header class="header">
    <a href="home.php">
        <img src="Image/logo.png" alt="Logo" class="logo">
    </a>
    <a href="login.php" class="nav-link">Faça login / Crie seu login</a>
</header>

<div class="container">
    <center><h1>Login</h1></center>

    <form action="login.php" method="post">
        <label for="email">Email:</label><br>
        <input type="text" name="email" id="email" required/><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="pwd" id="senha" required/><br><br>

        <input class="button" type="submit" value="Entrar"/>
        <a href="cadastro.php" class="button">Cadastrar-se</a> <!-- Link para a página de cadastro -->
    </form>

    <?php
    if (isset($_GET['errcode'])) {
        $errorMessages = [
            1 => 'Usuário ou senha inválidos. Tente novamente.',
            2 => 'Por favor, faça login.'
        ];
        $errcode = intval($_GET['errcode']);
        if (array_key_exists($errcode, $errorMessages)) {
            echo '<p style="color: red; text-align: center;">' . htmlspecialchars($errorMessages[$errcode]) . '</p>';
        }
    }
    ?>
</div>

</body>
</html>
