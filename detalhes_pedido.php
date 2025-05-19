<?php
session_start();
require 'connectDB.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

if (!isset($_GET['id'])) {
    echo "Pedido não especificado.";
    exit();
}

$pedido_id = intval($_GET['id']);

// Buscar o pedido
$sqlPedido = "SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?";
$stmt = $mysqli->prepare($sqlPedido);
$stmt->bind_param("ii", $pedido_id, $usuario_id);
$stmt->execute();
$resultPedido = $stmt->get_result();

if ($resultPedido->num_rows === 0) {
    echo "Pedido não encontrado.";
    exit();
}

$pedido = $resultPedido->fetch_assoc();

// Buscar endereço de entrega
$sqlEndereco = "SELECT * FROM endereco WHERE id = ?";
$stmtEndereco = $mysqli->prepare($sqlEndereco);
$stmtEndereco->bind_param("i", $pedido['endereco_id']);
$stmtEndereco->execute();
$endereco = $stmtEndereco->get_result()->fetch_assoc();

// Buscar itens do pedido
$sqlItens = "
    SELECT pi.*, p.nome AS produto_nome 
    FROM pedido_itens pi
    JOIN produtos p ON pi.produto_id = p.id
    WHERE pi.pedido_id = ?
";
$stmtItens = $mysqli->prepare($sqlItens);
$stmtItens->bind_param("i", $pedido_id);
$stmtItens->execute();
$itens = $stmtItens->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Detalhes do Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }

        header {
            background: #fff;
            padding: 20px;
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

        h1, h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        th {
            background: #eee;
        }

        p {
            margin: 8px 0;
        }
    </style>
</head>
<body>

<header>
    <a href="home.php"><img src="Image/logo.png" alt="Logo" class="logo"></a>
</header>

<main>
    <h1>Pedido #<?= $pedido_id ?></h1>
    <p><strong>Status:</strong> <?= ucfirst($pedido['status']) ?></p>
    <p><strong>Data do Pedido:</strong> <?= date('d/m/Y H:i', strtotime($pedido['data_criacao'])) ?></p>

    <h2>Itens do Pedido</h2>
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
            <?php foreach ($itens as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['produto_nome']) ?></td>
                    <td><?= $item['quantidade'] ?></td>
                    <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                    <td>R$ <?= number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Endereço de Entrega</h2>
    <p><?= htmlspecialchars($endereco['logradouro']) ?>, <?= $endereco['numero'] ?></p>
    <p><?= htmlspecialchars($endereco['bairro']) ?> - <?= htmlspecialchars($endereco['cidade']) ?>/<?= $endereco['uf'] ?></p>
    <p>CEP: <?= $endereco['cep'] ?></p>

    <h2>Forma de Pagamento</h2>
    <p><?= ucfirst($pedido['pagamento_tipo']) ?></p>

    <h2>Resumo Financeiro</h2>
    <p>Frete: <strong>R$ <?= number_format($pedido['valor_frete'], 2, ',', '.') ?></strong></p>
    <h3>Total: <strong>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></strong></h3>
</main>

</body>
</html>
