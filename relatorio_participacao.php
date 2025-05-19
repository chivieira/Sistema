<?php
// Ativa erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexão
$pdo = new PDO('mysql:host=localhost;dbname=south_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Consulta: total de vendas por vendedor
$sql = "SELECT f.nome, SUM(v.total_vendas) AS total_vendas
        FROM valor v
        JOIN funcionario f ON v.id_funcionario = f.id_funcionario
        GROUP BY f.nome";

$vendedores = $pdo->query($sql)->fetchAll();

// Calcular total geral
$total_geral = array_sum(array_column($vendedores, 'total_vendas'));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Participação nas Vendas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Relatório de Participação nas Vendas</h2>
        <p>Total Geral de Vendas: <strong>R$ <?= number_format($total_geral, 2, ',', '.') ?></strong></p>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Vendedor</th>
                    <th>Total de Vendas (R$)</th>
                    <th>Participação (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendedores as $v): 
                    $porcentagem = $total_geral > 0 ? ($v['total_vendas'] / $total_geral) * 100 : 0;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($v['nome']) ?></td>
                        <td>R$ <?= number_format($v['total_vendas'], 2, ',', '.') ?></td>
                        <td><?= number_format($porcentagem, 2, ',', '.') ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="index.php" class="btn btn-secondary mt-3">Voltar</a>
    </div>
</body>
</html>
