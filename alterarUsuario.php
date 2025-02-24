<?php
// Conexão com o banco de dados
include "connectDB.php";

// Iniciar a sessão para pegar o ID do usuário logado
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo "Você não está logado.";
    exit;
}

// Obter o ID do usuário logado
$user_id = $_SESSION['user_id'];

// Verificar se o ID do usuário a ser alterado foi passado
if (!isset($_GET['id'])) {
    echo "ID do usuário não fornecido.";
    exit;
}

$id_usuario = $_GET['id'];

// Verificar se o usuário tem permissão para alterar o grupo de outro usuário
// Se o usuário logado for o mesmo que o usuário a ser alterado, permitir alteração do grupo
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    // Verificando se o usuário logado é o mesmo que está sendo alterado
    if ($usuario['id'] != $id_usuario) {
        // Aqui você pode definir regras se o grupo pode ser alterado
        // Supondo que você queira permitir alterar o grupo apenas se o usuário logado for admin
        if ($_SESSION['tipo'] != 'admin') {
            echo "Você não tem permissão para alterar o grupo.";
            exit;
        }
    }

    // Preencher os campos do formulário com os dados atuais do usuário
    $nome = $usuario['nome'];
    $cpf = $usuario['cpf'];
    $tipo = $usuario['tipo'];
} else {
    echo "Usuário não encontrado.";
    exit;
}

// Processar o formulário de atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter os dados do formulário
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];
    $senha_confirm = $_POST['senha_confirm'];
    $tipo = $_POST['tipo'];

    // Validar as senhas
    if ($senha !== $senha_confirm) {
        echo "As senhas não coincidem.";
        exit;
    }

    // Se a senha foi alterada, encriptá-la
    if (!empty($senha)) {
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
    }

    // Atualizar os dados no banco de dados (exceto o email)
    $sql = "UPDATE usuarios SET nome = ?, cpf = ?, senha = ?, tipo = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    // Se a senha foi atualizada, passá-la para o SQL
    if (!empty($senha)) {
        $stmt->bind_param("ssssi", $nome, $cpf, $senhaHash, $tipo, $id_usuario);
    } else {
        // Caso a senha não tenha sido alterada, só atualizar os outros campos
        $stmt->bind_param("sssii", $nome, $cpf, $tipo, $id_usuario);
    }

    // Executar a atualização
    if ($stmt->execute()) {
        echo "Usuário alterado com sucesso!";
        // Redirecionar ou fazer outra ação após a atualização
    } else {
        echo "Erro ao alterar os dados do usuário.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Usuário</title>
</head>
<body>
    <h2>Alterar Usuário</h2>
    <form method="POST" action="">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" required><br>

        <label for="cpf">CPF:</label>
        <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($cpf); ?>" required><br>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" placeholder="Nova Senha (se desejar alterar)"><br>

        <label for="senha_confirm">Confirmar Senha:</label>
        <input type="password" id="senha_confirm" name="senha_confirm" placeholder="Confirmar Nova Senha"><br>

        <!-- Se o usuário for admin, permitir alteração do tipo -->
        <?php if ($_SESSION['tipo'] == 'admin') { ?>
            <label for="tipo">Grupo:</label>
            <select id="tipo" name="tipo" required>
                <option value="admin" <?php echo ($tipo == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                <option value="estoquista" <?php echo ($tipo == 'estoquista') ? 'selected' : ''; ?>>Estoquista</option>
            </select><br>
        <?php } ?>

        <button type="submit">Alterar</button>
    </form>

    <a href="listarUsuarios.php">Voltar</a>
</body>
</html>
