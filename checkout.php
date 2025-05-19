<?php
session_start();
include "connectDB.php";

// Verifica se o usuário está logado, se não, redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtém o ID do usuário
$usuario_id = $_SESSION['usuario_id'];

// Buscar endereços de entrega do usuário
$sqlEndereco = "SELECT * FROM endereco WHERE usuario_id = ? AND tipo = 'entrega' ORDER BY padrao DESC";
$stmtEndereco = $mysqli->prepare($sqlEndereco);
$stmtEndereco->bind_param("i", $usuario_id);
$stmtEndereco->execute();
$resultEndereco = $stmtEndereco->get_result();
$enderecos = $resultEndereco->fetch_all(MYSQLI_ASSOC);

// Verifica se o formulário de checkout foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se um endereço foi selecionado
    if (isset($_POST['endereco_id']) && !empty($_POST['endereco_id'])) {
        $endereco_id = $_POST['endereco_id'];

        // Redireciona para a próxima etapa (pode ser uma página de pagamento, por exemplo)
        header("Location: pagamento.php?endereco_id=" . $endereco_id);
        exit();
    } else {
        $erro = "Por favor, selecione um endereço de entrega.";
    }

    // Verifica se o usuário deseja adicionar um novo endereço
    if (isset($_POST['adicionar_endereco'])) {
        // Obtém os dados do novo endereço
        $cep = $_POST['cep'];
        $logradouro = $_POST['logradouro'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $uf = $_POST['uf'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];

        // Valida os campos
        if (!empty($cep) && !empty($logradouro) && !empty($bairro) && !empty($cidade) && !empty($uf) && !empty($numero)) {
            // Insere o novo endereço
            $sqlEnderecoInsert = "INSERT INTO endereco (usuario_id, cep, logradouro, numero, complemento, bairro, cidade, uf, tipo, padrao) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'entrega', 0)";
            $stmtEnderecoInsert = $mysqli->prepare($sqlEnderecoInsert);
            $stmtEnderecoInsert->bind_param("isssssss", $usuario_id, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $uf);
            $stmtEnderecoInsert->execute();

            // Não chamamos mais get_result() após a inserção, para evitar o erro de comando fora de sincronia.

            // Carregar os endereços novamente após adicionar um novo
            $stmtEndereco->execute();
            $resultEndereco = $stmtEndereco->get_result();
            $enderecos = $resultEndereco->fetch_all(MYSQLI_ASSOC);

            $sucesso = "Endereço de entrega adicionado com sucesso!";
        } else {
            $erro = "Por favor, preencha todos os campos de endereço.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilo para esconder o formulário de adicionar endereço */
        #form-adicionar-endereco {
            display: none;
        }
    </style>
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
    <div class="checkout-container">
        <h1>Checkout - Escolha um Endereço de Entrega</h1>
        
        <?php
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
?>

<h2>Resumo do Frete</h2>
<table border="1" cellpadding="10" cellspacing="0" style="margin-bottom: 20px; width: 100%; border-collapse: collapse;">
    <tr style="background-color: #f0f0f0;">
        <th>Tipo de Frete</th>
        <th>Valor</th>
    </tr>
    <tr>
        <td><?php echo $freteTexto; ?></td>
        <td>R$ <?php echo number_format($freteValor, 2, ',', '.'); ?></td>
    </tr>
</table>


        <?php if (isset($erro)) echo "<p style='color: red;'>$erro</p>"; ?>
        <?php if (isset($sucesso)) echo "<p style='color: green;'>$sucesso</p>"; ?>

        <?php if (count($enderecos) > 0) { ?>
            <form action="checkout.php" method="POST">
                <div class="enderecos">
                    <?php foreach ($enderecos as $endereco) { ?>
                        <div class="endereco">
                            <label>
                                <input type="radio" name="endereco_id" value="<?php echo $endereco['id']; ?>" <?php if ($endereco['padrao']) echo 'checked'; ?>>
                                <?php echo $endereco['logradouro'] . ', ' . $endereco['numero'] . ' - ' . $endereco['bairro'] . ', ' . $endereco['cidade'] . ' - ' . $endereco['uf']; ?>
                                <?php if ($endereco['padrao']) echo " <strong>(Padrão)</strong>"; ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>

                <div class="checkout-actions">
                    <input type="submit" value="Avançar para Pagamento" <?php if (empty($enderecos)) echo 'disabled'; ?>>
                </div>
            </form>
        <?php } else { ?>
            <p>Você não tem endereços de entrega cadastrados. Por favor, adicione um endereço de entrega.</p>
        <?php } ?>

        <!-- Botão para mostrar o formulário de adicionar endereço -->
        <button type="button" onclick="mostrarFormularioEndereco()">Adicionar Novo Endereço</button>

        <!-- Formulário para adicionar novo endereço (escondido por padrão) -->
        <div id="form-adicionar-endereco">
            <h2>Adicionar Novo Endereço</h2>
            <form action="checkout.php" method="POST">
                <label for="cep">CEP:</label><br>
                <input type="text" name="cep" id="cep" required><br><br>

                <label for="logradouro">Logradouro:</label><br>
                <input type="text" name="logradouro" id="logradouro" required><br><br>

                <label for="bairro">Bairro:</label><br>
                <input type="text" name="bairro" id="bairro" required><br><br>

                <label for="cidade">Cidade:</label><br>
                <input type="text" name="cidade" id="cidade" required><br><br>

                <label for="uf">Estado (UF):</label><br>
                <input type="text" name="uf" id="uf" required><br><br>

                <label for="numero">Número:</label><br>
                <input type="text" name="numero" id="numero" required><br><br>

                <label for="complemento">Complemento:</label><br>
                <input type="text" name="complemento" id="complemento"><br><br>

                <input type="submit" name="adicionar_endereco" value="Adicionar Endereço">
            </form>
        </div>
    </div>
</main>

<script>
    // Função para mostrar o formulário de adicionar endereço
    function mostrarFormularioEndereco() {
        var formulario = document.getElementById('form-adicionar-endereco');
        if (formulario.style.display === 'none' || formulario.style.display === '') {
            formulario.style.display = 'block';
        } else {
            formulario.style.display = 'none';
        }
    }
</script>




</body>
</html>
