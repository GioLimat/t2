<?php
session_start();
require 'includes/db.php';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $stmt = $pdo->prepare('SELECT id,nome,email,senha,tipo,credito FROM usuarios WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user || $user['senha'] !== $senha) {
        $erro = 'Email ou senha inválidos.';
    } else {
        $_SESSION['user'] = $user;
        if ($user['tipo'] === 'prestador') {
            header('Location: painel_prestador.php');
        } else {
            header('Location: lista_solicitacoes.php');
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Serviço Fácil — Login</title>
    <link rel="stylesheet" href="styles/styles.css" />
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
    <div class="login-wrapper">
        <div class="login-card">
            <h2>Entrar na sua conta</h2>
            <?php if ($erro): ?>
                <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="form-row">
                    <label for="loginEmail">Email</label>
                    <input id="loginEmail" name="email" type="email" required />
                </div>
                <div class="form-row">
                    <label for="loginPassword">Senha</label>
                    <input id="loginPassword" name="senha" type="password" required />
                </div>
                <button class="btn" type="submit">Entrar</button>
            </form>
            <div class="login-footer">
                Não tem conta? <a href="cadastro.php">Cadastre-se</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
