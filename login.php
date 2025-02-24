<?php
session_start();
include "connectDB.php"; // Conexão com o banco de dados

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $pwd = trim($_POST['pwd']);

    // Consulta ao banco para encontrar o usuário
    $sql = "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $mysqli->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Verifica se encontrou um usuário e se a senha está correta
    if ($usuario && hash('sha256', $pwd) === $usuario['senha']) {
        // Armazena o ID e outros dados do usuário na sessão
        $_SESSION['user_id'] = $usuario['id']; // Corrigido para 'user_id'
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo'];

        // Redirecionamento de acordo com o tipo de usuário
        if ($usuario['tipo'] == 'admin') {
            header("Location: backofficeadm.php");
        } elseif ($usuario['tipo'] == 'estoquista') {
            header("Location: backestoquista.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        // Redireciona para a página de login com erro
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
</head>
<body>

<header>
    <blockquote>
        <a href="index.php">
            <img src="image/logos.png" alt="Logo">
        </a>
    </blockquote>
</header>

<blockquote>
<div class="container">
    <center><h1>Teste</h1></center>

    <form action="login.php" method="post">
        <label for="email">Email:</label><br>
        <input type="text" name="email" id="email" required/><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="pwd" id="senha" required/><br><br>

        <input class="button" type="submit" value="Entrar"/>
        <input class="button" type="button" name="cancelar" value="Cancelar" onClick="window.location='index.php';" />
    </form>

    <!-- Exibição de erros -->
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
</blockquote>

</body>
</html>
