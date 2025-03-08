<?php
// Conexão com o banco de dados
include "connectDB.php";

// Iniciar a sessão para pegar o ID do usuário logado
session_start();

// Definição do filtro
$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : '';

// Montando a query com filtro opcional
$sql = "SELECT id, nome, email, grupo, ativo FROM usuario";
if ($filtro !== '') {
    $sql .= " WHERE nome LIKE ?";
}

$stmt = $mysqli->prepare($sql);

if ($filtro !== '') {
    $param = "%$filtro%";
    $stmt->bind_param("s", $param);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Usuários</title>

    <script>
        // Função de confirmação antes de enviar a solicitação de ativação/desativação
        function confirmarAlteracaoStatus(id) {
            const confirmacao = confirm("Você realmente deseja alterar o status deste usuário?");
            if (confirmacao) {
                // Se o usuário confirmar, redireciona para o arquivo de toggle
                window.location.href = 'toggle_status.php?id=' + id;
            }
        }
    </script>
</head>
<body>
    <h2>Lista de Usuários</h2>

    <form method="GET" action="">
        <input type="text" name="filtro" placeholder="Filtrar por nome" value="<?php echo htmlspecialchars($filtro); ?>">
        <button type="submit">Filtrar</button>
    </form>

    <button onclick="window.location.href='cadastrarFuncionario.php'">+</button>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Grupo</th> <!-- Adicionando a coluna do grupo -->
            <th>Status</th>
            <th>Alterar</th>
            <th>Hab/Des</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['nome']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['grupo']); ?></td> <!-- Exibição do grupo -->
            <td><?php echo ($row['ativo'] == 1) ? 'Ativo' : 'Inativo'; ?></td>
            <td><a href="alterarUsuario.php?id=<?php echo $row['id']; ?>">Alterar</a></td>
            <td>
                <!-- Chamando a função de confirmação -->
                <button type="button" onclick="confirmarAlteracaoStatus(<?php echo $row['id']; ?>)">
                    <?php echo ($row['ativo'] == 1) ? 'Desativar' : 'Ativar'; ?>
                </button>
            </td>
        </tr>
        <?php } ?>
    </table>

    <!-- Botão de Voltar -->
    <br><br>
    <button onclick="window.location.href='backofficeadm.php'">Voltar</button>
</body>
</html>
