<?php
// Ativa exibição de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexão
$pdo = new PDO('mysql:host=localhost;dbname=south_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ação de salvar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO Funcionario (nome, matricula, ativo) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $matricula, $ativo]);

    header('Location: cadastro_vendedores.php');
    exit;
}

// Listar vendedores cadastrados
$vendedores = $pdo->query("SELECT * FROM Funcionario ORDER BY id_funcionario DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Vendedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2>Cadastro de Vendedores</h2>

        <!-- Formulário -->
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label>Nome do Vendedor:</label>
                <input type="text" name="nome" required class="form-control">
            </div>
            <div class="mb-3">
                <label>Matrícula:</label>
                <input type="text" name="matricula" required class="form-control">
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="ativo" class="form-check-input" checked>
                <label class="form-check-label">Ativo</label>
            </div>
            <button type="submit" class="btn btn-success">Cadastrar</button>
        </form>

        <!-- Lista de vendedores -->
        <h4>Vendedores Cadastrados</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendedores as $v): ?>
                    <tr>
                        <td><?= $v['id_funcionario'] ?></td>
                        <td><?= htmlspecialchars($v['nome']) ?></td>
                        <td><?= htmlspecialchars($v['matricula']) ?></td>
                        <td><?= $v['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="index.php" class="btn btn-secondary mt-3">Voltar</a>
    </div>
</body>
</html>
