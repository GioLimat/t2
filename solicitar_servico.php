<?php
session_start();
require 'includes/db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'cliente') {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];

$categorias = [
    'Serviço Elétrico' => ['Conserto de tomada','Interruptor não funciona','Troca de lâmpada'],
    'Bombeiro Hidráulico' => ['Vazamento em torneira','Vazamento em descarga','Vazamento no teto'],
    'Chaveiro' => ['Abrir porta por perda de chave','Troca de fechadura'],
    'Marceneiro' => ['Reparo em armário','Montagem de móveis']
];

$precos = [
    'Conserto de tomada' => 200.00,
    'Interruptor não funciona' => 120.00,
    'Troca de lâmpada' => 80.00,
    'Vazamento em torneira' => 150.00,
    'Vazamento em descarga' => 200.00,
    'Vazamento no teto' => 300.00,
    'Abrir porta por perda de chave' => 180.00,
    'Troca de fechadura' => 220.00,
    'Reparo em armário' => 200.00,
    'Montagem de móveis' => 250.00
];

$passo = 'form';
$tipoSelecionado = '';
$servicoSelecionado = '';
$descricao = '';
$preco = 0;
$credito = (float)$user['credito'];
$valorCobrado = 0;
$prestadorEmail = 'prestador1@teste.com.br';
$numeroGerado = '';
$prestadorEmailConfirmado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $tipoSelecionado = $_POST['tipo_servico'] ?? '';
    $servicoSelecionado = $_POST['servico'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    if ($acao === 'preco') {
        if ($tipoSelecionado && $servicoSelecionado) {
            $preco = $precos[$servicoSelecionado] ?? 100.00;
            $stmt = $pdo->prepare('SELECT credito FROM usuarios WHERE id = ?');
            $stmt->execute([$user['id']]);
            $rowCred = $stmt->fetch();
            $credito = $rowCred ? (float)$rowCred['credito'] : 0.0;
            $desconto = min($credito,$preco);
            $valorCobrado = $preco - $desconto;
            $passo = 'preco';
        }
    } elseif ($acao === 'confirmar') {
        $preco = (float)($_POST['preco'] ?? 0);
        $tipoSelecionado = $_POST['tipo_servico'] ?? '';
        $servicoSelecionado = $_POST['servico'] ?? '';
        $descricao = $_POST['descricao'] ?? '';
        $prestadorEmail = $_POST['prestador_email'] ?? $prestadorEmail;
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ? AND tipo = "prestador"');
        $stmt->execute([$prestadorEmail]);
        $prestador = $stmt->fetch();
        if ($prestador) {
            $stmt = $pdo->prepare('SELECT credito FROM usuarios WHERE id = ?');
            $stmt->execute([$user['id']]);
            $rowCred = $stmt->fetch();
            $creditoAtual = $rowCred ? (float)$rowCred['credito'] : 0.0;
            $desconto = min($creditoAtual,$preco);
            $valorCobrado = $preco - $desconto;
            $numero = substr((string)time(),-7);
            $stmt = $pdo->prepare('INSERT INTO solicitacoes (numero,cliente_id,prestador_id,tipo_servico,servico,descricao,preco,valor_cobrado,status,data_criacao) VALUES (?,?,?,?,?,?,?,?,?,NOW())');
            $stmt->execute([
                $numero,
                $user['id'],
                $prestador['id'],
                $tipoSelecionado,
                $servicoSelecionado,
                $descricao,
                $preco,
                $valorCobrado,
                'execucao'
            ]);
            $novoCredito = $creditoAtual - $desconto;
            $stmt = $pdo->prepare('UPDATE usuarios SET credito = ? WHERE id = ?');
            $stmt->execute([$novoCredito,$user['id']]);
            $stmt = $pdo->prepare('SELECT id,nome,email,senha,tipo,credito FROM usuarios WHERE id = ?');
            $stmt->execute([$user['id']]);
            $_SESSION['user'] = $stmt->fetch();
            $numeroGerado = $numero;
            $prestadorEmailConfirmado = $prestadorEmail;
            $passo = 'confirmado';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Serviço Fácil — Solicitar Serviço</title>
    <link rel="stylesheet" href="styles/styles.css" />
</head>
<body>
<div class="container">
    <?php include 'includes/header.php'; ?>
    <main class="card">
        <div class="page-title">Solicitar Serviço</div>
        <?php if ($passo === 'form'): ?>
            <form method="post" class="form-servico">
                <div>
                    <label for="tipoServico">Tipo de Serviço</label>
                    <select id="tipoServico" name="tipo_servico" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $nome => $servicos): ?>
                            <option value="<?php echo htmlspecialchars($nome); ?>" <?php echo $tipoSelecionado === $nome ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($nome); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="servico">Serviço</label>
                    <select id="servico" name="servico" required>
                        <option value="">Selecione...</option>
                        <?php if ($tipoSelecionado && isset($categorias[$tipoSelecionado])): ?>
                            <?php foreach ($categorias[$tipoSelecionado] as $s): ?>
                                <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $servicoSelecionado === $s ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($s); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descreva seu problema..."><?php echo htmlspecialchars($descricao); ?></textarea>
                </div>
                <input type="hidden" name="acao" value="preco" />
                <button class="btn" type="submit">Adicionar Solicitação</button>
            </form>
        <?php elseif ($passo === 'preco'): ?>
            <div class="card" style="margin-top: 20px">
                <h3>Confirme o Preço do Serviço</h3>
                <table>
                    <tr>
                        <th>Tipo de Serviço:</th>
                        <td><?php echo htmlspecialchars($tipoSelecionado); ?></td>
                    </tr>
                    <tr>
                        <th>Serviço:</th>
                        <td><?php echo htmlspecialchars($servicoSelecionado); ?></td>
                    </tr>
                    <tr>
                        <th>Preço:</th>
                        <td>R$ <?php echo number_format($preco,2,',','.'); ?></td>
                    </tr>
                    <tr>
                        <th>Seu crédito:</th>
                        <td>R$ <?php echo number_format($credito,2,',','.'); ?></td>
                    </tr>
                    <tr>
                        <th>Valor a ser cobrado:</th>
                        <td>R$ <?php echo number_format($valorCobrado,2,',','.'); ?></td>
                    </tr>
                </table>
                <div class="actions">
                    <form method="post" style="display:inline-block; margin-right:8px;">
                        <button class="btn ghost" type="submit">Cancelar</button>
                    </form>
                    <form method="post" style="display:inline-block;">
                        <input type="hidden" name="acao" value="confirmar" />
                        <input type="hidden" name="tipo_servico" value="<?php echo htmlspecialchars($tipoSelecionado); ?>" />
                        <input type="hidden" name="servico" value="<?php echo htmlspecialchars($servicoSelecionado); ?>" />
                        <input type="hidden" name="descricao" value="<?php echo htmlspecialchars($descricao); ?>" />
                        <input type="hidden" name="preco" value="<?php echo htmlspecialchars($preco); ?>" />
                        <input type="hidden" name="prestador_email" value="<?php echo htmlspecialchars($prestadorEmail); ?>" />
                        <button class="btn" type="submit">Confirmar</button>
                    </form>
                </div>
            </div>
        <?php elseif ($passo === 'confirmado'): ?>
            <div style="margin-top: 20px">
                <h3>Solicitação Confirmada</h3>
                <p><strong>Número de solicitação:</strong> <span><?php echo htmlspecialchars($numeroGerado); ?></span></p>
                <p><strong>Prestador:</strong> <span><?php echo htmlspecialchars($prestadorEmailConfirmado); ?></span></p>
                <a href="lista_solicitacoes.php" class="btn ghost">Ir para sua lista de solicitações</a>
            </div>
        <?php endif; ?>
    </main>
</div>
</body>
</html>
