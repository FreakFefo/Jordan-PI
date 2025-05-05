<?php
session_start();
include "connectDB.php";

// Verifica se o usuário está logado, se não, redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica se o ID do endereço foi passado
if (!isset($_GET['endereco_id'])) {
    header("Location: checkout.php");
    exit();
}

$endereco_id = $_GET['endereco_id'];
$usuario_id = $_SESSION['usuario_id'];

// Buscar endereço de entrega do usuário
$sqlEndereco = "SELECT * FROM endereco WHERE id = ? AND usuario_id = ?";
$stmtEndereco = $mysqli->prepare($sqlEndereco);
$stmtEndereco->bind_param("ii", $endereco_id, $usuario_id);
$stmtEndereco->execute();
$resultEndereco = $stmtEndereco->get_result();
$endereco = $resultEndereco->fetch_assoc();

// Verifica se o formulário de pagamento foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se uma forma de pagamento foi selecionada
    if (isset($_POST['pagamento_tipo'])) {
        $pagamento_tipo = $_POST['pagamento_tipo'];

        // Verifica os campos dependendo da forma de pagamento selecionada
        if ($pagamento_tipo == 'cartao') {
            // Valida os dados do cartão
            $numero_cartao = $_POST['numero_cartao'];
            $codigo_verificador = $_POST['codigo_verificador'];
            $nome_completo = $_POST['nome_completo'];
            $data_vencimento = $_POST['data_vencimento'];
            $parcelas = $_POST['parcelas'];

            if (empty($numero_cartao) || empty($codigo_verificador) || empty($nome_completo) || empty($data_vencimento) || empty($parcelas)) {
                $erro = "Por favor, preencha todos os campos do cartão de crédito.";
            } else {
                // Processar o pagamento com cartão de crédito (em um cenário real, aqui seria a integração com o gateway de pagamento)

                // Simulando que o pagamento foi processado com sucesso
                $sucesso = "Pagamento com cartão de crédito realizado com sucesso!";

                // Redirecionar para a próxima etapa (validar pedido final)
                header("Location: validar_pedido.php?endereco_id=" . $endereco_id . "&pagamento_tipo=cartao");
                exit();
            }
        } elseif ($pagamento_tipo == 'boleto') {
            // Para boleto, não é necessário mais validações
            // Simula que o pagamento com boleto foi realizado com sucesso
            $sucesso = "Pagamento com boleto gerado com sucesso!";
            
            // Redireciona para a próxima etapa
            header("Location: validar_pedido.php?endereco_id=" . $endereco_id . "&pagamento_tipo=boleto");
            exit();
        } else {
            $erro = "Por favor, selecione uma forma de pagamento.";
        }
    } else {
        $erro = "Por favor, selecione uma forma de pagamento.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="header-container">
        <a href="home.php">
            <img src="Image/logo.png" alt="Logo" class="logo">
        </a>
    </div>
</header>

<main>
    <div class="pagamento-container">
        <h1>Pagamento - Escolha a Forma de Pagamento</h1>
        <?php
// Mostrar endereço de entrega
if ($endereco) {
    echo "<h2>Endereço de Entrega</h2>";
    echo "<p>{$endereco['logradouro']}, {$endereco['numero']}";
    if (!empty($endereco['complemento'])) echo " - {$endereco['complemento']}";
    echo "<br>{$endereco['bairro']} - {$endereco['cidade']} / {$endereco['uf']} - CEP: {$endereco['cep']}</p>";
}

// Mostrar frete escolhido
$freteEscolhido = $_SESSION['frete'] ?? null;
$freteTexto = "Não selecionado";
$freteValor = 0.00;

switch ($freteEscolhido) {
    case 'frete1':
        $freteTexto = "Frete Econômico";
        $freteValor = 15.00;
        break;
    case 'frete2':
        $freteTexto = "Frete Expresso";
        $freteValor = 30.00;
        break;
    case 'frete3':
        $freteTexto = "Frete Premium";
        $freteValor = 50.00;
        break;
}

echo "<h2>Frete Selecionado</h2>";
echo "<table border='1' cellpadding='10' cellspacing='0' style='margin-bottom: 20px; width: 100%; border-collapse: collapse;'>
        <tr style='background-color: #f0f0f0;'>
            <th>Tipo de Frete</th>
            <th>Valor</th>
        </tr>
        <tr>
            <td>$freteTexto</td>
            <td>R$ " . number_format($freteValor, 2, ',', '.') . "</td>
        </tr>
      </table>";
?>


        <?php if (isset($erro)) echo "<p style='color: red;'>$erro</p>"; ?>
        <?php if (isset($sucesso)) echo "<p style='color: green;'>$sucesso</p>"; ?>

        <form action="pagamento.php?endereco_id=<?php echo $endereco_id; ?>" method="POST">
            <div class="form-group">
                <label>
                    <input type="radio" name="pagamento_tipo" value="boleto" required>
                    Boleto Bancário
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="radio" name="pagamento_tipo" value="cartao">
                    Cartão de Crédito
                </label>
            </div>

            <!-- Campos para cartão de crédito -->
            <div id="cartao" style="display: none;">
                <h2>Dados do Cartão de Crédito</h2>
                <label for="numero_cartao">Número do Cartão:</label><br>
                <input type="text" name="numero_cartao" id="numero_cartao" placeholder="XXXX XXXX XXXX XXXX"><br><br>

                <label for="codigo_verificador">Código Verificador (CVV):</label><br>
                <input type="text" name="codigo_verificador" id="codigo_verificador" placeholder="XXX"><br><br>

                <label for="nome_completo">Nome Completo:</label><br>
                <input type="text" name="nome_completo" id="nome_completo"><br><br>

                <label for="data_vencimento">Data de Vencimento:</label><br>
                <input type="text" name="data_vencimento" id="data_vencimento" placeholder="MM/AAAA"><br><br>

                <label for="parcelas">Quantidade de Parcelas:</label><br>
                <input type="number" name="parcelas" id="parcelas" min="1" max="12"><br><br>
            </div>

            <div class="checkout-actions">
                <input type="submit" value="Finalizar Pagamento" />
            </div>
        </form>
    </div>
</main>

<script>
    // Exibir os campos de cartão de crédito apenas quando a opção for selecionada
    const pagamentoCartao = document.querySelector('input[value="cartao"]');
    const campoCartao = document.getElementById('cartao');

    pagamentoCartao.addEventListener('change', function() {
        if (this.checked) {
            campoCartao.style.display = 'block';
        }
    });

    const pagamentoBoleto = document.querySelector('input[value="boleto"]');

    pagamentoBoleto.addEventListener('change', function() {
        if (this.checked) {
            campoCartao.style.display = 'none';
        }
    });
</script>

</body>
</html>
