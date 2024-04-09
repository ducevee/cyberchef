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
    <form action="../processos/enviar_receita.php" method="POST">
    <label for="foto">Foto da Receita:</label><br>
        <input type="file" id="foto" name="foto" accept="image/*"><br><br>

        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" required><br><br>

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
        <input type="text" id="tempo_preparo" name="tempo_preparo"required>
        <br><br>

        <body>
        <body>
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
    <button type="button" onclick="adicionarIngrediente()">+</button>
    <br><br>

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
            <br><br>

        <label for="modo_preparo">Modo de preparo:</label><br>
        <body>
    <textarea id="textAreaWithLines" name="modo_preparo" rows="10" cols="50" placeholder="Digite aqui..."></textarea>

    <script>
        // Função para atualizar os números de linha
        function updateLineNumbers(textarea) {
            // Dividir o texto por novas linhas
            var lines = textarea.value.split('\n');
            // Adicionar o número da linha antes de cada linha
            for (var i = 0; i < lines.length; i++) {
                // Checar se a linha já tem um número
                if(lines[i].indexOf(i + 1 + '- ') !== 0) {
                    lines[i] = (i + 1) + '- ' + lines[i].replace(/^\d+\) /, '');
                }
            }
            // Juntar as linhas de volta em uma string e atualizar o textarea
            textarea.value = lines.join('\n');
        }

        // Adicionar event listener para o textarea
        document.getElementById('textAreaWithLines').addEventListener('input', function(adicionarNumeroPasso) {
            updateLineNumbers(this);
        });
    </script>
</body>
<br><br>
        <label for="dificuldade">Dificuldade:</label><br>
        <select id="dificuldade" name="dificuldade">
            <option value="vazio"></option>
            <option value="facil">Fácil</option>
            <option value="medio">Médio</option>
            <option value="dificil">Difícil</option><br>
            </select>
        <br><br>

        <label for="categoria">Filtros:</label><br>
        <label for="Salgado">
            <input type="checkbox" id="Salgado" name="filtros[]" value="Salgado">
            Salgado
        </label>
        <br>
        <label for="Doce">
            <input type="checkbox" id="Doce" name="filtros[]" value="Doce">
            Doce
        </label>
        <br>
        <label for="Almoço">
            <input type="checkbox" id="Almoço" name="filtros[]" value="Almoço">
            Almoço
        </label>
        <br>
        <label for="Massas">
            <input type="checkbox" id="Massa" name="filtros[]" value="Massa">
            Massa
        </label>
        <br>
        <label for="Café da manha">
            <input type="checkbox" id="Café da manha" name="filtros[]" value="Café da manha">
            Café da manha
        </label>
        <br>
        <label for="Carnes">
            <input type="checkbox" id="Carnes" name="filtros[]" value="Carnes">
            Carnes
        </label>
        <br>
        <label for="Janta">
            <input type="checkbox" id="Janta" name="filtros[]" value="Janta">
            Jantar
        </label>
        <br>
        <label for="Frutos do mar">
            <input type="checkbox" id="Frutos do mar" name="filtros[]" value="Frutos">
            Frutos do mar
        </label>
        <br>
        <label for="Vegetariano">
            <input type="checkbox" id="Vegetariano" name="filtros[]" value="Vegetariano">
            Vegetariano
        </label>
        <br>
        <label for="Bebidas">
            <input type="checkbox" id="Bebidas" name="filtros[]" value="Bebidas">
            Bebidas
        </label>
        <br>
        <label for="Vegano">
            <input type="checkbox" id="Vegano" name="filtros[]" value="Vegano">
            Vegano
        </label>
        <br>
        <label for="Sobremesa">
            <input type="checkbox" id="Sobremesa" name="filtros[]" value="Sobremesa">
            Sobremesa
        </label>
        <br>
        <label for="Ensopados">
            <input type="checkbox" id="Ensopados" name="filtros[]" value="Ensopados">
            Ensopados
        </select>
        <br>
        </select><br><br>

        <input type="submit" value="Enviar">
    </form>
</body>
</html>
