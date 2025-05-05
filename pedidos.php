<?php
session_start();
require 'connectDB.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT id, data_criacao, total, status 
        FROM pedidos 
        WHERE usuario_id = ? 
        ORDER BY data_criacao DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus Pedidos</title>
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

        h1 {
            text-align: center;
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

        .btn {
            background: #333;
            color: #fff;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn:hover {
            background: #555;
        }
    </style>
</head>
<body>

<header>
    <a href="home.php"><img src="Image/logo.png" alt="Logo" class="logo"></a>
    <nav>
        <a href="home.php" class="btn">Voltar para Loja</a>
    </nav>
</header>

<main>
    <h1>Meus Pedidos</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nº Pedido</th>
                    <th>Data</th>
                    <th>Valor Total</th>
                    <th>Status</th>
                    <th>Detalhes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($pedido = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $pedido['id'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($pedido['data_criacao'])) ?></td>
                        <td>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></td>
                        <td><?= ucfirst($pedido['status']) ?></td>
                        <td>
                            <a href="detalhes_pedido.php?id=<?= $pedido['id'] ?>" class="btn">Ver</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Você ainda não fez nenhum pedido.</p>
    <?php endif; ?>
</main>

</body>
</html>
