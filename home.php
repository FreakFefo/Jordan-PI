<?php
session_start();
require 'connectDB.php'; // Conexão com o banco de dados

// Consulta para buscar os produtos com imagem principal
$sql = "SELECT p.id, p.nome, p.preco, i.caminho 
        FROM produtos p 
        LEFT JOIN imagens_produto i ON p.id = i.produto_id AND i.principal = 1 
        WHERE p.status = 1";
$result = $mysqli->query($sql);

// Função para realizar o logoff
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: home.php");
    exit();
}

// Contar itens no carrinho
$quantidadeTotal = 0;
if (isset($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $item) {
        $quantidadeTotal += $item['quantidade'];
    }
}

// Verifica se o cliente está logado
$usuario_logado = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja - Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="Image/logo.png" alt="Logo da Loja" class="logo">
        <div class="header-icons">
            <a href="verCarrinho.php" class="cart-icon">🛒 (<?php echo $quantidadeTotal; ?>)</a>
            <?php if ($usuario_logado): ?>
                <a href="dashboard.php">Ir para Dashboard</a>
                <a href="home.php?logout=true">Logoff</a>
            <?php else: ?>
                <a href="login.php">Faça login / Crie seu login</a>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <h1>Lista de Produtos</h1>
        <div class="product-container">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="product-card">
                    <img src="<?php echo $row['caminho']; ?>" alt="<?php echo $row['nome']; ?>">
                    <h2><?php echo $row['nome']; ?></h2>
                    <p>R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></p>
                    <a href="detalheProduto.php?id=<?php echo $row['id']; ?>" class="btn">Detalhes</a>
                    <form action="carrinho.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="hidden" name="nome" value="<?php echo $row['nome']; ?>">
                        <input type="hidden" name="preco" value="<?php echo $row['preco']; ?>">
                        <button type="submit" class="btn">Comprar</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>
</html>

<style>
    body { font-family: Arial, sans-serif; }
    header { display: flex; justify-content: space-between; align-items: center; padding: 10px; background: #f8f8f8; }
    .logo { height: 50px; }
    .header-icons { display: flex; gap: 15px; }
    .cart-icon { font-size: 24px; text-decoration: none; }
    .product-container { display: flex; flex-wrap: wrap; gap: 20px; padding: 20px; }
    .product-card { border: 1px solid #ddd; padding: 10px; width: 200px; text-align: center; }
    .product-card img { width: 100%; height: auto; }
    .btn { display: block; padding: 5px; background: blue; color: white; text-decoration: none; margin-top: 5px; }
</style>
