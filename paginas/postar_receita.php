<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include_once '../processos/inicializar_banco.php';
$id_receita = isset($_GET['id_receita']) ? $_GET['id_receita'] : null;
$receita = null;

if ($id_receita) {
    $stmt = $pdo->prepare("SELECT * FROM Receita WHERE id_receita = ?");
    $stmt->execute([$id_receita]);
    $receita = $stmt->fetch();

    if ($_SESSION['usuario_id'] != $receita['fk_id_usuario']) {
        // Redireciona para a página de criação de receita se não for o dono
        header('Location: postar_receita.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poste suas receitas!</title>
    <link rel="stylesheet" href="../css/style_receita.css">
</head>
<body>
    <h1>Poste suas receitas</h1>
    <form action="../processos/enviar_receita.php" method="POST" enctype="multipart/form-data">
        <?php if ($id_receita): ?>
            <input type="hidden" name="id_receita" value="<?= $receita['id_receita'] ?>">
        <?php endif; ?>

        <label for="foto">Foto da Receita:</label><br>
        <input type="file" id="foto" name="foto" accept="image/*"><br>
        <?php if ($id_receita && $receita['foto']): ?>
            <img src="../uploads/<?= htmlspecialchars($receita['foto']); ?>" alt="Foto atual" height="100"><br>
        <?php endif; ?><br>

        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($receita['titulo'] ?? ''); ?>"><br><br>

        <label for="qtde_porcoes">Rendimento:</label><br>
        <input type="number" id="qtde_porcoes" name="qtde_porcoes" required value="<?= isset($receita['qtde_porcoes']) ? $receita['qtde_porcoes'] : ''; ?>">
        <select id="tipo_porcao" name="tipo_porcao">
            <option value="vazio" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'vazio') ? 'selected' : ''; ?>></option>
            <option value="fatia" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'fatia') ? 'selected' : ''; ?>>Fatia(s)</option>
            <option value="prato" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'prato') ? 'selected' : ''; ?>>Prato(s)</option>
            <option value="porcao" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'porcao') ? 'selected' : ''; ?>>Porção(s)</option>
            <option value="copo" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'copo') ? 'selected' : ''; ?>>Copo(s)</option>
        </select>
        <br><br>

        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao" rows="5" cols="45" required><?= htmlspecialchars($receita['descricao'] ?? ''); ?></textarea><br><br>

        <label for="tempo_preparo">Tempo de preparo:</label><br>
        <input type="text" id="tempo_preparo" name="tempo_preparo" required value="<?= htmlspecialchars($receita['tempo_preparo'] ?? ''); ?>"><br><br>

        <div id="container-ingredientes">
            <label>Ingredientes:</label><br>
            <div class="ingrediente">
                <input type="number" name="quantidades[]" required>
                <select name="unidades[]" required>
                    <option value="">Selecione a unidade</option>
                    <option value="unidade">unidade(s)</option>
                    <option value="ml">ml</option>
                    <option value="gramas">g</option>
                    <option value="xicara">Xícara(s)</option>
                    <option value="colher">Colher(s)</option>
                </select>
                <span> de </span>
                <input type="text" name="ingredientes[]" placeholder="Nome do ingrediente" required>
            </div>
        </div>
        <button type="button" onclick="adicionarIngrediente()">+</button><br><br>

        <label for="modo_preparo">Modo de preparo:</label><br>
        <textarea id="textAreaWithLines" name="modo_preparo" rows="10" cols="50" placeholder="Digite aqui..."><?= htmlspecialchars($receita['modo_preparo'] ?? ''); ?></textarea><br><br>

        <label for="dificuldade">Dificuldade:</label><br>
        <select id="dificuldade" name="dificuldade">
            <option value="facil" <?= (isset($receita['dificuldade']) && $receita['dificuldade'] == 'facil') ? 'selected' : ''; ?>>Fácil</option>
            <option value="medio" <?= (isset($receita['dificuldade']) && $receita['dificuldade'] == 'medio') ? 'selected' : ''; ?>>Médio</option>
            <option value="dificil" <?= (isset($receita['dificuldade']) && $receita['dificuldade'] == 'dificil') ? 'selected' : ''; ?>>Difícil</option>
        </select><br><br>

        <label for="categoria">Filtros:</label><br>
        <input type="checkbox" id="Salgado" name="filtros[]" value="Salgado" <?= strpos($receita['categorias'] ?? '', 'Salgado') !== false ? 'checked' : ''; ?>> Salgado<br>
        <input type="checkbox" id="Doce" name="filtros[]" value="Doce" <?= strpos($receita['categorias'] ?? '', 'Doce') !== false ? 'checked' : ''; ?>> Doce<br>
        <input type="checkbox" id="Almoço" name="filtros[]" value="Almoço" <?= strpos($receita['categorias'] ?? '', 'Almoço') !== false ? 'checked' : ''; ?>> Almoço<br>
        <input type="checkbox" id="Massa" name="filtros[]" value="Massa" <?= strpos($receita['categorias'] ?? '', 'Massa') !== false ? 'checked' : ''; ?>> Massa<br>
        <input type="checkbox" id="Cafe_da_manha" name="filtros[]" value="Café da manhã" <?= strpos($receita['categorias'] ?? '', 'Café da manhã') !== false ? 'checked' : ''; ?>> Café da manhã<br>
        <input type="checkbox" id="Carnes" name="filtros[]" value="Carnes" <?= strpos($receita['categorias'] ?? '', 'Carnes') !== false ? 'checked' : ''; ?>> Carnes<br>
        <input type="checkbox" id="Janta" name="filtros[]" value="Janta" <?= strpos($receita['categorias'] ?? '', 'Janta') !== false ? 'checked' : ''; ?>> Jantar<br>
        <input type="checkbox" id="Frutos_do_mar" name="filtros[]" value="Frutos do mar" <?= strpos($receita['categorias'] ?? '', 'Frutos do mar') !== false ? 'checked' : ''; ?>> Frutos do mar<br>
        <input type="checkbox" id="Vegetariano" name="filtros[]" value="Vegetariano" <?= strpos($receita['categorias'] ?? '', 'Vegetariano') !== false ? 'checked' : ''; ?>> Vegetariano<br>
        <input type="checkbox" id="Bebidas" name="filtros[]" value="Bebidas" <?= strpos($receita['categorias'] ?? '', 'Bebidas') !== false ? 'checked' : ''; ?>> Bebidas<br>
        <input type="checkbox" id="Vegano" name="filtros[]" value="Vegano" <?= strpos($receita['categorias'] ?? '', 'Vegano') !== false ? 'checked' : ''; ?>> Vegano<br>
        <input type="checkbox" id="Sobremesa" name="filtros[]" value="Sobremesa" <?= strpos($receita['categorias'] ?? '', 'Sobremesa') !== false ? 'checked' : ''; ?>> Sobremesa<br>
        <input type="checkbox" id="Ensopados" name="filtros[]" value="Ensopados" <?= strpos($receita['categorias'] ?? '', 'Ensopados') !== false ? 'checked' : ''; ?>> Ensopados<br>
        <input type="submit" value="Enviar">
    </form>

    <script>
        function adicionarIngrediente() {
            const container = document.getElementById('container-ingredientes');
            const ingredienteDiv = document.createElement('div');
            ingredienteDiv.classList.add('ingrediente');
            ingredienteDiv.innerHTML = `
                <input type="number" name="quantidades[]" required>
                <select name="unidades[]" required>
                    <option value="">Selecione a unidade</option>
                    <option value="unidade">unidade(s)</option>
                    <option value="ml">ml</option>
                    <option value="gramas">g</option>
                    <option value="xicara">Xícara(s)</option>
                    <option value="colher">Colher(s)</option>
                </select>
                <span> de </span>
                <input type="text" name="ingredientes[]" placeholder="Nome do ingrediente" required>
            `;
            container.appendChild(ingredienteDiv);
        }
    </script>
</body>
</html>
