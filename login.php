<?php
session_start();
include "connectDB.php"; // Conexão com o banco de dados

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Verifica se o e-mail existe no banco de dados
    $sql = "SELECT * FROM usuario WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica a senha com o hash armazenado
        if (password_verify($senha, $usuario['senha'])) {
            // Senha correta, inicia a sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_grupo'] = $usuario['grupo'];

            // Redireciona com base no grupo do usuário
            if ($usuario['grupo'] == 'ADM') {
                header("Location: backofficeadm.php");
            } elseif ($usuario['grupo'] == 'EST') {
                header("Location: backofficeestoquista.php");
            } elseif ($usuario['grupo'] == 'CLI') {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $erro = "Usuário ou senha inválidos. Tente novamente.";
        }
    } else {
        $erro = "Usuário ou senha inválidos. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="header-container">
        <a href="home.php">
            <img src="Image/logo.png" alt="Logo" class="logo">
        </a>
    </div>
</header>

<div class="container">
    <h1>Login</h1>

    <?php if (isset($erro)) echo "<p style='color: red;'>$erro</p>"; ?>

    <form action="login.php" method="post">
        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" id="senha" required><br><br>

        <input type="submit" value="Entrar">
    </form>

    <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
</div>

</body>
</html>
