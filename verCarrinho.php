<?php
session_start();

$carrinho = $_SESSION['carrinho'] ?? [];
$freteEscolhido = $_SESSION['frete'] ?? null;
$cepInformado = $_POST['cep'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['frete'])) {
    $_SESSION['frete'] = $_POST['frete'];
    $freteEscolhido = $_POST['frete'];
}

$subtotal = 0;
foreach ($carrinho as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}

$frete = 0;
if (empty($_SESSION['usuario_id'])) {
    if ($freteEscolhido) {
        switch ($freteEscolhido) {
            case 'frete1': $frete = 15.00; break;
            case 'frete2': $frete = 30.00; break;
            case 'frete3': $frete = 50.00; break;
        }
    }
} else {
    $frete = 10.00;
}

$total = $subtotal + $frete;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de Compras</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="home.php">
        <img src="Image/logo.png" alt="Logo da Loja" class="logo">
    </a>
    <div class="header-icons">
        <a href="verCarrinho.php" class="cart-icon">🛒 (<?php echo count($carrinho); ?>)</a>
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
                <?php foreach ($carrinho as $item): ?>
                    <tr>
                        <td><?php echo $item['nome']; ?></td>
                        <td>
                            <form method="POST" action="atualizarQuantidade.php" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantidade" value="<?php echo $item['quantidade']; ?>" min="1" style="width:60px;">
                                <button type="submit">Atualizar</button>
                            </form>
                        </td>
                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                        <td><a href="removerProdutoCarrinho.php?id=<?php echo $item['id']; ?>">Remover</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Subtotal: R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></h3>

        <form method="POST" action="">
            <h3>Calcular o frete para meu CEP</h3>
            <input type="text" name="cep" placeholder="Digite seu CEP" value="<?php echo htmlspecialchars($cepInformado); ?>" required style="width:200px;">
            <br><br>

            <h3>Escolha o Frete</h3>
            <label>
                <input type="radio" name="frete" value="frete1" <?php echo ($freteEscolhido == 'frete1') ? 'checked' : ''; ?>>
                Frete 1 - R$ 15,00 (Total: R$ <?php echo number_format($subtotal + 15.00, 2, ',', '.'); ?>)
            </label><br>
            <label>
                <input type="radio" name="frete" value="frete2" <?php echo ($freteEscolhido == 'frete2') ? 'checked' : ''; ?>>
                Frete 2 - R$ 30,00 (Total: R$ <?php echo number_format($subtotal + 30.00, 2, ',', '.'); ?>)
            </label><br>
            <label>
                <input type="radio" name="frete" value="frete3" <?php echo ($freteEscolhido == 'frete3') ? 'checked' : ''; ?>>
                Frete 3 - R$ 50,00 (Total: R$ <?php echo number_format($subtotal + 50.00, 2, ',', '.'); ?>)
            </label><br><br>

            <button type="submit">Atualizar Frete</button>
        </form>

        <h3>Frete Atual: R$ <?php echo number_format($frete, 2, ',', '.'); ?></h3>
        <h3>Total Atual: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>

        <a href="finalizarCompra.php" class="btn">Finalizar Compra</a>
    <?php endif; ?>
</main>
</body>
</html>

<style>
    body { font-family: Arial, sans-serif; }
    header { display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8f8f8; }
    .logo { height: 50px; }
    .header-icons { display: flex; gap: 15px; }
    .cart-icon { font-size: 24px; text-decoration: none; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 10px; text-align: center; }
    td a { color: red; text-decoration: none; }
</style>
