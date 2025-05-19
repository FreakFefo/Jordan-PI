<?php
session_start();
require 'connectDB.php';

// Se necessário, verifique se o usuário tem permissão (ex: admin)
// if ($_SESSION['tipo_usuario'] !== 'admin') {
//     die("Acesso negado.");
// }

$sql = "SELECT * FROM pedidos ORDER BY data_criacao DESC";
$result = $mysqli->query($sql);

if (!$result) {
    die("Erro ao buscar pedidos: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <li><a href="backofficeestoquista.php"><- Menu</a></li>
    <title>Lista de Pedidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .btn-editar {
            background: #007bff;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-editar:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<h1>Todos os Pedidos</h1>

<table>
    <thead>
        <tr>
            <th>Nº do Pedido</th>
            <th>Data do Pedido</th>
            <th>Valor Total (R$)</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($pedido = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $pedido['id'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($pedido['data_criacao'])) ?></td>
                <td><?= number_format($pedido['total'], 2, ',', '.') ?></td>
                <td><?= ucfirst($pedido['status']) ?></td>
                <td>
                    <a class="btn-editar" href="editar_pedido.php?id=<?= $pedido['id'] ?>">Editar</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
