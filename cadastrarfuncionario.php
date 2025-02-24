<?php
// Incluir a conexão com o banco de dados
include "connectDB.php";

// Função para verificar se o CPF é válido
function validarCPF($cpf) {
    // Remove caracteres especiais do CPF
    $cpfOriginal = $cpf; // Armazena o CPF original para debug
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    // Exibe para depuração
    // echo "CPF Original: $cpfOriginal<br>";
    // echo "CPF sem caracteres especiais: $cpf<br>";

    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se o CPF é uma sequência de números iguais (ex: 111.111.111-11)
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Calcula o primeiro dígito verificador
    $soma = 0;
    for ($i = 0; $i < 9; $i++) {
        $soma += (int) $cpf[$i] * (10 - $i);
    }
    $resto = $soma % 11;
    $digito1 = $resto < 2 ? 0 : 11 - $resto;

    // Calcula o segundo dígito verificador
    $soma = 0;
    for ($i = 0; $i < 10; $i++) {
        $soma += (int) $cpf[$i] * (11 - $i);
    }
    $resto = $soma % 11;
    $digito2 = $resto < 2 ? 0 : 11 - $resto;

    // Verifica se os dígitos verificadores estão corretos
    if ($cpf[9] != $digito1 || $cpf[10] != $digito2) {
        return false;
    }

    // CPF válido
    return true;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $data_nac = $_POST['data_nac'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $senha_confirm = $_POST['senha_confirm'];
    $tipo = $_POST['tipo'];
    $genero = $_POST['genero'];

    // Validar CPF
    if (!validarCPF($cpf)) {
        echo "CPF inválido.";
        exit;
    }

    // Verificar se as senhas são iguais
    if ($senha !== $senha_confirm) {
        echo "As senhas não coincidem.";
        exit;
    }

    // Encriptar a senha
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    // Verificar se o email já existe
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "Este email já está cadastrado.";
        exit;
    }

    // Inserir o novo usuário no banco de dados
    $sql = "INSERT INTO usuarios (nome, cpf, data_nac, telefone, email, senha, tipo, genero, ativo) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)"; // 1 significa "ativo"
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssssss", $nome, $cpf, $data_nac, $telefone, $email, $senhaHash, $tipo, $genero);


    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar usuário.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <script>
        // Máscara para CPF
        function mascaraCPF(input) {
            let value = input.value;
            value = value.replace(/\D/g, "");
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
            input.value = value;
        }

        // Máscara para Telefone
        function mascaraTelefone(input) {
            let value = input.value;
            value = value.replace(/\D/g, "");
            value = value.replace(/^(\d{2})(\d)/g, "($1) $2");
            value = value.replace(/(\d{5})(\d)/, "$1-$2");
            input.value = value;
        }
    </script>
</head>
<body>
    <h2>Cadastrar Usuário</h2>
    <form action="" method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required><br>
        
        <label>CPF:</label>
        <input type="text" name="cpf" required oninput="mascaraCPF(this)"><br>
        
        <label>Data de Nascimento:</label>
        <input type="date" name="data_nac" required><br>
        
        <label>Telefone:</label>
        <input type="text" name="telefone" required oninput="mascaraTelefone(this)"><br>
        
        <label>Email:</label>
        <input type="email" name="email" required><br>
        
        <label>Senha:</label>
        <input type="password" name="senha" required><br>
        
        <label>Confirme a Senha:</label>
        <input type="password" name="senha_confirm" required><br>
        
        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="admin">Administrador</option>
            <option value="estoquista">Estoquista</option>
        </select><br>
        
        <label>Gênero:</label>
        <select name="genero" required>
            <option value="Homem">Homem</option>
            <option value="mulher">Mulher</option>
        </select><br>
        
        <button type="submit">Cadastrar</button>
    </form>
    <li><a href="backofficeadm.php">voltar</a></li>
</body>
</html>
