<?php
session_start();
require 'includes/db.php';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';
    $tipo = $_POST['tipo'] ?? '';
    if ($senha !== $senha2) {
        $erro = 'As senhas não coincidem.';
    } elseif ($tipo !== 'cliente' && $tipo !== 'prestador') {
        $erro = 'Selecione o tipo de conta.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erro = 'Já existe uma conta com esse email.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO usuarios (nome,email,senha,tipo,credito) VALUES (?,?,?,?,0)');
            $stmt->execute([$nome,$email,$senha,$tipo]);
            $id = $pdo->lastInsertId();
            $stmt = $pdo->prepare('SELECT id,nome,email,senha,tipo,credito FROM usuarios WHERE id = ?');
            $stmt->execute([$id]);
            $_SESSION['user'] = $stmt->fetch();
            if ($tipo === 'cliente') {
                header('Location: solicitar_servico.php');
            } else {
                header('Location: painel_prestador.php');
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Serviço Fácil — Cadastro</title>
    <link rel="stylesheet" href="styles/styles.css" />
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>Criar uma conta</h2>
            <?php if ($erro): ?>
                <div class="erro"><?php echo htmlspecialchars($erro); ?></div>
            <?php endif; ?>
            <form method="post" action="cadastro.php">
                <div class="form-row">
                    <label for="registerName">Nome completo</label>
                    <input id="registerName" name="nome" type="text" required />
                </div>
                <div class="form-row">
                    <label for="registerEmail">Email</label>
                    <input id="registerEmail" name="email" type="email" required />
                </div>
                <div class="form-row">
                    <label for="registerPassword">Senha</label>
                    <input id="registerPassword" name="senha" type="password" required />
                </div>
                <div class="form-row">
                    <label for="registerConfirmPassword">Confirmar senha</label>
                    <input id="registerConfirmPassword" name="senha2" type="password" required />
                </div>
                <div class="form-row">
                    <label>Tipo de conta</label>
                    <select id="registerRole" name="tipo" required>
                        <option value="">Selecione...</option>
                        <option value="cliente">Cliente</option>
                        <option value="prestador">Prestador de Serviço</option>
                    </select>
                </div>
                <button class="btn" type="submit">Cadastrar</button>
            </form>
            <div class="auth-footer">Já tem conta? <a href="login.php">Entre aqui</a></div>
        </div>
    </div>
</div>
</body>
</html>
