<?php
session_start();
include "connectDB.php"; // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Função para validar a senha
function validarSenha($senha) {
    return strlen($senha) >= 6;
}

// Função para validar os dados de entrada
function validarCampo($campo) {
    return trim($campo) !== "";
}

// Buscar os dados do usuário
$sql = "SELECT * FROM usuario WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Atualizar dados do usuário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['atualizar_dados'])) {
        $nome = $_POST['nome'];
        $data_nascimento = $_POST['data_nascimento'];
        $genero = $_POST['genero'];

        // Verificação simples para garantir que os campos não estão vazios
        if (validarCampo($nome) && validarCampo($data_nascimento) && validarCampo($genero)) {
            // Atualizar os dados do usuário
            $sqlUpdate = "UPDATE usuario SET nome = ?, data_nascimento = ?, genero = ? WHERE id = ?";
            $stmtUpdate = $mysqli->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sssi", $nome, $data_nascimento, $genero, $usuario_id);
            $stmtUpdate->execute();
            $sucesso = "Dados atualizados com sucesso!";
        } else {
            $erro = "Por favor, preencha todos os campos.";
        }
    }

    // Alteração de senha
    if (isset($_POST['alterar_senha'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        if (password_verify($senha_atual, $usuario['senha'])) {
            if (validarSenha($nova_senha) && $nova_senha === $confirmar_senha) {
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sqlSenha = "UPDATE usuario SET senha = ? WHERE id = ?";
                $stmtSenha = $mysqli->prepare($sqlSenha);
                $stmtSenha->bind_param("si", $senha_hash, $usuario_id);
                $stmtSenha->execute();
                $sucesso = "Senha alterada com sucesso!";
            } else {
                $erro = "As senhas não coincidem ou a nova senha não é válida.";
            }
        } else {
            $erro = "Senha atual incorreta.";
        }
    }

    // Adicionar endereço de entrega
    if (isset($_POST['adicionar_endereco'])) {
        $cep = $_POST['cep'];
        $logradouro = $_POST['logradouro'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $uf = $_POST['uf'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'] ?: NULL;  // Se não houver complemento, será NULL

        if (validarCampo($cep) && validarCampo($logradouro) && validarCampo($bairro) && validarCampo($cidade) && validarCampo($uf) && validarCampo($numero)) {
            // Corrigido para adicionar o tipo como 'entrega'
            $sqlEndereco = "INSERT INTO endereco (usuario_id, cep, logradouro, numero, complemento, bairro, cidade, uf, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'entrega')";
            $stmtEndereco = $mysqli->prepare($sqlEndereco);

            // Corrigido para ter os tipos de dados corretos
            $stmtEndereco->bind_param("isssssss", $usuario_id, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $uf);
            $stmtEndereco->execute();
            $sucesso = "Endereço de entrega adicionado com sucesso!";
        } else {
            $erro = "Por favor, preencha todos os campos de endereço.";
        }
    }

    // Marcar um endereço como padrão
    if (isset($_POST['definir_padrao'])) {
        $endereco_id = $_POST['endereco_id'];

        // Definir todos os endereços como não padrão
        $sqlAtualizarPadrao = "UPDATE endereco SET padrao = 0 WHERE usuario_id = ?";
        $stmtAtualizarPadrao = $mysqli->prepare($sqlAtualizarPadrao);
        $stmtAtualizarPadrao->bind_param("i", $usuario_id);
        $stmtAtualizarPadrao->execute();

        // Marcar o endereço selecionado como padrão
        $sqlDefinirPadrao = "UPDATE endereco SET padrao = 1 WHERE id = ?";
        $stmtDefinirPadrao = $mysqli->prepare($sqlDefinirPadrao);
        $stmtDefinirPadrao->bind_param("i", $endereco_id);
        $stmtDefinirPadrao->execute();

        $sucesso = "Endereço padrão atualizado com sucesso!";
    }
}

// Buscar os endereços de entrega do usuário
$sqlEndereco = "SELECT * FROM endereco WHERE usuario_id = ? AND tipo = 'entrega'";
$stmtEndereco = $mysqli->prepare($sqlEndereco);
$stmtEndereco->bind_param("i", $usuario_id);
$stmtEndereco->execute();
$resultEndereco = $stmtEndereco->get_result();
$enderecos = $resultEndereco->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Cliente</title>
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

<div class="container">
    <h1>Bem-vindo, <?php echo $usuario['nome']; ?>!</h1>

    <?php if (isset($erro)) echo "<p style='color: red;'>$erro</p>"; ?>
    <?php if (isset($sucesso)) echo "<p style='color: green;'>$sucesso</p>"; ?>

    <h2>Atualizar Dados</h2>
    <form action="dashboard.php" method="POST">
        <label for="nome">Nome:</label><br>
        <input type="text" name="nome" id="nome" value="<?php echo $usuario['nome']; ?>" required><br><br>

        <label for="data_nascimento">Data de Nascimento:</label><br>
        <input type="date" name="data_nascimento" id="data_nascimento" value="<?php echo $usuario['data_nascimento']; ?>" required><br><br>

        <label for="genero">Gênero:</label><br>
        <select name="genero" id="genero" required>
            <option value="masculino" <?php if ($usuario['genero'] == 'masculino') echo 'selected'; ?>>Masculino</option>
            <option value="feminino" <?php if ($usuario['genero'] == 'feminino') echo 'selected'; ?>>Feminino</option>
            <option value="outro" <?php if ($usuario['genero'] == 'outro') echo 'selected'; ?>>Outro</option>
        </select><br><br>

        <input type="submit" name="atualizar_dados" value="Atualizar Dados">
    </form>

    <h2>Alterar Senha</h2>
    <form action="dashboard.php" method="POST">
        <label for="senha_atual">Senha Atual:</label><br>
        <input type="password" name="senha_atual" id="senha_atual" required><br><br>

        <label for="nova_senha">Nova Senha:</label><br>
        <input type="password" name="nova_senha" id="nova_senha" required><br><br>

        <label for="confirmar_senha">Confirmar Nova Senha:</label><br>
        <input type="password" name="confirmar_senha" id="confirmar_senha" required><br><br>

        <input type="submit" name="alterar_senha" value="Alterar Senha">
    </form>

    <h2>Endereços de Entrega</h2>
    <?php if (count($enderecos) > 0) { ?>
        <ul>
            <?php foreach ($enderecos as $endereco) { ?>
                <li>
                    <?php echo $endereco['logradouro'] . ', ' . $endereco['numero'] . ' - ' . $endereco['bairro'] . ', ' . $endereco['cidade'] . ' - ' . $endereco['uf']; ?>
                    <?php if ($endereco['padrao'] == 1) { ?>
                        <span>(Endereço Padrão)</span>
                    <?php } else { ?>
                        <form action="dashboard.php" method="POST" style="display:inline;">
                            <input type="hidden" name="endereco_id" value="<?php echo $endereco['id']; ?>">
                            <input type="submit" name="definir_padrao" value="Definir como Padrão">
                        </form>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>Você não tem endereços de entrega cadastrados.</p>
    <?php } ?>

    <h2>Adicionar Endereço de Entrega</h2>
    <form action="dashboard.php" method="POST">
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
<script>
document.getElementById('cep').addEventListener('blur', function () {
    let cep = this.value.replace(/\D/g, '');

    if (cep.length !== 8) return;

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert("CEP não encontrado.");
                return;
            }

            document.getElementById('logradouro').value = data.logradouro || '';
            document.getElementById('bairro').value = data.bairro || '';
            document.getElementById('cidade').value = data.localidade || '';
            document.getElementById('uf').value = data.uf || '';
        })
        .catch(error => {
            console.error('Erro ao buscar o CEP:', error);
            alert("Erro ao buscar o CEP.");
        });
});
</script>

</body>
</html>
