<?php
// Conexão com o banco de dados
include "connectDB.php";

// Verificar se o ID do produto foi passado na URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Produto não encontrado.";
    exit;
}

$id_produto = $_GET['id'];

// Buscar as informações do produto
$sql = "SELECT id, nome, avaliacao, descricao, preco, quantidade_estoque, status FROM produtos WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_produto);
$stmt->execute();
$result = $stmt->get_result();

// Verificar se o produto foi encontrado
if ($result->num_rows == 0) {
    echo "Produto não encontrado.";
    exit;
}

$produto = $result->fetch_assoc();

// Atualizar a quantidade de estoque se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_quantidade = intval($_POST['quantidade_estoque']);

    // Verificar se a quantidade é válida
    if ($nova_quantidade < 0) {
        echo "A quantidade não pode ser negativa.";
    } else {
        // Atualizar a quantidade no banco de dados
        $sql_update = "UPDATE produtos SET quantidade_estoque = ? WHERE id = ?";
        $stmt_update = $mysqli->prepare($sql_update);
        $stmt_update->bind_param("ii", $nova_quantidade, $id_produto);
        if ($stmt_update->execute()) {
            echo "Quantidade do produto atualizada com sucesso!";
            // Redirecionar após o sucesso da atualização
            header("Location: listarprodutoestoquista.php");
            exit;
        } else {
            echo "Erro ao atualizar a quantidade.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Produto - Estoquista</title>
    <style>
        label, input {
            display: block;
            margin-bottom: 10px;
        }
        .disabled {
            background-color: #f1f1f1;
            border: 1px solid #ccc;
        }
        .container {
            width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Alterar Produto - Estoquista</h2>

        <form method="POST" action="">
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" class="disabled" disabled>
            
            <label for="avaliacao">Avaliação:</label>
            <input type="text" id="avaliacao" name="avaliacao" value="<?php echo htmlspecialchars($produto['avaliacao']); ?>" class="disabled" disabled>
            
            <label for="descricao">Descrição:</label>
            <textarea id="descricao" name="descricao" class="disabled" disabled><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
            
            <label for="preco">Preço:</label>
            <input type="text" id="preco" name="preco" value="R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>" class="disabled" disabled>
            
            <label for="quantidade_estoque">Quantidade em Estoque:</label>
            <input type="number" id="quantidade_estoque" name="quantidade_estoque" value="<?php echo $produto['quantidade_estoque']; ?>" required>
            
            <label for="status">Status:</label>
            <input type="text" id="status" name="status" value="<?php echo ($produto['status'] == 1) ? 'Ativo' : 'Inativo'; ?>" class="disabled" disabled>

            <button type="submit">Atualizar Quantidade</button>
            <button type="button" onclick="window.location.href='listarprodutoestoquista.php'">Cancelar</button>
        </form>
    </div>
</body>
</html>
