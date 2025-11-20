<?php
session_start();
require 'includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'cliente') {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
$numero = $_GET['num'] ?? '';
$stmt = $pdo->prepare('SELECT s.*, p.email AS prestador_email FROM solicitacoes s JOIN usuarios p ON s.prestador_id = p.id WHERE s.numero = ? AND s.cliente_id = ?');
$stmt->execute([$numero,$user['id']]);
$sol = $stmt->fetch();
if (!$sol) {
    header('Location: lista_solicitacoes.php');
    exit;
}
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nota = (int)($_POST['nota'] ?? 0);
    $comentario = $_POST['comentario'] ?? '';
    if ($nota < 1 || $nota > 5) {
        $erro = 'Selecione uma nota.';
    } else {
        $jaAvaliado = (int)$sol['avaliado'] === 1;
        $stmt = $pdo->prepare('UPDATE solicitacoes SET avaliado = 1, nota_avaliacao = ?, comentario_avaliacao = ? WHERE id = ?');
        $stmt->execute([$nota,$comentario,$sol['id']]);
        if (!$jaAvaliado) {
            $stmt = $pdo->prepare('UPDATE usuarios SET credito = credito + 10 WHERE id = ?');
            $stmt->execute([$user['id']]);
        }
        $stmt = $pdo->prepare('SELECT id,nome,email,senha,tipo,credito FROM usuarios WHERE id = ?');
        $stmt->execute([$user['id']]);
        $_SESSION['user'] = $stmt->fetch();
        header('Location: lista_solicitacoes.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Serviço Fácil — Avaliar Prestador</title>
    <link rel="stylesheet" href="styles/styles.css" />
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
    <main class="avaliar-card">
        <h2>Avalie seu prestador</h2>
        <?php if ($erro): ?>
            <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
        <?php endif; ?>
        <div class="avaliar-info">
            <p><strong>Tipo de Serviço:</strong> <?php echo htmlspecialchars($sol['tipo_servico']); ?></p>
            <p><strong>Serviço:</strong> <?php echo htmlspecialchars($sol['servico']); ?></p>
            <p><strong>Data:</strong> <?php echo date('d/m/Y',strtotime($sol['data_criacao'])); ?></p>
            <p><strong>Data da Conclusão:</strong> <?php echo $sol['data_conclusao'] ? date('d/m/Y',strtotime($sol['data_conclusao'])) : ''; ?></p>
            <p><strong>Prestador:</strong> <?php echo htmlspecialchars($sol['prestador_email']); ?></p>
        </div>
        <form method="post" class="avaliar-form">
            <div class="rating">
                <label>Nota:</label>
                <div class="stars">
                    <input type="radio" id="star5" name="nota" value="5" required />
                    <label for="star5">★</label>
                    <input type="radio" id="star4" name="nota" value="4" />
                    <label for="star4">★</label>
                    <input type="radio" id="star3" name="nota" value="3" />
                    <label for="star3">★</label>
                    <input type="radio" id="star2" name="nota" value="2" />
                    <label for="star2">★</label>
                    <input type="radio" id="star1" name="nota" value="1" />
                    <label for="star1">★</label>
                </div>
            </div>
            <div class="form-row">
                <label for="comentario">Comentário:</label>
                <textarea id="comentario" name="comentario" placeholder="Escreva seu feedback (opcional)"></textarea>
            </div>
            <button type="submit" class="btn">Registrar Avaliação</button>
        </form>
    </main>
</div>
</body>
</html>
