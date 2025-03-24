<?php
session_start();

// Verificar se o carrinho está vazio ou se o produto não foi passado
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho']) || !isset($_GET['id'])) {
    echo "Carrinho vazio ou produto não encontrado!";
    exit;
}

// Obter o ID do produto a ser removido
$produtoId = $_GET['id'];

// Encontrar o índice do produto no carrinho
foreach ($_SESSION['carrinho'] as $key => $item) {
    if ($item['id'] == $produtoId) {
        // Remover o produto do carrinho
        unset($_SESSION['carrinho'][$key]);
        break;
    }
}

// Reindexar o array para garantir que os índices do carrinho sejam corrigidos
$_SESSION['carrinho'] = array_values($_SESSION['carrinho']);

// Redirecionar para a página do carrinho ou home
header("Location: verCarrinho.php");
exit;

?>
