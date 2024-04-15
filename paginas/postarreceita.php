<?php
include_once '../processos/inicializar_banco.php';
$id_receita = isset($_GET['id_receita']) ? $_GET['id_receita'] : null;
$receita = null;

if ($id_receita) {
    $stmt = $pdo->prepare("SELECT * FROM Receita WHERE id_receita = ?");
    $stmt->execute([$id_receita]);
    $receita = $stmt->fetch();
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
        <label for="foto">Foto da Receita:</label><br>
        <input type="file" id="foto" name="foto" accept="image/*"><br><br>

        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" required value="<?= htmlspecialchars($receita['titulo'] ?? ''); ?>"><br><br>

        <label for="qtde_porcoes">Rendimento:</label><br>
        <input type="number" id="qtde_porcoes" name="qtde_porcoes" required>
        <select id="tipo_porcao" name="tipo_porcao">
            <option value="vazio"></option>
            <option value="fatia">Fatia(s)</option>
            <option value="prato">Prato(s)</option>
            <option value="porcao">Porção(s)</option>
            <option value="copo">Copo(s)</option>
        </select>
        <br><br>

        <label for="descricao">Descrição:</label><br>
        <textarea id="descricao" name="descricao" rows="5" cols="45" required></textarea><br><br>

        <label for="tempo_preparo">Tempo de preparo:</label><br>
        <input type="text" id="tempo_preparo" name="tempo_preparo" required><br><br>

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
        <textarea id="textAreaWithLines" name="modo_preparo" rows="10" cols="50" placeholder="Digite aqui..."></textarea><br><br>

        <label for="dificuldade">Dificuldade:</label><br>
        <select id="dificuldade" name="dificuldade">
            <option value="vazio"></option>
            <option value="facil">Fácil</option>
            <option value="medio">Médio</option>
            <option value="dificil">Difícil</option>
        </select><br><br>

        <label for="categoria">Filtros:</label><br>
        <input type="checkbox" id="Salgado" name="filtros[]" value="Salgado"> Salgado<br>
        <input type="checkbox" id="Doce" name="filtros[]" value="Doce"> Doce<br>
        <input type="checkbox" id="Almoço" name="filtros[]" value="Almoço"> Almoço<br>
        <input type="checkbox" id="Massa" name="filtros[]" value="Massa"> Massa<br>
        <input type="checkbox" id="Cafe_da_manha" name="filtros[]" value="Café da manhã"> Café da manhã<br>
        <input type="checkbox" id="Carnes" name="filtros[]" value="Carnes"> Carnes<br>
        <input type="checkbox" id="Janta" name="filtros[]" value="Janta"> Jantar<br>
        <input type="checkbox" id="Frutos_do_mar" name="filtros[]" value="Frutos do mar"> Frutos do mar<br>
        <input type="checkbox" id="Vegetariano" name="filtros[]" value="Vegetariano"> Vegetariano<br>
        <input type="checkbox" id="Bebidas" name="filtros[]" value="Bebidas"> Bebidas<br>
        <input type="checkbox" id="Vegano" name="filtros[]" value="Vegano"> Vegano<br>
        <input type="checkbox" id="Sobremesa" name="filtros[]" value="Sobremesa"> Sobremesa<br>
        <input type="checkbox" id="Ensopados" name="filtros[]" value="Ensopados"> Ensopados<br>
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
