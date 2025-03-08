<?php
include "connectDB.php";
session_start();

//if (!isset($_SESSION['user_id'])) {
//    echo "Você não está logado.";
//    exit;
//}

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $avaliacao = floatval($_POST['avaliacao']);
    $descricao = trim($_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $quantidade_estoque = intval($_POST['quantidade_estoque']);

    // Inserir o produto
    $stmt = $mysqli->prepare("INSERT INTO produtos (nome, avaliacao, descricao, preco, quantidade_estoque) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssd", $nome, $avaliacao, $descricao, $preco, $quantidade_estoque);
    $stmt->execute();
    $produto_id = $stmt->insert_id;
    $stmt->close();

    // Processamento das imagens
    $diretorio = "uploads/";
    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    $imagem_principal = isset($_POST['imagem_principal']) ? $_POST['imagem_principal'] : null;

    foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['imagens']['error'][$key] == UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['imagens']['name'][$key], PATHINFO_EXTENSION);
            $novo_nome = uniqid('img_') . "." . $extensao;
            $caminho = $diretorio . $novo_nome;

            if (move_uploaded_file($tmp_name, $caminho)) {
                $principal = ($imagem_principal == $key) ? 1 : 0;
                $stmt = $mysqli->prepare("INSERT INTO imagens_produto (produto_id, caminho, principal) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $produto_id, $caminho, $principal);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    header("Location: listarprodutos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <script>
        function addImagemInput() {
            const container = document.getElementById("imagens-container");
            const index = container.children.length;

            const div = document.createElement("div");
            div.innerHTML = `
                <input type="file" name="imagens[]" accept="image/*" required>
                <label>
                    <input type="radio" name="imagem_principal" value="${index}" ${index === 0 ? "checked" : ""}>
                    Definir como principal
                </label>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <h2>Cadastrar Produto</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label>Nome:</label>
        <input type="text" name="nome" maxlength="200" required><br>

        <label>Avaliação:</label>
        <select name="avaliacao" required>
            <option value="1.0">1.0</option>
            <option value="1.5">1.5</option>
            <option value="2.0">2.0</option>
            <option value="2.5">2.5</option>
            <option value="3.0">3.0</option>
            <option value="3.5">3.5</option>
            <option value="4.0">4.0</option>
            <option value="4.5">4.5</option>
            <option value="5.0">5.0</option>
        </select><br>

        <label>Descrição:</label>
        <textarea name="descricao" maxlength="2000" required></textarea><br>

        <label>Preço:</label>
        <input type="number" name="preco" step="0.01" min="0" required><br>

        <label>Quantidade em Estoque:</label>
        <input type="number" name="quantidade_estoque" min="0" required><br>

        <h3>Imagens do Produto</h3>
        <div id="imagens-container">
            <div>
                <input type="file" name="imagens[]" accept="image/*" required>
                <label>
                    <input type="radio" name="imagem_principal" value="0" checked>
                    Definir como principal
                </label>
            </div>
        </div>
        <button type="button" onclick="addImagemInput()">+ Adicionar outra imagem</button><br><br>

        <button type="submit">Salvar</button>
        <button type="button" onclick="window.location.href='listarprodutos.php'">Cancelar</button>
    </form>
</body>
</html>
