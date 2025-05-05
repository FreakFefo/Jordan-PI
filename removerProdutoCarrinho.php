<?php
session_start();

// Verificar se o carrinho está vazio ou se o produto não foi passado
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho']) || !isset($_GET['id'])) {
    echo "Carrinho vazio ou produto não encontrado!";
    exit;
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if (isset($_SESSION['carrinho'][$id])) {
        unset($_SESSION['carrinho'][$id]);
    }
}

header('Location: verCarrinho.php');
exit();