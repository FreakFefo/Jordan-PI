<?php
include "connectDB.php";
//session_start();

//if (!isset($_SESSION['user_id']) || $_SESSION['grupo'] != 'ADM') {
//    echo "Acesso negado.";
//    exit;
//}

// Verificar se o ID do produto foi passado
if (!isset($_GET['id'])) {
    echo "ID do produto não fornecido.";
    exit;
}

$produto_id = intval($_GET['id']);

// Buscar os dados do produto
$stmt = $mysqli->prepare("SELECT * FROM produtos WHERE id = ?");
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();
$stmt->close();

if (!$produto) {
    echo "Produto não encontrado.";
    exit;
}

// Buscar as imagens do produto
$stmt = $mysqli->prepare("SELECT * FROM imagens_produto WHERE produto_id = ?");
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$imagens = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $avaliacao = floatval($_POST['avaliacao']);
    $descricao = trim($_POST['descricao']);
    $preco = floatval($_POST['preco']);
    $quantidade_estoque = intval($_POST['quantidade_estoque']);
    $imagem_principal = isset($_POST['imagem_principal']) ? intval($_POST['imagem_principal']) : null;

    // Atualizar os dados do produto
    $stmt = $mysqli->prepare("UPDATE produtos SET nome = ?, avaliacao = ?, descricao = ?, preco = ?, quantidade_estoque = ? WHERE id = ?");
    $stmt->bind_param("sdssdi", $nome, $avaliacao, $descricao, $preco, $quantidade_estoque, $produto_id);
    $stmt->execute();
    $stmt->close();

    // Atualizar a imagem principal
    if ($imagem_principal !== null) {
        $stmt = $mysqli->prepare("UPDATE imagens_produto SET principal = IF(id = ?, 1, 0) WHERE produto_id = ?");
        $stmt->bind_param("ii", $imagem_principal, $produto_id);
        $stmt->execute();
        $stmt->close();
    }

    // Processar novas imagens
    $diretorio = "uploads/";
    if (!file_exists($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    foreach ($_FILES['imagens']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['imagens']['error'][$key] == UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['imagens']['name'][$key], PATHINFO_EXTENSION);
            $novo_nome = uniqid('img_') . "." . $extensao;
            $caminho = $diretorio . $novo_nome;

            if (move_uploaded_file($tmp_name, $caminho)) {
                $principal = ($imagem_principal === "new_$key") ? 1 : 0;
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

// Remover uma imagem (caso seja chamada via GET)
if (isset($_GET['remover_imagem'])) {
    $imagem_id = intval($_GET['remover_imagem']);

    // Buscar o caminho da imagem para deletar do diretório
    $stmt = $mysqli->prepare("SELECT caminho FROM imagens_produto WHERE id = ? AND produto_id = ?");
    $stmt->bind_param("ii", $imagem_id, $produto_id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($resultado) {
        unlink($resultado['caminho']); // Remove o arquivo do diretório

        // Remover do banco de dados
        $stmt = $mysqli->prepare("DELETE FROM imagens_produto WHERE id = ?");
        $stmt->bind_param("i", $imagem_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: alterarProduto.php?id=$produto_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Produto</title>
    <script>
        function addImagemInput() {
            const container = document.getElementById("imagens-container");
            const index = container.children.length;

            const div = document.createElement("div");
            div.innerHTML = `
                <input type="file" name="imagens[]" accept="image/*">
                <label>
                    <input type="radio" name="imagem_principal" value="new_${index}">
                    Definir como principal
                </label>
            `;
            container.appendChild(div);
        }
    </script>
</head>
<body>
    <h2>Alterar Produto</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" maxlength="200" required><br>

        <label>Avaliação:</label>
        <select name="avaliacao" required>
            <?php for ($i = 1.0; $i <= 5.0; $i += 0.5) {
                $selected = ($produto['avaliacao'] == $i) ? "selected" : "";
                echo "<option value='$i' $selected>$i</option>";
            } ?>
        </select><br>

        <label>Descrição:</label>
        <textarea name="descricao" maxlength="2000" required><?= htmlspecialchars($produto['descricao']) ?></textarea><br>

        <label>Preço:</label>
        <input type="number" name="preco" value="<?= $produto['preco'] ?>" step="0.01" min="0" required><br>

        <label>Quantidade em Estoque:</label>
        <input type="number" name="quantidade_estoque" value="<?= $produto['quantidade_estoque'] ?>" min="0" required><br>

        <h3>Imagens do Produto</h3>
        <?php foreach ($imagens as $img) { ?>
            <div>
                <img src="<?= $img['caminho'] ?>" width="100">
                <label>
                    <input type="radio" name="imagem_principal" value="<?= $img['id'] ?>" <?= ($img['principal'] == 1) ? "checked" : "" ?>>
                    Definir como principal
                </label>
                <a href="alterarProduto.php?id=<?= $produto_id ?>&remover_imagem=<?= $img['id'] ?>">Remover</a>
            </div>
        <?php } ?>

        <h4>Adicionar Novas Imagens</h4>
        <div id="imagens-container"></div>
        <button type="button" onclick="addImagemInput()">+ Adicionar outra imagem</button><br><br>

        <button type="submit">Salvar</button>
        <button type="button" onclick="window.location.href='listarprodutos.php'">Cancelar</button>
    </form>
</body>
</html>
