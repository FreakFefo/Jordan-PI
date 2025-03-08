<?php
// Incluir a conexão com o banco de dados
include "connectDB.php";

// Função para verificar se o CPF é válido
function validarCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/\D/', '', $cpf);

    // Verifica se tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se todos os dígitos são iguais (ex: 111.111.111-11)
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Calcula os dígitos verificadores
    for ($j = 9; $j < 11; $j++) {
        $soma = 0;
        for ($i = 0; $i < $j; $i++) {
            $soma += (int) $cpf[$i] * (($j + 1) - $i);
        }
        $resto = $soma % 11;
        if ($cpf[$j] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
    }
    
    return true;
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $cpf = trim($_POST['cpf']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $senha_confirm = $_POST['senha_confirm'];
    $grupo = $_POST['tipo']; // Deve ser ADM, EST ou CLI

    // Validar CPF
    if (!validarCPF($cpf)) {
        die("Erro: CPF inválido.");
    }

    // Verificar se as senhas são iguais
    if ($senha !== $senha_confirm) {
        die("Erro: As senhas não coincidem.");
    }

    // Encriptar a senha
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    // Verificar se o email já existe
    $sql = "SELECT id FROM usuario WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        die("Erro: Este email já está cadastrado.");
    }

    // Verificar se o CPF já existe
    $sql = "SELECT id FROM usuario WHERE cpf = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        die("Erro: Este CPF já está cadastrado.");
    }

    // Inserir o novo usuário no banco de dados
    $sql = "INSERT INTO usuario (nome, cpf, email, senha, grupo, ativo) 
            VALUES (?, ?, ?, ?, ?, 1)"; // 1 significa "ativo"
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssss", $nome, $cpf, $email, $senhaHash, $grupo);

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
            let value = input.value.replace(/\D/g, ""); // Remove tudo que não for número
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
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
        
        <label>Email:</label>
        <input type="email" name="email" required><br>
        
        <label>Senha:</label>
        <input type="password" name="senha" required><br>
        
        <label>Confirme a Senha:</label>
        <input type="password" name="senha_confirm" required><br>
        
        <label>Grupo:</label>
        <select name="tipo" required>
            <option value="ADM">Administrador</option>
            <option value="EST">Estoquista</option>
        </select><br>
        
        <button type="submit">Cadastrar</button>
    </form>
    <li><a href="backofficeadm.php">Voltar</a></li>
</body>
</html>
