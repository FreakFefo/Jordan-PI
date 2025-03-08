<?php
// Conexão com o banco de dados
include "connectDB.php";

// Verificar se o ID do produto foi passado pela URL
if (isset($_GET['id'])) {
    $produto_id = intval($_GET['id']);
    
    // Buscar os detalhes do produto
    $sql_produto = "SELECT * FROM produtos WHERE id = ?";
    $stmt_produto = $mysqli->prepare($sql_produto);
    $stmt_produto->bind_param("i", $produto_id);
    $stmt_produto->execute();
    $produto = $stmt_produto->get_result()->fetch_assoc();
    
    if (!$produto) {
        echo "Produto não encontrado.";
        exit;
    }
    
    // Buscar as imagens associadas ao produto
    $sql_imagens = "SELECT * FROM imagens_produto WHERE produto_id = ?";
    $stmt_imagens = $mysqli->prepare($sql_imagens);
    $stmt_imagens->bind_param("i", $produto_id);
    $stmt_imagens->execute();
    $imagens = $stmt_imagens->get_result();
} else {
    echo "Produto não especificado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Produto - ADM</title>

    <!-- Adicionando o CSS para o carrossel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">

    <style>
        .product-details {
            display: flex;
            margin-bottom: 30px;
        }
        .product-images {
            width: 50%;
            margin-right: 20px;
        }
        .product-info {
            width: 50%;
        }
        .slick-slide img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
        }
        .product-info h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .product-info p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .product-info .price {
            font-size: 20px;
            color: #e63946;
            font-weight: bold;
        }
        .buy-button {
            background-color: #ff6347;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: not-allowed;
            opacity: 0.6;
            font-size: 16px;
        }
        .buy-button:disabled {
            background-color: #ddd;
        }
    </style>

</head>
<body>
    <h1>Detalhes do Produto</h1>

    <div class="product-details">
        <!-- Carrossel de Imagens -->
        <div class="product-images">
            <div class="slick-carousel">
                <?php while ($imagem = $imagens->fetch_assoc()) { ?>
                    <div><img src="<?php echo $imagem['caminho']; ?>" alt="Imagem do Produto"></div>
                <?php } ?>
            </div>
        </div>

        <!-- Detalhes do Produto -->
        <div class="product-info">
            <h2><?php echo htmlspecialchars($produto['nome']); ?></h2>
            <p><strong>Descrição:</strong> <?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
            <p class="price">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
            <p><strong>Avaliação:</strong> <?php echo $produto['avaliacao']; ?> / 5.0</p>

            <!-- Botão de comprar (desabilitado) -->
            <button class="buy-button" disabled>Comprar</button>
        </div>
    </div>

    <a href="listarprodutos.php">Voltar para a lista de produtos</a>

    <!-- Incluindo o jQuery e o Slick Carousel -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    
    <script>
        $(document).ready(function(){
            // Inicializando o carrossel
            $('.slick-carousel').slick({
                dots: true,        // Exibe os pontos de navegação
                infinite: true,    // Carrossel infinito
                speed: 500,        // Velocidade da transição
                fade: true,        // Transição com efeito de fade
                cssEase: 'linear'  // Estilo de transição
            });
        });
    </script>
</body>
</html>
