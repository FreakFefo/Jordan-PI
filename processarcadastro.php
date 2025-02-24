<?php
// Incluir a conexão com o banco de dados
include "connectDB.php";

// Recebe os dados do formulário
$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$data_nac = $_POST['data_nac'];
$telefone = $_POST['telefone'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$senha_confirm = $_POST['senha_confirm'];
$tipo = $_POST['tipo'];
$genero = $_POST['genero'];

// Verificar se as senhas coincidem
if ($senha !== $senha_confirm) {
    echo "As senhas não coincidem. Tente novamente.";
    exit;
}

// Validar o CPF
if (!validarCPF($cpf)) {
    echo "CPF inválido. Tente novamente.";
    exit;
}

// Verificar se o email já está cadastrado
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->execute([':email' => $email]);
if ($stmt->rowCount() > 0) {
    echo "Este email já está cadastrado.";
    exit;
}

// Encriptar a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Inserir o usuário no banco de dados (status sempre ativo)
$sql = "INSERT INTO usuarios (nome, cpf, data_nac, telefone, email, senha, tipo, ativo, genero) 
        VALUES (:nome, :cpf, :data_nac, :telefone, :email, :senha, :tipo, 1, :genero)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':nome' => $nome,
    ':cpf' => $cpf,
    ':data_nac' => $data_nac,
    ':telefone' => $telefone,
    ':email' => $email,
    ':senha' => $senha_hash,
    ':tipo' => $tipo,
    ':genero' => $genero
]);

echo "Usuário cadastrado com sucesso!";
?>
