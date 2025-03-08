<?php
// Incluir a conexão com o banco de dados
include "connectDB.php";

// Verificar se o ID foi passado corretamente na URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id']; // Obtém o ID e converte para inteiro

    // Obter o status atual do usuário
    $sql = "SELECT ativo FROM usuario WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $mysqli->error);
    }

    $stmt->bind_param("i", $id); // "i" indica inteiro
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario) {
        // Alterna o status do usuário
        $novo_status = $usuario['ativo'] == 1 ? 0 : 1;

        // Atualizar o status no banco de dados
        $update_sql = "UPDATE usuario SET ativo = ? WHERE id = ?";
        $update_stmt = $mysqli->prepare($update_sql);
        if (!$update_stmt) {
            die("Erro na preparação da atualização: " . $mysqli->error);
        }

        $update_stmt->bind_param("ii", $novo_status, $id);
        if ($update_stmt->execute()) {
            // Redireciona para a página de listagem de usuários
            header("Location: listarusuarios.php");
            exit();
        } else {
            echo "Erro ao alterar o status do usuário.";
        }
    } else {
        echo "Usuário não encontrado.";
    }
} else {
    echo "ID do usuário inválido ou não fornecido.";
}
?>
