// ESSE CÓDIGO NÃO TEM CONSULTA SQL, ELE É IGUAL O ATERAR_RECEITA.PHP

<?php
include_once '../processos/inicializar_banco.php';

// Obtém o ID da receita
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
}


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
