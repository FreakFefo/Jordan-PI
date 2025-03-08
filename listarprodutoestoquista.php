<?php
// Conexão com o banco de dados
include "connectDB.php";

// Definição de paginação
$itens_por_pagina = 10;
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$offset = ($pagina - 1) * $itens_por_pagina;

// Definição do filtro de busca
$filtro = isset($_GET['filtro']) ? trim($_GET['filtro']) : '';

// Query base para buscar produtos (ORDENADO DECRESCENTE)
$sql = "SELECT id, nome, quantidade_estoque, preco, status FROM produtos";
$parametros = [];
$tipos = "";

// Aplicar filtro, se houver
if ($filtro !== '') {
    $sql .= " WHERE nome LIKE ?";
    $parametros[] = "%$filtro%";
    $tipos .= "s";
}

// Ordenação por ID (mais recentes primeiro) e paginação
$sql .= " ORDER BY id DESC LIMIT ?, ?";
$parametros[] = $offset;
$parametros[] = $itens_por_pagina;
$tipos .= "ii";

// Preparar e executar a consulta
$stmt = $mysqli->prepare($sql);
$stmt->bind_param($tipos, ...$parametros);
$stmt->execute();
$result = $stmt->get_result();

// Contar total de produtos para paginação
$sql_total = "SELECT COUNT(*) AS total FROM produtos";
if ($filtro !== '') {
    $sql_total .= " WHERE nome LIKE ?";
}
$stmt_total = $mysqli->prepare($sql_total);
if ($filtro !== '') {
    $stmt_total->bind_param("s", $parametros[0]);
}
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_registros = $result_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $itens_por_pagina);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backoffice Estoquista - Produtos</title>

    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .buttons-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .pagination {
            margin-top: 10px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
        .status-ativo {
            color: green;
        }
        .status-inativo {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Gestão de Produtos - Estoquista</h2>

    <form method="GET" action="">
        <input type="text" name="filtro" placeholder="Buscar produto..." value="<?php echo htmlspecialchars($filtro); ?>">
        <button type="submit">Buscar Produto</button>
    </form>

    <div class="buttons-container">
        <button onclick="window.location.href='backofficeestoquista.php'">Voltar</button>
    </div>

    <table>
        <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Quantidade em Estoque</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['nome']); ?></td>
            <td><?php echo $row['quantidade_estoque']; ?></td>
            <td>R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></td>
            <td>
                <span class="<?php echo ($row['status'] == 1) ? 'status-ativo' : 'status-inativo'; ?>">
                    <?php echo ($row['status'] == 1) ? 'Ativo' : 'Inativo'; ?>
                </span>
            </td>
            <td>
                <a href="alterarProdutoEstoquista.php?id=<?php echo $row['id']; ?>">Alterar</a>
            </td>
        </tr>
        <?php } ?>
    </table>

    <div class="pagination">
        <?php if ($total_paginas > 1) { ?>
            <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                <a href="?pagina=<?php echo $i; ?>&filtro=<?php echo urlencode($filtro); ?>">
                    <?php echo $i; ?>
                </a>
            <?php } ?>
        <?php } ?>
    </div>
</body>
</html>
