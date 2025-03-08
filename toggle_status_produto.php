<?php
include "connectDB.php";

// Verifica se o ID do produto foi passado
if (!isset($_GET['id'])) {
    echo "ID do produto não fornecido.";
    exit;
}

$produto_id = intval($_GET['id']);

// Obtém o status atual do produto
$stmt = $mysqli->prepare("SELECT status FROM produtos WHERE id = ?");
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$result = $stmt->get_result();
$produto = $result->fetch_assoc();
$stmt->close();

if (!$produto) {
    echo "Produto não encontrado.";
    exit;
}

// Alterna o status (1 → 0 e 0 → 1)
$novo_status = ($produto['status'] == 1) ? 0 : 1;

$stmt = $mysqli->prepare("UPDATE produtos SET status = ? WHERE id = ?");
$stmt->bind_param("ii", $novo_status, $produto_id);
$stmt->execute();
$stmt->close();

// Redireciona de volta para a lista de produtos
header("Location: listarprodutos.php");
exit;
?>
