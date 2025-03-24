<?php
session_start();

// Verificar se o carrinho existe
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    echo "Carrinho vazio!";
    exit;
}

// Verificar se o formulário foi submetido para selecionar o frete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['frete'])) {
    $_SESSION['frete'] = $_POST['frete'];  // Armazenar a escolha de frete na sessão
}

// Calcular o subtotal do carrinho
$subtotal = 0;
foreach ($_SESSION['carrinho'] as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}

// Definir o valor do frete, se o cliente estiver logado ou não
$frete = 0;
$freteEscolhido = isset($_SESSION['frete']) ? $_SESSION['frete'] : null;

// Se o cliente não estiver logado, ele pode escolher o frete
if (empty($_SESSION['usuario_id'])) {
    if ($freteEscolhido) {
        switch ($freteEscolhido) {
            case 'frete1':
                $frete = 15.00;  // Exemplo de valor de frete 1
                break;
            case 'frete2':
                $frete = 30.00;  // Exemplo de valor de frete 2
                break;
            case 'frete3':
                $frete = 50.00;  // Exemplo de valor de frete 3
                break;
        }
    }
} else {
    // Para cliente logado, o frete pode ser diferente ou até mesmo gratuito
    // Por exemplo, pode ser gratuito ou com base em outro critério
    $frete = 10.00;  // Valor do frete fixo para logados (modifique conforme necessário)
}

// Calcular o total
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
        <img src="Image/logo.png" alt="Logo da Loja" class="logo">
        <div class="header-icons">
            <a href="verCarrinho.php" class="cart-icon">🛒 (<?php echo count($_SESSION['carrinho']); ?>)</a>
            <a href="#">Faça login / Crie seu login</a>
        </div>
    </header>

    <main>
        <h1>Carrinho de Compras</h1>
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
                <?php foreach ($_SESSION['carrinho'] as $item) : ?>
                    <tr>
                        <td><?php echo $item['nome']; ?></td>
                        <td><?php echo $item['quantidade']; ?></td>
                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($item['preco'] * $item['quantidade'], 2, ',', '.'); ?></td>
                        <td><a href="removerProdutoCarrinho.php?id=<?php echo $item['id']; ?>">Remover</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Subtotal: R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></h3>

        <!-- Exibição do frete -->
        <form method="POST" action="">
            <h3>Escolha o Frete</h3>
            <label for="frete1">
                <input type="radio" name="frete" id="frete1" value="frete1" <?php echo ($freteEscolhido == 'frete1') ? 'checked' : ''; ?>>
                Frete 1 - R$ 15,00
            </label><br>
            <label for="frete2">
                <input type="radio" name="frete" id="frete2" value="frete2" <?php echo ($freteEscolhido == 'frete2') ? 'checked' : ''; ?>>
                Frete 2 - R$ 30,00
            </label><br>
            <label for="frete3">
                <input type="radio" name="frete" id="frete3" value="frete3" <?php echo ($freteEscolhido == 'frete3') ? 'checked' : ''; ?>>
                Frete 3 - R$ 50,00
            </label><br>
            <button type="submit">Atualizar Frete</button>
        </form>

        <h3>Frete: R$ <?php echo number_format($frete, 2, ',', '.'); ?></h3>
        <h3>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>

        <a href="finalizarCompra.php">Finalizar Compra</a>
    </main>
</body>
</html>
