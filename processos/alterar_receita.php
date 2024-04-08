// ESSE CODIGO CONTÉM CONSULTA SQL, NÃO SEI SE ELAS FUNCIONAM CERTINHO, ESSA PARTE O CHAT FEZ A BOA.


<?php
include_once '../processos/inicializar_banco.php';

// Obtém o ID da receita (onde caralhos fica esse ID?)
$id_receita = $_GET['id_receita'];

// Verifica se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os dados do formulário
    $titulo = $_POST['titulo'];
    $tempo_preparo = $_POST['tempo_preparo'];
    $qtde_porcoes = intval($_POST['qtde_porcoes']);
    $tipo_porcao = $_POST['tipo_porcao'];
    $descricao = $_POST['descricao'];
    $modo_preparo = $_POST['modo_preparo'];
    $dificuldade = $_POST['dificuldade'];
    $categorias = $_POST['filtros'];

    // Atualiza os dados da receita na tabela "Receita"
    $sql = "UPDATE Receita 
            SET titulo = :titulo, tempo_preparo = :tempo_preparo, qtde_porcoes = :qtde_porcoes, 
                tipo_porcao = :tipo_porcao, descricao = :descricao, modo_preparo = :modo_preparo, 
                dificuldade = :dificuldade 
            WHERE id_receita = :id_receita";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titulo' => $titulo,
        ':tempo_preparo' => $tempo_preparo,
        ':qtde_porcoes' => $qtde_porcoes,
        ':tipo_porcao' => $tipo_porcao,
        ':descricao' => $descricao,
        ':modo_preparo' => $modo_preparo,
        ':dificuldade' => $dificuldade,
        ':id_receita' => $id_receita
    ]);

    // Remove todas as categorias associadas a esta receita
    $sql_delete_categorias = "DELETE FROM Receita_Categoria WHERE id_receita = :id_receita";
    $stmt_delete_categorias = $pdo->prepare($sql_delete_categorias);
    $stmt_delete_categorias->execute([':id_receita' => $id_receita]);

    // Insere as novas categorias associadas a esta receita na tabela de relacionamento
    foreach ($categorias as $categoria) {
        $sql_inserir_categoria = "INSERT INTO Receita_Categoria (id_receita, id_categoria) 
                                  VALUES (:id_receita, (SELECT id_categoria FROM Categoria WHERE categoria = :categoria))";
        $stmt_inserir_categoria = $pdo->prepare($sql_inserir_categoria);
        $stmt_inserir_categoria->execute([':id_receita' => $id_receita, ':categoria' => $categoria]);
    }

    // Redireciona para a página de detalhes da receita após a atualização
    header("Location: detalhes_receita.php?id_receita=$id_receita");
    exit();
}

// Consulta SQL para obter os detalhes da receita com base no ID fornecido
$sql = "SELECT * FROM Receita WHERE id_receita = :id_receita";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_receita' => $id_receita]);
$receita = $stmt->fetch(PDO::FETCH_ASSOC);

// Consulta SQL para obter as categorias associadas a esta receita
$sql_categorias = "SELECT categoria FROM Categoria 
                   WHERE id_categoria IN (SELECT id_categoria FROM Receita_Categoria WHERE id_receita = :id_receita)";
$stmt_categorias = $pdo->prepare($sql_categorias);
$stmt_categorias->execute([':id_receita' => $id_receita]);
$categorias_associadas = $stmt_categorias->fetchAll(PDO::FETCH_COLUMN);

// Consulta SQL para obter todas as categorias disponíveis
$sql_todas_categorias = "SELECT categoria FROM Categoria";
$stmt_todas_categorias = $pdo->query($sql_todas_categorias);
$todas_categorias = $stmt_todas_categorias->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Receita</title>
</head>
<body>
    <h1>Editar Receita</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <!-- Campos do formulário preenchidos com os dados da receita -->
        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" value="<?php echo $receita['titulo']; ?>" required><br><br>

        <label for="tempo_preparo">Tempo de preparo:</label><br>
        <input type="text" id="tempo_preparo" name="tempo_preparo" value="<?php echo $receita['tempo_preparo']; ?>" required>
        <br><br>

        <label for="qtde_porcoes">Rendimento:</label><br>
        <input type="number" id="qtde_porcoes" name="qtde_porcoes" value="<?php echo $receita['qtde_porcoes']; ?>" required>
        <select id="tipo_porcao" name="tipo_porcao">
            <option value="fatia" <?php echo ($receita['tipo_porcao'] == 'fatia') ? 'selected' : ''; ?>>Fatia(s)</option>
            <option value="prato" <?php echo ($receita['tipo_porcao'] == 'prato') ? 'selected' : ''; ?>>Prato(s)</option>
            <option value="porcao" <?php echo ($receita['tipo_porcao'] == 'porcao') ? 'selected' : ''; ?>>Porção(s)</option>
        </select>
        <br><br>

        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao" rows="5" cols="45" required><?php echo $receita['descricao']; ?></textarea><br><br>

        <label for="modo_preparo">Modo de preparo:</label><br>
        <textarea id="modo_preparo" name="modo_preparo" rows="10" cols="50" required><?php echo $receita['modo_preparo']; ?></textarea><br><br>

        <label for="dificuldade">Dificuldade:</label><br>
        <select id="dificuldade" name="dificuldade">
            <option value="facil" <?php echo ($receita['dificuldade'] == 'facil') ? 'selected' : ''; ?>>Fácil</option>
            <option value="medio" <?php echo ($receita['dificuldade'] == 'medio') ? 'selected' : ''; ?>>Médio</option>
            <option value="dificil" <?php echo ($receita['dificuldade'] == 'dificil') ? 'selected' : ''; ?>>Difícil</option>
        </select>
        <br><br>

        <label for="categorias">Categorias:</label><br>
        <?php foreach ($todas_categorias as $categoria) : ?>
            <label>
                <input type="checkbox" name="filtros[]" value="<?php echo $categoria; ?>" <?php echo (in_array($categoria, $categorias_associadas)) ? 'checked' : ''; ?>>
                <?php echo $categoria; ?>
            </label><br>
        <?php endforeach; ?>
        <br>

        <input type="submit" value="Salvar Alterações">
    </form>
</body>
</html>
