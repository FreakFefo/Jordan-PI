<?php
session_start();
include "connectDB.php"; // Conexão com o banco de dados

if (isset($_POST['Login']) && isset($_POST['pwd'])) {
    $username = trim($_POST['Login']);
    $pwd = trim($_POST['pwd']);

    // Preparar a query para buscar o usuário pelo email
    $sql = "SELECT id, senha, tipo FROM usuarios WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        error_log("Erro na preparação da query: " . $mysqli->error);
        header("Location: login.php?errcode=2");
        exit();
    }

    // Bind do parâmetro e execução da query
    $stmt->bind_param("s", $username);
    $stmt->execute();
    
    // Capturar resultado
    $resultado = $stmt->get_result();
    $user = $resultado->fetch_assoc();

    // DEBUG: Verifique se o usuário foi encontrado
    if (!$user) {
        error_log("Usuário não encontrado: " . $username);
        header("Location: login.php?errcode=1");
        exit();
    }

    // DEBUG: Verifique a senha criptografada no banco
    error_log("Senha do BD: " . $user['senha']);

    if (password_verify($pwd, $user['senha'])) {
        // Armazena ID e Tipo na sessão
        $_SESSION['id'] = $user['id'];
        $_SESSION['tipo'] = $user['tipo'];

        // Redirecionamento conforme o tipo de usuário
        switch ($user['tipo']) {
            case 'admin':
                header("Location: backofficeadm.php");
                break;
            case 'estoquista':
                header("Location: backestoquista.php");
                break;
            default:
                header("Location: home.php");
        }
        exit();
    } else {
        error_log("Senha incorreta para usuário: " . $username);
        header("Location: login.php?errcode=1");
        exit();
    }
} else {
    header("Location: login.php?errcode=2");
    exit();
}
?>
