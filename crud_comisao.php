<?php
// Ativa exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexão com o banco south_db
$pdo = new PDO('mysql:host=localhost;dbname=south_db', 'root', '');

// Processar ações
$acao = $_GET['acao'] ?? '';
$id = $_GET['id'] ?? '';

// Buscar dados de vendedores para seleção
$vendedores = $pdo->query("SELECT id_funcionario, nome FROM funcionario WHERE ativo = 1")->fetchAll();

if ($acao === 'salvar') {
    $id_funcionario = $_POST['id_funcionario'];
    $total_vendas = $_POST['total_vendas'];
    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $percentual = $_POST['percentual_comissao'];
    $id_editar = $_POST['id'] ?? '';

    if ($id_editar) {
        $stmt = $pdo->prepare("UPDATE valor SET id_funcionario=?, total_vendas=?, mes=?, ano=?, percentual_comissao=? WHERE id_valor=?");
        $stmt->execute([$id_funcionario, $total_vendas, $mes, $ano, $percentual, $id_editar]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO valor (id_funcionario, total_vendas, mes, ano, percentual_comissao) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_funcionario, $total_vendas, $mes, $ano, $percentual]);
    }

    header('Location: crud_comissao.php');
    exit;
}

if ($acao === 'editar' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM valor WHERE id_valor = ?");
    $stmt->execute([$id]);
    $registro = $stmt->fetch();
}

if ($acao === 'excluir' && $id) {
    $stmt = $pdo->prepare("DELETE FROM valor WHERE id_valor = ?");
    $stmt->execute([$id]);
    header('Location: crud_comissao.php');
    exit;
}

// Buscar vendas + nome do vendedor
$sql = "SELECT v.*, f.nome 
        FROM valor v 
        INNER JOIN funcionario f ON v.id_funcionario = f.id_funcionario";
$vendas = $pdo->query($sql)->fetchAll();
?>

<head>
    <meta charset="UTF-8">
    <title>Comissões</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">
    <h2>Cadastro de Vendas</h2>
    <form method="POST" action="?acao=salvar" class="mb-4">
        <input type="hidden" name="id" value="<?= $registro['id_valor'] ?? '' ?>">
        <div class="mb-2">
            <label>Vendedor:</label>
            <select name="id_funcionario" required>
                <option value="">Selecione</option>
                <?php foreach ($vendedores as $v): ?>
                    <option value="<?= $v['id_funcionario'] ?>" <?= ($registro['id_funcionario'] ?? '') == $v['id_funcionario'] ? 'selected' : '' ?>>
                        <?= $v['nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-2">
            <label>Mês:</label>
            <input type="number" name="mes" min="1" max="12" value="<?= $registro['mes'] ?? '' ?>" required>
        </div>
        <div class="mb-2">
            <label>Ano:</label>
            <input type="number" name="ano" value="<?= $registro['ano'] ?? '' ?>" required>
        </div>
        <div class="mb-2">
            <label>Valor da Venda:</label>
            <input type="number" step="0.01" name="total_vendas" value="<?= $registro['total_vendas'] ?? '' ?>" required>
        </div>
        <div class="mb-2">
            <label>% Comissão:</label>
            <input type="number" step="0.01" name="percentual_comissao" value="<?= $registro['percentual_comissao'] ?? '' ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
    </form>

    <h3>Lista de Comissões</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Vendedor</th>
                <th>Mês</th>
                <th>Ano</th>
                <th>Venda (R$)</th>
                <th>% Comissão</th>
                <th>Comissão (R$)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendas as $v): ?>
            <tr>
                <td><?= $v['nome'] ?></td>
                <td><?= $v['mes'] ?></td>
                <td><?= $v['ano'] ?></td>
                <td>R$ <?= number_format($v['total_vendas'], 2, ',', '.') ?></td>
                <td><?= $v['percentual_comissao'] ?>%</td>
                <td>R$ <?= number_format($v['total_vendas'] * $v['percentual_comissao'] / 100, 2, ',', '.') ?></td>
                <td>
                    <a href="?acao=editar&id=<?= $v['id_valor'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="?acao=excluir&id=<?= $v['id_valor'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
