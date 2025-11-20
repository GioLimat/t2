<?php
session_start();
require 'includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'cliente') {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$stmt = $pdo->prepare('SELECT id,nome,email,senha,tipo,credito FROM usuarios WHERE id = ?');
$stmt->execute([$user['id']]);
$userDb = $stmt->fetch();
if ($userDb) {
    $_SESSION['user'] = $userDb;
    $user = $userDb;
}
$stmt = $pdo->prepare('SELECT s.*, p.email AS prestador_email FROM solicitacoes s JOIN usuarios p ON s.prestador_id = p.id WHERE s.cliente_id = ? ORDER BY s.data_criacao DESC');
$stmt->execute([$user['id']]);
$solicitacoes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Serviço Fácil — Lista de Solicitações</title>
    <link rel="stylesheet" href="styles/styles.css" />
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
    <main class="card">
        <div class="page-title">Minhas Solicitações</div>
        <div class="lista-header">
            <div class="lista-header-info">
                Cliente: <?php echo htmlspecialchars($user['email']); ?>
            </div>
            <div class="lista-header-credit">
                <span>Crédito: R$ <?php echo number_format((float)$user['credito'],2,',','.'); ?></span>
                <a href="solicitar_servico.php" class="btn">Criar nova solicitação</a>
            </div>
        </div>
        <div id="lista-container">
            <?php if (count($solicitacoes) === 0): ?>
                <div class="no-solicitacoes">Você não tem solicitações no momento</div>
            <?php else: ?>
                <table>
                    <thead>
                    <tr>
                        <th>Número</th>
                        <th>Data</th>
                        <th>Serviço</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($solicitacoes as $s): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($s['numero']); ?></td>
                            <td><?php echo date('d/m/Y',strtotime($s['data_criacao'])); ?></td>
                            <td><?php echo htmlspecialchars($s['tipo_servico'] . ' - ' . $s['servico']); ?></td>
                            <td>
                                <?php
                                $status = $s['status'];
                                $classe = '';
                                $texto = '';
                                if ($status === 'novo') {
                                    $classe = 'status-aguardando';
                                    $texto = 'Novo';
                                } elseif ($status === 'execucao') {
                                    $classe = 'status-execucao';
                                    $texto = 'Em execução';
                                } elseif ($status === 'pendente') {
                                    $classe = 'status-pendente';
                                    $texto = 'Pendente';
                                } elseif ($status === 'concluido') {
                                    $classe = 'status-concluido';
                                    $texto = 'Concluído';
                                } elseif ($status === 'cancelado') {
                                    $classe = 'status-cancelado';
                                    $texto = 'Cancelado';
                                } else {
                                    $texto = $status;
                                }
                                ?>
                                <span class="status-badge <?php echo $classe; ?>"><?php echo $texto; ?></span>
                                <?php if ($status === 'concluido' && !$s['avaliado']): ?>
                                    <a href="avaliar_prestador.php?num=<?php echo urlencode($s['numero']); ?>" class="status-link">Avaliar</a>
                                <?php endif; ?>
                            </td>
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
