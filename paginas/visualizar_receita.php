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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação de Receita</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style_avaliar.css">
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
    <?php
if (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
} else {
    header("Location: ../paginas/login.php");
    exit(); 
}
?>
<div class="avaliacoes">
    <h1 style="font-family: 'Maven Pro', sans-serif;";>Avalie</h1>
    <p style="font-family: 'Maven Pro', sans-serif;";>Dê uma nota e adicione um cometário à essa receita!</p>

    <?php
    if(isset($_SESSION['msg'])){
        echo $_SESSION['msg'];
        unset($_SESSION['msg']);
    }

    ?>

    <form method="POST" action="../processos/processa_avaliacoes.php">

        <div class="estrelas" style="font-size: 20px;">
        <input type="radio" name="estrela" id="vazio" value="" checked>
        <label for="estrela1"><i class="opcao fa" aria-hidden="true"></i></label>
        <input type="radio" name="estrela" id="estrela1" value="1">
        <label for="estrela2"><i class="opcao fa" aria-hidden="true"></i></label>
        <input type="radio" name="estrela" id="estrela2" value="2" >
        <label for="estrela3"><i class="opcao fa" aria-hidden="true"></i></label>
        <input type="radio" name="estrela" id="estrela3" value="3" >
        <label for="estrela4"><i class="opcao fa" aria-hidden="true"></i></label>
        <input type="radio" name="estrela" id="estrela4" value="4" >
        <label for="estrela5"><i class="opcao fa" aria-hidden="true"></i></label>
        <input type="radio" name="estrela" id="estrela5" value="5" > <br><br>
        <textarea name="mensagem" id="" cols="50" rows="4" placeholder="Deixe aqui sua opinião!" style="width: 350px; padding: 10px; border-radius: 5px; margin-bottom: 10px;"></textarea> <br>
        <input type="hidden" name="id_receita" value="<?php echo $id_receita; ?>">

        <input type="submit" value="Cadastrar" style="background-color: #007bff; color: #fff; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease; font-size: 14px; font-family: 'Maven Pro', sans-serif;">
        
        <br><br>
</div>
</form>
<?php include '../processos/listar_avaliacoes.php' ?>
</body>
</html>


