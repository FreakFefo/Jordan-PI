<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $novaQuantidade = intval($_POST['quantidade']);

    if ($id > 0 && $novaQuantidade > 0 && isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id]['quantidade'] = $novaQuantidade;
    }
}

header('Location: verCarrinho.php');
exit;
