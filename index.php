<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Serviço Fácil</title>
    <link rel="stylesheet" href="styles/styles.css" />
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
    <section class="hero">
        <h1>Facilitando o acesso a serviços perto de você</h1>
        <p>
            Conectamos clientes e prestadores de forma rápida, prática e confiável. Solicite ou
            ofereça serviços em poucos cliques.
        </p>
        <div class="actions">
            <a href="solicitar_servico.php" class="btn">Solicitar Serviço</a>
            <a href="cadastro.php" class="btn ghost btn white">Quero Me Cadastrar</a>
        </div>
    </section>
</div>
</body>
</html>
