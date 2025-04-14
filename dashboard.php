<?php
session_start();
include "connectDB.php";

// Verifica se o usuário está logado, se não estiver, redireciona para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Verifica se foi solicitado o logoff
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset(); // Limpa todas as variáveis da sessão
    session_destroy(); // Destroi a sessão
    echo "<script>alert('Você saiu da sua conta.'); window.location.href = 'home.php';</script>"; // Alerta e redireciona para a página inicial
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

function validarSenha($senha) {
    return strlen($senha) >= 6;
}

function validarCampo($campo) {
    return trim($campo) !== "";
}

// Buscar dados do usuário
$sql = "SELECT * FROM usuario WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['atualizar_dados'])) {
        $nome = $_POST['nome'];
        $data_nascimento = $_POST['data_nascimento'];
        $genero = $_POST['genero'];

        if (validarCampo($nome) && validarCampo($data_nascimento) && validarCampo($genero)) {
            $sqlUpdate = "UPDATE usuario SET nome = ?, data_nascimento = ?, genero = ? WHERE id = ?";
            $stmtUpdate = $mysqli->prepare($sqlUpdate);
            $stmtUpdate->bind_param("sssi", $nome, $data_nascimento, $genero, $usuario_id);
            $stmtUpdate->execute();
            $sucesso = "Dados atualizados com sucesso!";
        } else {
            $erro = "Por favor, preencha todos os campos.";
        }
    }

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

    if (isset($_POST['adicionar_endereco'])) {
        $cep = $_POST['cep'];
        $logradouro = $_POST['logradouro'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $uf = $_POST['uf'];
        $numero = $_POST['numero'];
        $complemento = $_POST['complemento'];

        if (validarCampo($cep) && validarCampo($logradouro) && validarCampo($bairro) && validarCampo($cidade) && validarCampo($uf) && validarCampo($numero)) {
            $sqlEndereco = "INSERT INTO endereco (usuario_id, cep, logradouro, numero, complemento, bairro, cidade, uf, tipo, padrao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'entrega', 0)";
            $stmtEndereco = $mysqli->prepare($sqlEndereco);
            $stmtEndereco->bind_param("isssssss", $usuario_id, $cep, $logradouro, $numero, $complemento, $bairro, $cidade, $uf);
            $stmtEndereco->execute();
            $sucesso = "Endereço de entrega adicionado com sucesso!";
        } else {
            $erro = "Por favor, preencha todos os campos de endereço.";
        }
    }

    if (isset($_POST['definir_padrao'])) {
        $endereco_id = $_POST['endereco_padrao'];
        $mysqli->query("UPDATE endereco SET padrao = 0 WHERE usuario_id = $usuario_id AND tipo = 'entrega'");
        $stmtPadrao = $mysqli->prepare("UPDATE endereco SET padrao = 1 WHERE id = ? AND usuario_id = ?");
        $stmtPadrao->bind_param("ii", $endereco_id, $usuario_id);
        $stmtPadrao->execute();
        $sucesso = "Endereço padrão atualizado com sucesso!";
    }
}

// Buscar endereços
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

<main>
    <h1>Bem-vindo, <?php echo $usuario['nome']; ?>!</h1>
    <p>Aqui você pode editar seus dados e gerenciar seu perfil.</p>

    <!-- Link de logoff com confirmação -->
    <a href="dashboard.php?logout=true" onclick="return confirm('Tem certeza de que deseja sair da sua conta?');">Logoff</a>
</main>

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
                    <?php
                        echo $endereco['logradouro'] . ', ' . $endereco['numero'] . ' - ' . $endereco['bairro'] . ', ' . $endereco['cidade'] . ' - ' . $endereco['uf'];
                        if ($endereco['padrao']) echo " <strong>(Padrão)</strong>";
                    ?>
                </li>
            <?php } ?>
        </ul>

        <?php if (count($enderecos) > 1) { ?>
            <h3>Definir Endereço Padrão</h3>
            <form action="dashboard.php" method="POST">
                <select name="endereco_padrao" required>
                    <?php foreach ($enderecos as $endereco) { ?>
                        <option value="<?php echo $endereco['id']; ?>" <?php if ($endereco['padrao']) echo 'selected'; ?>>
                            <?php echo $endereco['logradouro'] . ', ' . $endereco['numero']; ?>
                        </option>
                    <?php } ?>
                </select>
                <br><br>
                <input type="submit" name="definir_padrao" value="Salvar como padrão">
            </form>
        <?php } ?>
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
// JavaScript para preencher automaticamente os campos de endereço com base no CEP
document.getElementById('cep').addEventListener('blur', function() {
    let cep = this.value.replace(/\D/g, '');

    if (cep.length !== 8) {
        alert('CEP inválido. Deve conter 8 dígitos.');
        return;
    }

    fetch('https://viacep.com.br/ws/' + cep + '/json/')
        .then(response => {
            if (!response.ok) throw new Error("Erro ao buscar CEP");
            return response.json();
        })
        .then(data => {
            if (data.erro) {
                alert('CEP não encontrado.');
                return;
            }

            document.getElementById('logradouro').value = data.logradouro;
            document.getElementById('bairro').value = data.bairro;
            document.getElementById('cidade').value = data.localidade;
            document.getElementById('uf').value = data.uf;
        })
        .catch(error => {
            console.error(error);
            alert('Erro ao consultar o CEP.');
        });
});
</script>

</body>
</html>
