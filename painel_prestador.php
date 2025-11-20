<?php
session_start();
require 'includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'prestador') {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['acao'] ?? '') === 'concluir') {
    $numero = $_POST['numero'] ?? '';
    $stmt = $pdo->prepare('UPDATE solicitacoes SET status = "concluido", data_conclusao = NOW() WHERE numero = ? AND prestador_id = ? AND status = "execucao"');
    $stmt->execute([$numero,$user['id']]);
}
$stmt = $pdo->prepare('SELECT s.*, c.email AS cliente_email FROM solicitacoes s JOIN usuarios c ON s.cliente_id = c.id WHERE s.prestador_id = ? ORDER BY s.data_criacao DESC');
$stmt->execute([$user['id']]);
$servicos = $stmt->fetchAll();
function filtrarPorStatus($servicos,$status) {
    $lista = [];
    foreach ($servicos as $s) {
        if ($s['status'] === $status) {
            $lista[] = $s;
        }
    }
    return $lista;
}
$novos = filtrarPorStatus($servicos,'novo');
$execucao = filtrarPorStatus($servicos,'execucao');
$pendentes = filtrarPorStatus($servicos,'pendente');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Serviço Fácil — Painel do Prestador</title>
    <link rel="stylesheet" href="styles/styles.css" />
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
    <main class="card">
        <div class="page-title">Painel do Prestador</div>

        <div class="section">
            <h3>Novos Serviços Solicitados</h3>
            <?php if (count($novos) === 0): ?>
                <p>Nenhum serviço novo.</p>
            <?php else: ?>
                <table>
                    <thead>
                    <tr>
                        <th>Número</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($novos as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['numero']); ?></td>
                            <td><?php echo date('d/m/Y',strtotime($s['data_criacao'])); ?></td>
                            <td><?php echo htmlspecialchars($s['cliente_email']); ?></td>
                            <td><?php echo htmlspecialchars($s['tipo_servico'] . ' - ' . $s['servico']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="section">
            <h3>Serviço em Execução</h3>
            <?php if (count($execucao) === 0): ?>
                <p>Não há serviço em execução</p>
            <?php else: ?>
                <table>
                    <thead>
                    <tr>
                        <th>Número</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($execucao as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['numero']); ?></td>
                            <td><?php echo date('d/m/Y',strtotime($s['data_criacao'])); ?></td>
                            <td><?php echo htmlspecialchars($s['cliente_email']); ?></td>
                            <td><?php echo htmlspecialchars($s['tipo_servico'] . ' - ' . $s['servico']); ?></td>
                            <td>
                                <form method="post" action="painel_prestador.php">
                                    <input type="hidden" name="acao" value="concluir" />
                                    <input type="hidden" name="numero" value="<?php echo htmlspecialchars($s['numero']); ?>" />
                                    <button class="btn" type="submit">Concluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="section">
            <h3>Serviços Pendentes</h3>
            <?php if (count($pendentes) === 0): ?>
                <p>Nenhum serviço pendente.</p>
            <?php else: ?>
                <table>
                    <thead>
                    <tr>
                        <th>Número</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Serviço</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pendentes as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['numero']); ?></td>
                            <td><?php echo date('d/m/Y',strtotime($s['data_criacao'])); ?></td>
                            <td><?php echo htmlspecialchars($s['cliente_email']); ?></td>
                            <td><?php echo htmlspecialchars($s['tipo_servico'] . ' - ' . $s['servico']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
