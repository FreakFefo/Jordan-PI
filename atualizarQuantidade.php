<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['quantidade'])) {
    $id = $_POST['id'];
    $novaQuantidade = (int) $_POST['quantidade'];

    if ($novaQuantidade <= 0) {
        foreach ($_SESSION['carrinho'] as $index => $item) {
            if ($item['id'] == $id) {
                unset($_SESSION['carrinho'][$index]);
                break;
            }
        }
    } else {
        foreach ($_SESSION['carrinho'] as $index => $item) {
            if ($item['id'] == $id) {
                $_SESSION['carrinho'][$index]['quantidade'] = $novaQuantidade;
                break;
            }
        }
    }
}

header('Location: verCarrinho.php');
exit;
