<?php
session_start();

$carrinho = $_SESSION['carrinho'] ?? [];
$freteEscolhido = $_SESSION['frete'] ?? null;
$cepInformado = $_POST['cep'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['frete'])) {
    $_SESSION['frete'] = $_POST['frete'];
    $freteEscolhido = $_POST['frete'];
}

$subtotal = 0;
foreach ($carrinho as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}

$frete = 0;
switch ($freteEscolhido) {
    case 'frete1': $frete = 15.00; break;
    case 'frete2': $frete = 30.00; break;
    case 'frete3': $frete = 50.00; break;
}

$total = $subtotal + $frete;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo {
            height: 50px;
        }
        .header-icons {
            display: flex;
            gap: 20px;
            font-size: 16px;
        }
        .header-icons a {
            text-decoration: none;
            color: #333;
        }
        main {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h1, h3 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fafafa;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #eee;
        }
        input[type="number"], input[type="text"] {
            padding: 5px;
            border: 1px solid #ccc;
            width: 60px;
        }
        button, .btn {
            background-color: #333;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
            border-radius: 4px;
        }
        button:hover, .btn:hover {
            background-color: #555;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        td a {
            color: red;
            text-decoration: none;
        }
    </style>
</head>
<body>
<header>
    <a href="home.php"><img src="Image/logo.png" alt="Logo da Loja" class="logo"></a>
    <div class="header-icons">
        <a href="verCarrinho.php">🛒 (<?php echo count($carrinho); ?>)</a>
        <a href="#">Faça login / Crie seu login</a>
    </div>
</header>

<main>
    <h1>Carrinho de Compras</h1>

    <?php if (empty($carrinho)): ?>
        <p>Seu carrinho está vazio no momento.</p>
        <a href="home.php" class="btn">Voltar para a loja</a>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                    <th>Subtotal</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrinho as $id => $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nome']); ?></td>
                        <td>
                            <form method="POST" action="atualizarQuantidade.php">
                                <input type="hidden" name="id" value="<?php echo $id; ?>">
                                <input type="number" name="quantidade" value="<?php echo $item['quantidade']; ?>" min="1">
                                <button type="submit">Atualizar</button>
                            </form>
                        </td>
                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                        <td><a href="removerProdutoCarrinho.php?id=<?php echo $id; ?>">Remover</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Subtotal: R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></h3>

        <form method="POST" action="">
            <h3>Calcular o frete para meu CEP</h3>
            <input type="text" name="cep" placeholder="Digite seu CEP" value="<?php echo htmlspecialchars($cepInformado); ?>" required>

            <h3>Escolha o Frete</h3>
            <label>
                <input type="radio" name="frete" value="frete1" <?php echo ($freteEscolhido == 'frete1') ? 'checked' : ''; ?> onchange="this.form.submit()">
                Frete 1 - R$ 15,00 (Total: R$ <?php echo number_format($subtotal + 15.00, 2, ',', '.'); ?>)
            </label>
            <label>
                <input type="radio" name="frete" value="frete2" <?php echo ($freteEscolhido == 'frete2') ? 'checked' : ''; ?> onchange="this.form.submit()">
                Frete 2 - R$ 30,00 (Total: R$ <?php echo number_format($subtotal + 30.00, 2, ',', '.'); ?>)
            </label>
            <label>
                <input type="radio" name="frete" value="frete3" <?php echo ($freteEscolhido == 'frete3') ? 'checked' : ''; ?> onchange="this.form.submit()">
                Frete 3 - R$ 50,00 (Total: R$ <?php echo number_format($subtotal + 50.00, 2, ',', '.'); ?>)
            </label>

            <button type="submit">Atualizar Frete</button>
        </form>

        <h3>Frete Atual: R$ <?php echo number_format($frete, 2, ',', '.'); ?></h3>
        <h3>Total Atual: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>

        <a href="verificaCheckout.php" class="btn">Finalizar Compra</a>
    <?php endif; ?>
</main>
</body>
</html>
