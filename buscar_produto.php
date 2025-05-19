<?php
session_start();
require 'connectDB.php';

$termo = $_GET['termo'] ?? '';
$produtos = [];

// Buscar categorias únicas
$sqlCategorias = "SELECT DISTINCT categoria FROM produtos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria ASC";
$resultCategorias = $mysqli->query($sqlCategorias);
$categorias = $resultCategorias->fetch_all(MYSQLI_ASSOC);

// Buscar produtos conforme termo
if ($termo !== '') {
    $termoLike = "%" . $termo . "%";
    $sql = "SELECT * FROM produtos WHERE nome LIKE ? OR categoria LIKE ? ORDER BY nome ASC";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $termoLike, $termoLike);
    $stmt->execute();
    $result = $stmt->get_result();
    $produtos = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Buscar Produto</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
            margin: 0;
        }

        header {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .logo {
            height: 50px;
            cursor: pointer;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 300px;
            font-size: 16px;
        }

        button {
            padding: 8px 12px;
            font-size: 16px;
            cursor: pointer;
        }

        .categorias {
            margin-bottom: 20px;
        }

        .categorias button {
            padding: 6px 12px;
            margin: 4px;
            border: none;
            background: #007BFF;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        .categorias button:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 4px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f7f7f7;
        }

        tr:hover {
            background: #f1f1f1;
        }

        a {
            color: #1a73e8;
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function buscarPorCategoria(categoria) {
            const input = document.querySelector('input[name="termo"]');
            input.value = categoria;
            input.form.submit();
        }
    </script>
</head>
<body>

<header>
    <a href="home.php"><img src="Image/logo.png" alt="Logo da Loja" class="logo"></a>
</header>

<h1>Buscar Produto</h1>

<form method="GET" action="buscar_produto.php">
    <input type="text" name="termo" placeholder="Digite o nome ou categoria" value="<?= htmlspecialchars($termo) ?>">
    <button type="submit">Buscar</button>
</form>

<div class="categorias">
    <?php foreach ($categorias as $cat): ?>
        <button type="button" onclick="buscarPorCategoria('<?= htmlspecialchars($cat['categoria']) ?>')">
            <?= htmlspecialchars($cat['categoria']) ?>
        </button>
    <?php endforeach; ?>
</div>

<?php if ($termo && empty($produtos)): ?>
    <p><strong>Nenhum produto encontrado.</strong></p>
<?php elseif (!empty($produtos)): ?>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                    <td><?= htmlspecialchars($produto['categoria']) ?></td>
                    <td>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
                    <td><a href="detalheProduto.php?id=<?= $produto['id'] ?>">Ver</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
