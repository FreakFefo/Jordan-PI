<?php
session_start();

if (empty($_SESSION['usuario_id'])) {
    // Redireciona para a tela de login do cliente
    header("Location: login.php");
    exit();
} else {
    // Cliente logado, inicia checkout
    header("Location: checkout.php");
    exit();
}
