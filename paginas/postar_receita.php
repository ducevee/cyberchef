<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

include_once '../processos/inicializar_banco.php';
$id_receita = isset($_GET['id_receita']) ? $_GET['id_receita'] : null;
$receita = null;
$ingredientesReceita = [];
$categoriasReceita = [];

if ($id_receita) {
    $stmt = $pdo->prepare("SELECT * FROM Receita WHERE id_receita = ?");
    $stmt->execute([$id_receita]);
    $receita = $stmt->fetch();
    $stmt = $pdo->prepare("SELECT I.quantidade, I.unidade, I.ingrediente 
                       FROM Receita_Ingrediente RI 
                       JOIN Ingredientes I ON RI.id_ingrediente = I.id_ingrediente 
                       WHERE RI.id_receita = ?");
    $stmt->execute([$id_receita]);
    $ingredientesReceita = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->prepare("SELECT C.categoria FROM Receita_Categoria RC JOIN Categoria C ON RC.id_categoria = C.id_categoria WHERE RC.id_receita = ?");
    $stmt->execute([$id_receita]);
    $categoriasReceita = $stmt->fetchAll(PDO::FETCH_COLUMN);


    if ($_SESSION['usuario_id'] != $receita['fk_id_usuario']) {
        // Redireciona para a página de criação de receita se não for o dono
        header('Location: postar_receita.php');
        exit;
    }
}

if(isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
    echo "<script>alert('" . htmlspecialchars($mensagem) . "');</script>";
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
        <input type="file" id="foto" name="foto" accept="image/*" onchange="previewImage();"><br>
        <img id="preview" src="<?php echo ($id_receita && $receita['foto']) ? "../uploads/" . htmlspecialchars($receita['foto']) : ''; ?>" alt="Foto da receita" style="height: 100px; display: <?php echo ($id_receita && $receita['foto']) ? 'block' : 'none'; ?>;"><br>

        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($receita['titulo'] ?? ''); ?>"><br><br>

        <label for="qtde_porcoes">Rendimento:</label><br>
        <input type="number" id="qtde_porcoes" name="qtde_porcoes" required value="<?= isset($receita['qtde_porcoes']) ? $receita['qtde_porcoes'] : ''; ?>">
        <select id="tipo_porcao" name="tipo_porcao">
            <option value="vazio" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'vazio') ? 'selected' : ''; ?>></option>
            <option value="fatia" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'fatia') ? 'selected' : ''; ?>>Fatia(s)</option>
            <option value="prato" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'prato') ? 'selected' : ''; ?>>Prato(s)</option>
            <option value="porcao" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'porcao') ? 'selected' : ''; ?>>Porção(ões)</option>
            <option value="copo" <?= (isset($receita['tipo_porcao']) && $receita['tipo_porcao'] == 'copo') ? 'selected' : ''; ?>>Copo(s)</option>
        </select>
        <br><br>

        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao" rows="5" cols="45" required><?= htmlspecialchars($receita['descricao'] ?? ''); ?></textarea><br><br>

        <label for="tempo_preparo">Tempo de preparo:</label><br>
        <input type="text" id="tempo_preparo" name="tempo_preparo" required value="<?= htmlspecialchars($receita['tempo_preparo'] ?? ''); ?>"><br><br>

        <div id="container-ingredientes">
            <label>Ingredientes:</label><br>
            <?php foreach ($ingredientesReceita as $ingrediente): ?>
                <div class="ingrediente">
                    <input type="number" name="quantidades[]" value="<?= htmlspecialchars($ingrediente['quantidade']); ?>" required>
                    <select name="unidades[]" required>
                        <!-- Preencha com as opções, selecionando a unidade correta -->
                        <option value="unidade" <?= $ingrediente['unidade'] == 'unidade' ? 'selected' : ''; ?>>unidade(s)</option>
                        <!-- Repita para outras unidades -->
                    </select>
                    <span> de </span>
                    <input type="text" name="ingredientes[]" value="<?= htmlspecialchars($ingrediente['ingrediente']); ?>" placeholder="Nome do ingrediente" required>
                </div>
            <?php endforeach; ?>
            <!-- Restante do formulário -->
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

        <label for="categoria">Categorias:</label><br>
        <input type="checkbox" id="Salgado" name="categorias[]" value="Salgado" <?= in_array('Salgado', $categoriasReceita) ? 'checked' : ''; ?>> Salgado<br>
        <input type="checkbox" id="Doce" name="categorias[]" value="Doce" <?= in_array('Doce', $categoriasReceita) ? 'checked' : ''; ?>> Doce<br>
        <input type="checkbox" id="Almoço" name="categorias[]" value="Almoço" <?= in_array('Almoço', $categoriasReceita) ? 'checked' : ''; ?>> Almoço<br>
        <input type="checkbox" id="Massa" name="categorias[]" value="Massa" <?= in_array('Massa', $categoriasReceita) ? 'checked' : ''; ?>> Massa<br>
        <input type="checkbox" id="Cafe_da_manha" name="categorias[]" value="Café da manhã" <?= in_array('Café da manhã', $categoriasReceita) ? 'checked' : ''; ?>> Café da manhã<br>
        <input type="checkbox" id="Carnes" name="categorias[]" value="Carnes" <?= in_array('Carnes', $categoriasReceita) ? 'checked' : ''; ?>> Carnes<br>
        <input type="checkbox" id="Janta" name="categorias[]" value="Jantar" <?= in_array('Janta', $categoriasReceita) ? 'checked' : ''; ?>> Jantar<br>
        <input type="checkbox" id="Frutos_do_mar" name="categorias[]" value="Frutos do mar" <?= in_array('Frutos do mar', $categoriasReceita) ? 'checked' : ''; ?>> Frutos do mar<br>
        <input type="checkbox" id="Vegetariano" name="categorias[]" value="Vegetariano" <?= in_array('Vegetariano', $categoriasReceita) ? 'checked' : ''; ?>> Vegetariano<br>
        <input type="checkbox" id="Bebidas" name="categorias[]" value="Bebidas" <?= in_array('Bebidas', $categoriasReceita) ? 'checked' : ''; ?>> Bebidas<br>
        <input type="checkbox" id="Vegano" name="categorias[]" value="Vegano" <?= in_array('Vegano', $categoriasReceita) ? 'checked' : ''; ?>> Vegano<br>
        <input type="checkbox" id="Sobremesa" name="categorias[]" value="Sobremesa" <?= in_array('Sobremesa', $categoriasReceita) ? 'checked' : ''; ?>> Sobremesa<br>
        <input type="checkbox" id="Ensopados" name="categorias[]" value="Ensopados" <?= in_array('Ensopados', $categoriasReceita) ? 'checked' : ''; ?>> Ensopados<br>
        <br>
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
        };

        function previewImage() {
            var file = document.getElementById("foto").files[0];
            var preview = document.getElementById("preview");
            var reader = new FileReader();
            
            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = 'block';
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
