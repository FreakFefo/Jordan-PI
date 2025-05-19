<?php
session_start();
include "connectDB.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['endereco_id']) || !isset($_GET['pagamento_tipo'])) {
    header("Location: pagamento.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$endereco_id = $_GET['endereco_id'];
$pagamento_tipo = $_GET['pagamento_tipo'];

$stmt = $mysqli->prepare("SELECT * FROM endereco WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $endereco_id, $usuario_id);
$stmt->execute();
$resultEndereco = $stmt->get_result();
$endereco = $resultEndereco->fetch_assoc();

$carrinho = $_SESSION['carrinho'] ?? [];
$freteEscolhido = $_SESSION['frete'] ?? null;
$valorFrete = match($freteEscolhido) {
    'frete1' => 15.00,
    'frete2' => 30.00,
    'frete3' => 50.00,
    default => 0
};

$subtotal = 0;
foreach ($carrinho as $item) {
    $subtotal += $item['preco'] * $item['quantidade'];
}
$total = $subtotal + $valorFrete;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Resumo do Pedido</title>
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
        main {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h1, h2, h3 {
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
        .btn {
            background-color: #333;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<header>
    <a href="home.php"><img src="Image/logo.png" alt="Logo da Loja" class="logo"></a>
</header>

<main>
    <h1>Resumo do Pedido</h1>

    <?php if (empty($carrinho)): ?>
        <p>Seu carrinho está vazio.</p>
        <a href="home.php" class="btn">Voltar para a loja</a>
    <?php else: ?>
        <h2>Produtos</h2>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carrinho as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nome']) ?></td>
                        <td><?= $item['quantidade'] ?></td>
                        <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Endereço de Entrega</h2>
        <p>
            <?= htmlspecialchars($endereco['logradouro']) ?>, <?= htmlspecialchars($endereco['numero']) ?><br>
            <?= htmlspecialchars($endereco['bairro']) ?> - <?= htmlspecialchars($endereco['cidade']) ?>/<?= htmlspecialchars($endereco['uf']) ?><br>
            CEP: <?= htmlspecialchars($endereco['cep']) ?>
        </p>

        <h2>Forma de Pagamento</h2>
        <p><?= ucfirst($pagamento_tipo) ?></p>

        <h2>Resumo Financeiro</h2>
        <p>Subtotal: <strong>R$ <?= number_format($subtotal, 2, ',', '.') ?></strong></p>
        <p>Frete: <strong>R$ <?= number_format($valorFrete, 2, ',', '.') ?></strong></p>
        <h3>Total Geral: <strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></h3>

        <form action="concluir_compra.php" method="POST">
            <input type="hidden" name="endereco_id" value="<?= $endereco_id ?>">
            <input type="hidden" name="pagamento_tipo" value="<?= $pagamento_tipo ?>">
            <input type="submit" value="Concluir Compra" class="btn">
        </form>

        <a href="pagamento.php?endereco_id=<?= $endereco_id ?>" class="btn">Voltar</a>
    <?php endif; ?>
</main>

</body>
</html>
