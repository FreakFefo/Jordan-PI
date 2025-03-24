<?php
session_start();
require 'connectDB.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Produto não especificado.");
}

$produto_id = intval($_GET['id']);

// Buscar detalhes do produto
$sql_produto = "SELECT * FROM produtos WHERE id = ?";
$stmt_produto = $mysqli->prepare($sql_produto);
$stmt_produto->bind_param("i", $produto_id);
$stmt_produto->execute();
$result_produto = $stmt_produto->get_result();
$produto = $result_produto->fetch_assoc();

if (!$produto) {
    die("Produto não encontrado.");
}

// Buscar imagens do produto
$sql_imagens = "SELECT * FROM imagens_produto WHERE produto_id = ?";
$stmt_imagens = $mysqli->prepare($sql_imagens);
$stmt_imagens->bind_param("i", $produto_id);
$stmt_imagens->execute();
$result_imagens = $stmt_imagens->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <style>
        body { font-family: Arial, sans-serif; }
        .container { width: 80%; margin: auto; padding: 20px; }
        .product-container { display: flex; gap: 20px; }
        .carousel { width: 50%; }
        .product-details { width: 50%; }
        .carousel img { width: 100%; height: auto; }
        .price { font-size: 20px; color: #e63946; font-weight: bold; }
        .buy-button { background: green; color: white; padding: 10px; border: none; cursor: pointer; }
        .buy-button:hover { background: darkgreen; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
        
        <div class="product-container">
            <!-- Carrossel de imagens -->
            <div class="carousel slick-carousel">
                <?php while ($imagem = $result_imagens->fetch_assoc()) { ?>
                    <div><img src="<?php echo htmlspecialchars($imagem['caminho']); ?>" alt="Imagem do Produto"></div>
                <?php } ?>
            </div>

            <!-- Detalhes do Produto -->
            <div class="product-details">
                <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
                <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                <p><strong>Avaliação:</strong> <?php echo $produto['avaliacao']; ?> / 5.0</p>

                <!-- Formulário para adicionar ao carrinho -->
                <form action="carrinho.php" method="post">
                    <input type="hidden" name="id" value="<?php echo $produto_id; ?>">
                    <input type="hidden" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>">
                    <input type="hidden" name="preco" value="<?php echo $produto['preco']; ?>">
                    <button type="submit" class="buy-button">Comprar</button>
                </form>
            </div>
        </div>

        <a href="home.php">Voltar para a loja</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.slick-carousel').slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear'
            });
        });
    </script>
</body>
</html>
