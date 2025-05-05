<?php
session_start();
require 'connectDB.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$endereco_id = $_POST['endereco_id'] ?? null;
$pagamento_tipo = $_POST['pagamento_tipo'] ?? null;
$carrinho = $_SESSION['carrinho'] ?? [];

if (!$endereco_id || !$pagamento_tipo || empty($carrinho)) {
    die("Dados incompletos para concluir a compra.");
}

// Calcular total (subtotal + frete)
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

// Inserir pedido
$stmtPedido = $mysqli->prepare("INSERT INTO pedidos (usuario_id, endereco_id, pagamento_tipo, total) VALUES (?, ?, ?, ?)");
if (!$stmtPedido) {
    die("Erro ao preparar pedido: " . $mysqli->error);
}
$stmtPedido->bind_param("iisd", $usuario_id, $endereco_id, $pagamento_tipo, $total);
if (!$stmtPedido->execute()) {
    die("Erro ao salvar pedido: " . $stmtPedido->error);
}

$pedido_id = $stmtPedido->insert_id;
$stmtPedido->close();

// Inserir itens
$stmtItem = $mysqli->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
if (!$stmtItem) {
    die("Erro ao preparar itens: " . $mysqli->error);
}

foreach ($carrinho as $id => $item) {
    $produto_id = $id;
    $quantidade = $item['quantidade'];
    $preco_unitario = $item['preco'];

    if (empty($produto_id)) {
        echo "Produto com nome '" . htmlspecialchars($item['nome']) . "' possui ID inválido.<br>";
        continue;
    }

    $stmtItem->bind_param("iiid", $pedido_id, $produto_id, $quantidade, $preco_unitario);
    if (!$stmtItem->execute()) {
        echo "Erro ao salvar item: " . $stmtItem->error . "<br>";
    }
}
$stmtItem->close();

// Limpar carrinho
unset($_SESSION['carrinho']);
unset($_SESSION['frete']);

// Mensagem final
echo "<h1>Compra realizada com sucesso!</h1>";
echo "<p>Número do pedido: <strong>$pedido_id</strong></p>";
echo "<p>Valor total: <strong>R$ " . number_format($total, 2, ',', '.') . "</strong></p>";
echo "<a href='home.php'>Voltar para a loja</a>";
?>
