<?php
session_start();
require 'connectDB.php';

// Proteção opcional: apenas admin pode editar
// if ($_SESSION['tipo_usuario'] !== 'admin') {
//     die("Acesso negado.");
// }

if (!isset($_GET['id'])) {
    die("Pedido não especificado.");
}

$pedido_id = intval($_GET['id']);

// Buscar o pedido
$sql = "SELECT * FROM pedidos WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $pedido_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Pedido não encontrado.");
}

$pedido = $result->fetch_assoc();

// Lista de status possíveis
$statusList = [
    'aguardando pagamento',
    'pagamento rejeitado',
    'pagamento com sucesso',
    'aguardando retirada',
    'em transito',
    'entregue'
];

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novo_status = $_POST['status'] ?? '';

    if (!in_array($novo_status, $statusList)) {
        die("Status inválido.");
    }

    $updateSql = "UPDATE pedidos SET status = ? WHERE id = ?";
    $stmtUpdate = $mysqli->prepare($updateSql);
    $stmtUpdate->bind_param("si", $novo_status, $pedido_id);

    if ($stmtUpdate->execute()) {
        header("Location: todos_pedidos.php?msg=sucesso");
        exit();
    } else {
        echo "Erro ao atualizar o status: " . $stmtUpdate->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: #f4f4f4;
        }

        h1 {
            margin-bottom: 20px;
        }

        form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
        }

        select, button {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
        }

        a {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>
<body>

<h1>Editar Pedido #<?= $pedido_id ?></h1>

<form method="POST">
    <label for="status">Status do Pedido:</label>
    <select name="status" id="status" required>
        <?php foreach ($statusList as $status): ?>
            <option value="<?= $status ?>" <?= ($pedido['status'] === $status) ? 'selected' : '' ?>>
                <?= ucfirst($status) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Salvar</button>
</form>

<a href="todos_pedidos.php">← Voltar para lista de pedidos</a>

</body>
</html>
