<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<header class="header">
    <a class="brand" href="../index.php">
        <div class="logo">SF</div>
        <span>Serviço Fácil</span>
    </a>
    <nav class="nav">
        <?php if (!$user): ?>
            <a class="btn ghost" href="cadastro.php">Cadastro</a>
            <a class="btn ghost" href="login.php">Login</a>
        <?php else: ?>
            <?php if ($user['tipo'] === 'cliente'): ?>
                <a class="btn" href="solicitar_servico.php">Solicitar Serviço</a>
                <a class="btn ghost" href="lista_solicitacoes.php">Minhas Solicitações</a>
            <?php elseif ($user['tipo'] === 'prestador'): ?>
                <a class="btn" href="painel_prestador.php">Painel Prestador</a>
            <?php endif; ?>
            <span style="font-weight:600; color:var(--purple-700); margin-left:10px;">
        Olá, <?php echo htmlspecialchars($user['nome']); ?>
      </span>
            <a class="btn ghost" href="./includes/logout.php">Sair</a>
        <?php endif; ?>
    </nav>
</header>
