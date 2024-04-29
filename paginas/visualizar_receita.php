<?php
session_start();
include_once '../processos/inicializar_banco.php'; // Ajuste o caminho conforme necessário

$id_receita = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id_receita) {
    echo "<script>alert('Nenhuma receita especificada.'); window.location.href='listar_receita.php';</script>";
    exit;
}

// Busca detalhada da receita, incluindo ingredientes e categorias
try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.nome AS nome_usuario, 
            GROUP_CONCAT(DISTINCT i.ingrediente SEPARATOR ', ') AS ingredientes, 
            GROUP_CONCAT(DISTINCT c.categoria SEPARATOR ', ') AS categorias
        FROM Receita r
        JOIN usuarios u ON r.fk_id_usuario = u.id
        LEFT JOIN Receita_Ingrediente ri ON r.id_receita = ri.id_receita
        LEFT JOIN Ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        LEFT JOIN Receita_Categoria rc ON r.id_receita = rc.id_receita
        LEFT JOIN Categoria c ON rc.id_categoria = c.id_categoria
        WHERE r.id_receita = ?
        GROUP BY r.id_receita
    ");
    $stmt->execute([$id_receita]);
    $receita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receita) {
        echo "<script>alert('Receita não encontrada.'); window.location.href='listar_receita.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Erro de banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($receita['titulo']); ?></title>
    <link rel="stylesheet" href="../css/style_visualizar_receita.css"> <!-- Certifique-se de que o caminho está correto -->
</head>
<body>
    <div class="receita-container">
        <h1><?= htmlspecialchars($receita['titulo']); ?></h1>
        <p><strong>Postado por:</strong> <?= htmlspecialchars($receita['nome_usuario']); ?></p>
        <?php if ($receita['foto']): ?>
            <img src="../uploads/<?= htmlspecialchars($receita['foto']); ?>" alt="Imagem da receita" style="max-width: 500px;">
        <?php endif; ?>
        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($receita['descricao'])); ?></p>
        <p><strong>Ingredientes:</strong> <?= htmlspecialchars($receita['ingredientes']); ?></p>
        <p><strong>Categorias:</strong> <?= htmlspecialchars($receita['categorias']); ?></p>
        <p><strong>Tempo de Preparo:</strong> <?= htmlspecialchars($receita['tempo_preparo']); ?></p>
        <p><strong>Dificuldade:</strong> <?= htmlspecialchars($receita['dificuldade']); ?></p>
        <p><strong>Modo de Preparo:</strong> <?= nl2br(htmlspecialchars($receita['modo_preparo'])); ?></p>
    </div>
</body>
</html>
