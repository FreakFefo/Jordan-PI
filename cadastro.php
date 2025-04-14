<?php
session_start();
include "connectDB.php"; // Conexão com o banco de dados

// Função para validar CPF
function validarCPF($cpf) {
    // Remove qualquer caractere não numérico
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Valida o CPF com os cálculos de verificação
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

// Função para validar o formato do nome completo
function validarNome($nome) {
    $nomes = explode(' ', $nome);
    return count($nomes) >= 2 && strlen($nomes[0]) >= 3 && strlen($nomes[1]) >= 3;
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $data_nascimento = $_POST['data_nascimento'];
    $genero = $_POST['genero'];
    $senha = $_POST['senha'];
    $endereco_faturamento = $_POST['endereco_faturamento'];
    $cep_faturamento = $_POST['cep_faturamento'];
    $logradouro_faturamento = $_POST['logradouro_faturamento'];
    $bairro_faturamento = $_POST['bairro_faturamento'];
    $cidade_faturamento = $_POST['cidade_faturamento'];
    $uf_faturamento = $_POST['uf_faturamento'];
    $numero_faturamento = $_POST['numero_faturamento'];

    // Verifica se o usuário quer usar o mesmo endereço de faturamento ou adicionar um endereço de entrega
    $usar_endereco_entrega = isset($_POST['usar_endereco_entrega']) ? $_POST['usar_endereco_entrega'] : null;
    $endereco_entrega = null;
    $cep_entrega = null;
    $logradouro_entrega = null;
    $bairro_entrega = null;
    $cidade_entrega = null;
    $uf_entrega = null;
    $numero_entrega = null;

    if (!$usar_endereco_entrega) {
        // Se o usuário deseja adicionar um endereço de entrega, coleta os dados
        $endereco_entrega = $_POST['endereco_entrega'];
        $cep_entrega = $_POST['cep_entrega'];
        $logradouro_entrega = $_POST['logradouro_entrega'];
        $bairro_entrega = $_POST['bairro_entrega'];
        $cidade_entrega = $_POST['cidade_entrega'];
        $uf_entrega = $_POST['uf_entrega'];
        $numero_entrega = $_POST['numero_entrega'];
    }

    // Valida os campos
    if (!validarNome($nome)) {
        $erro = "O nome completo deve ter pelo menos duas palavras, com no mínimo 3 letras em cada uma.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Email inválido.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif (!validarCPF($cpf)) {
        $erro = "CPF inválido.";
    } elseif (empty($endereco_faturamento) || empty($cep_faturamento) || empty($logradouro_faturamento) || empty($bairro_faturamento) || empty($cidade_faturamento) || empty($uf_faturamento) || empty($numero_faturamento)) {
        $erro = "O endereço de faturamento é obrigatório.";
    } else {
        // Validação do CEP utilizando a API ViaCEP
        $cep = $cep_faturamento;
        $json = file_get_contents("https://viacep.com.br/ws/$cep/json/");
        $dadosCEP = json_decode($json, true);

        if (isset($dadosCEP['erro'])) {
            $erro = "CEP de faturamento inválido.";
        } else {
            // Preenche os campos com os dados do ViaCEP
            $logradouro_faturamento = $dadosCEP['logradouro'];
            $bairro_faturamento = $dadosCEP['bairro'];
            $cidade_faturamento = $dadosCEP['localidade'];
            $uf_faturamento = $dadosCEP['uf'];

            // Verifica se o email já está cadastrado
            $sql = "SELECT * FROM usuario WHERE email = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $erro = "Este email já está cadastrado.";
            } else {
                // Verifica se o CPF já está cadastrado
                $sql = "SELECT * FROM usuario WHERE cpf = ?";
                $stmt = $mysqli->prepare($sql);
                $stmt->bind_param("s", $cpf);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $erro = "Este CPF já está cadastrado.";
                } else {
                    // Encriptação da senha
                    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

                    // Insere o cliente no banco de dados
                    $sql = "INSERT INTO usuario (email, senha, grupo, ativo, nome, cpf) VALUES (?, ?, 'CLI', 1, ?, ?)";
                    $stmt = $mysqli->prepare($sql);
                    $stmt->bind_param("ssss", $email, $senhaHash, $nome, $cpf);
                    if ($stmt->execute()) {
                        // Insere o endereço de faturamento
                        $userId = $stmt->insert_id;
                        $sqlEndereco = "INSERT INTO endereco (usuario_id, cep, logradouro, numero, complemento, bairro, cidade, uf, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'faturamento')";
                        $stmtEndereco = $mysqli->prepare($sqlEndereco);
                        $stmtEndereco->bind_param("isssssss", $userId, $cep_faturamento, $logradouro_faturamento, $numero_faturamento, $_POST['complemento_faturamento'], $bairro_faturamento, $cidade_faturamento, $uf_faturamento);
                        $stmtEndereco->execute();

                        // Insere o endereço de entrega, caso o usuário tenha escolhido
                        if ($usar_endereco_entrega && !empty($endereco_entrega) && !empty($cep_entrega)) {
                            // Validação do CEP de entrega utilizando a API ViaCEP
                            $cepEntrega = $cep_entrega;
                            $jsonEntrega = file_get_contents("https://viacep.com.br/ws/$cepEntrega/json/");
                            $dadosCEPEntrega = json_decode($jsonEntrega, true);

                            if (isset($dadosCEPEntrega['erro'])) {
                                $erro = "CEP de entrega inválido.";
                            } else {
                                $logradouro_entrega = $dadosCEPEntrega['logradouro'];
                                $bairro_entrega = $dadosCEPEntrega['bairro'];
                                $cidade_entrega = $dadosCEPEntrega['localidade'];
                                $uf_entrega = $dadosCEPEntrega['uf'];

                                // Insere o endereço de entrega
                                $sqlEnderecoEntrega = "INSERT INTO endereco (usuario_id, cep, logradouro, numero, complemento, bairro, cidade, uf, tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'entrega')";
                                $stmtEnderecoEntrega = $mysqli->prepare($sqlEnderecoEntrega);
                                $stmtEnderecoEntrega->bind_param("isssssss", $userId, $cep_entrega, $logradouro_entrega, $numero_entrega, $_POST['complemento_entrega'], $bairro_entrega, $cidade_entrega, $uf_entrega);
                                $stmtEnderecoEntrega->execute();
                            }
                        }

                        // Redireciona para a tela de login
                        header("Location: login.php");
                        exit();
                    } else {
                        $erro = "Erro ao cadastrar. Tente novamente.";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente</title>


        <link rel="stylesheet" href="style.css">
    </style>
</head>
<body>

<!-- Header -->
<header>
    <div class="header-container">
        <a href="home.php">
            <img src="Image/logo.png" alt="Logo" class="logo">
        </a>
    </div>
</header>

<!-- Conteúdo do Formulário de Cadastro -->
<div class="container">
    <h1>Cadastro de Cliente</h1>

    <?php if (isset($erro)) { echo "<p style='color: red;'>$erro</p>"; } ?>

    <form action="cadastro.php" method="post">
        <label for="nome">Nome Completo:</label><br>
        <input type="text" name="nome" id="nome" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br><br>

        <label for="cpf">CPF:</label><br>
        <input type="text" name="cpf" id="cpf" required><br><br>

        <label for="data_nascimento">Data de Nascimento:</label><br>
        <input type="date" name="data_nascimento" id="data_nascimento" required><br><br>

        <label for="genero">Gênero:</label><br>
        <select name="genero" id="genero" required>
            <option value="masculino">Masculino</option>
            <option value="feminino">Feminino</option>
            <option value="outro">Outro</option>
        </select><br><br>

        <label for="senha">Senha:</label><br>
        <input type="password" name="senha" id="senha" required><br><br>

        <!-- Endereço de Faturamento -->
        <h3>Endereço de Faturamento:</h3>
        <label for="cep_faturamento">CEP:</label><br>
        <input type="text" name="cep_faturamento" id="cep_faturamento" required><br><br>
        
        <label for="logradouro_faturamento">Logradouro:</label><br>
        <input type="text" name="logradouro_faturamento" id="logradouro_faturamento" required><br><br>

        <label for="bairro_faturamento">Bairro:</label><br>
        <input type="text" name="bairro_faturamento" id="bairro_faturamento" required><br><br>

        <label for="cidade_faturamento">Cidade:</label><br>
        <input type="text" name="cidade_faturamento" id="cidade_faturamento" required><br><br>

        <label for="uf_faturamento">Estado (UF):</label><br>
        <input type="text" name="uf_faturamento" id="uf_faturamento" required><br><br>

        <label for="numero_faturamento">Número:</label><br>
        <input type="text" name="numero_faturamento" id="numero_faturamento" required><br><br>
        <!-- Endereço de Entrega -->
        <label>
            <input type="checkbox" name="usar_endereco_entrega" value="1">
            Usar o mesmo endereço de faturamento para entrega?
        </label><br><br>

        <div id="endereco_entrega" style="display: none;">
            <h3>Endereço de Entrega:</h3>
            <label for="cep_entrega">CEP:</label><br>
            <input type="text" name="cep_entrega" id="cep_entrega"><br><br>
            
            <label for="logradouro_entrega">Logradouro:</label><br>
            <input type="text" name="logradouro_entrega" id="logradouro_entrega"><br><br>

            <label for="bairro_entrega">Bairro:</label><br>
            <input type="text" name="bairro_entrega" id="bairro_entrega"><br><br>

            <label for="cidade_entrega">Cidade:</label><br>
            <input type="text" name="cidade_entrega" id="cidade_entrega"><br><br>

            <label for="uf_entrega">Estado (UF):</label><br>
            <input type="text" name="uf_entrega" id="uf_entrega"><br><br>

            <label for="numero_entrega">Número:</label><br>
            <input type="text" name="numero_entrega" id="numero_entrega"><br><br>
        </div>

        <input type="submit" value="Cadastrar">
    </form>
</div>

<script>
// Função para preencher os dados de endereço automaticamente ao digitar o CEP
document.getElementById('cep_faturamento').addEventListener('blur', function() {
    var cep = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos

    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('logradouro_faturamento').value = data.logradouro;
                    document.getElementById('bairro_faturamento').value = data.bairro;
                    document.getElementById('cidade_faturamento').value = data.localidade;
                    document.getElementById('uf_faturamento').value = data.uf;
                } else {
                    alert("CEP não encontrado.");
                }
            })
            .catch(error => alert("Erro ao buscar o CEP."));
    }
});

// Função para preencher os dados de entrega
document.getElementById('cep_entrega').addEventListener('blur', function() {
    var cep = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos

    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('logradouro_entrega').value = data.logradouro;
                    document.getElementById('bairro_entrega').value = data.bairro;
                    document.getElementById('cidade_entrega').value = data.localidade;
                    document.getElementById('uf_entrega').value = data.uf;
                } else {
                    alert("CEP não encontrado.");
                }
            })
            .catch(error => alert("Erro ao buscar o CEP."));
    }
});

// Exibe os campos de endereço de entrega quando a checkbox for desmarcada
document.querySelector('[name="usar_endereco_entrega"]').addEventListener('change', function() {
    var enderecoEntrega = document.getElementById("endereco_entrega");
    if (this.checked) {
        enderecoEntrega.style.display = 'none';
    } else {
        enderecoEntrega.style.display = 'block';
    }
});
</script>

</body>
</html>
