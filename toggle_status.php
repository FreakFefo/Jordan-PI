<?php
// Incluir a conexão com o banco de dados
include "connectDB.php";

// Verificar se o ID foi passado
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Obtém o ID e converte para inteiro

    // Obter o status atual do usuário
    $sql = "SELECT ativo FROM usuarios WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id); // "i" é o tipo do parâmetro (inteiro)
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    // Se o usuário foi encontrado
    if ($usuario) {
        // Alterar o status para o oposto (se estiver ativo, desativar; se estiver inativo, ativar)
        $novo_status = ($usuario['ativo'] == 1) ? 0 : 1;

        // Atualizar o status no banco de dados
        $update_sql = "UPDATE usuarios SET ativo = ? WHERE id = ?";
        $update_stmt = $mysqli->prepare($update_sql);
        $update_stmt->bind_param("ii", $novo_status, $id); // "ii" para dois inteiros
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
    echo "ID do usuário não fornecido.";
}
?>
