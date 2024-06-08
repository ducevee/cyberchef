<?php
session_start();

include_once '../processos/inicializar_banco.php';

if (isset($_GET['mensagem'])) {
    $mensagem = $_GET['mensagem'];
    echo "<script>alert('" . htmlspecialchars($mensagem) . "');</script>";
}

// Função para excluir a receita
function excluir_receita($id_receita) {
    global $pdo;

    try {
        // Verificar se o usuário logado é o proprietário da receita
        $id_usuario_logado = $_SESSION['usuario_id'];
        $sql_check_owner = "SELECT fk_id_usuario FROM Receita WHERE id_receita = :id_receita";
        $stmt_check_owner = $pdo->prepare($sql_check_owner);
        $stmt_check_owner->bindParam(':id_receita', $id_receita);
        $stmt_check_owner->execute();
        $result_check_owner = $stmt_check_owner->fetch(PDO::FETCH_ASSOC);

        if (!$result_check_owner || $result_check_owner['fk_id_usuario'] != $id_usuario_logado) {
            return false; // Não é o proprietário da receita
        }

        // Excluir registros da tabela Receita_Ingrediente
        $sql_delete_ingrediente = "DELETE FROM Receita_Ingrediente WHERE id_receita = :id_receita";
        $stmt_ingrediente = $pdo->prepare($sql_delete_ingrediente);
        $stmt_ingrediente->bindParam(':id_receita', $id_receita);
        $stmt_ingrediente->execute();

        // Excluir registros da tabela Receita_Categoria
        $sql_delete_categoria = "DELETE FROM Receita_Categoria WHERE id_receita = :id_receita";
        $stmt_categoria = $pdo->prepare($sql_delete_categoria);
        $stmt_categoria->bindParam(':id_receita', $id_receita);
        $stmt_categoria->execute();

        // Excluir a receita da tabela Receita
        $sql_delete_receita = "DELETE FROM Receita WHERE id_receita = :id_receita";
        $stmt_receita = $pdo->prepare($sql_delete_receita);
        $stmt_receita->bindParam(':id_receita', $id_receita);
        $stmt_receita->execute();

        // Verificar se a exclusão foi bem-sucedida
        return $stmt_receita->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Verificar se o formulário de exclusão foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_receita'])) {
    $id_receita_excluir = $_POST['id_receita_excluir'];

    if (!empty($id_receita_excluir)) {
        $exclusao_sucesso = excluir_receita($id_receita_excluir);
        if ($exclusao_sucesso) {
            echo "<script>alert('Receita excluída com sucesso!');</script>";
            // Recarregar a página após a exclusão
            echo "<script>window.location.href = '../paginas/listar_receita.php';</script>";
            exit;
        } else {
            echo "<script>alert('Erro ao excluir a receita. Por favor, tente novamente.');</script>";
        }
    }
}


// Capturando dados do filtro
$dificuldade = $_GET['dificuldade'] ?? '';
$categorias = $_GET['categorias'] ?? [];
$search_query = $_GET['search_query'] ?? '';

// Preparando condições para a consulta SQL
$conditions = [];
$params = [];

if (!empty($search_query)) {
    $search_query_sql = "%$search_query%";
    $conditions[] = "(r.titulo LIKE :search_query OR u.nome LIKE :search_query OR c.categoria LIKE :search_query)";
    $params[':search_query'] = $search_query_sql;
}

if (!empty($dificuldade)) {
    $conditions[] = "r.dificuldade = :dificuldade";
    $params[':dificuldade'] = $dificuldade;
}

if (!empty($categorias)) {
    $placeholders = implode(',', array_map(function($key) { return ":categoria_$key"; }, array_keys($categorias)));
    $conditions[] = "c.categoria IN ($placeholders)";
    foreach ($categorias as $key => $categoria) {
        $params[":categoria_$key"] = $categoria;
    }
}

// Montando a consulta SQL baseada nas condições existentes
$sql = "SELECT r.*, 
                GROUP_CONCAT(DISTINCT i.ingrediente SEPARATOR ', ') AS ingredientes, 
                GROUP_CONCAT(DISTINCT c.categoria SEPARATOR ', ') AS categorias,
                u.nome AS nome_usuario
        FROM Receita r
        LEFT JOIN Receita_Ingrediente ri ON r.id_receita = ri.id_receita
        LEFT JOIN Ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        LEFT JOIN Receita_Categoria rc ON r.id_receita = rc.id_receita
        LEFT JOIN Categoria c ON rc.id_categoria = c.id_categoria
        LEFT JOIN usuarios u ON r.fk_id_usuario = u.id";

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " GROUP BY r.id_receita";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Receitas</title>
    <link rel="stylesheet" href="../css/style_listar_receita.css">
</head>
<body>
    <header>
        <nav class="navHeader">
            <a href="<?php echo (isset($_SESSION['usuario_id']) && $_SESSION['is_admin'] == 1) ? 'home_admin.php' : 'home_usuario.php'; ?>" id="link-logo" title="Página inicial">
                <img src="../css/img/cyber_chef_logo.png" alt="logo" id="logo">
            </a>
            <form class="search-container" method="get" action="">
                <input type="search" name="search_query" class="search-input" placeholder="Busque por uma receita, Chef ou Categoria." value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit" class="search-button">
                    <svg width="19" height="21" viewBox="0 0 28 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.0114 15.7233H18.7467L18.2985 15.3373C19.8674 13.7078 20.8119 11.5923 20.8119 9.29102C20.8119 4.15952 16.1532 0 10.4059 0C4.65866 0 0 4.15952 0 9.29102C0 14.4225 4.65866 18.582 10.4059 18.582C12.9834 18.582 15.3528 17.7387 17.1778 16.3379L17.6101 16.7381V17.8674L25.6146 25L28 22.8702L20.0114 15.7233ZM10.4059 15.7233C6.41967 15.7233 3.20183 12.8502 3.20183 9.29102C3.20183 5.73185 6.41967 2.85878 10.4059 2.85878C14.3922 2.85878 17.6101 5.73185 17.6101 9.29102C17.6101 12.8502 14.3922 15.7233 10.4059 15.7233Z" fill="white"/>
                    </svg> 
                </button>
                <!-- Adicionando inputs hidden para preservar os filtros -->
                <input type="hidden" name="dificuldade" value="<?= htmlspecialchars($dificuldade) ?>">
                <?php foreach ($categorias as $categoria): ?>
                    <input type="hidden" name="categorias[]" value="<?= htmlspecialchars($categoria) ?>">
                <?php endforeach; ?>
            </form>
            <ul id="lista">
                <li>
                    <a class="linksHeader" href=".">EM ALTA</a>
                </li>
                <li>
                    <a class="linksHeader" href="../paginas/listar_receita.php">NOVIDADES</a>
                </li>
                <li>
                    <a class="linksHeader" href=".">CATEGORIA</a>
                    </li>
                </ul>
            
                <?php
                    if (isset($_SESSION['usuario_id'])) {
                        echo "<div class='user'>Bem-vindo, <b>" . htmlspecialchars($_SESSION['usuario_nome']) . "!</b></div>";
                        echo  "<a href='../processos/logout.php' alt='Sair' title='Sair'>
                                    <svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' fill='#FFF' version='1.1' id='Capa_1' width='25px' height='25px' viewBox='0 0 492.5 492.5' xml:space='preserve'>
                                        <g>
                                            <path d='M184.646,0v21.72H99.704v433.358h31.403V53.123h53.539V492.5l208.15-37.422v-61.235V37.5L184.646,0z M222.938,263.129   c-6.997,0-12.67-7.381-12.67-16.486c0-9.104,5.673-16.485,12.67-16.485s12.67,7.381,12.67,16.485   C235.608,255.748,229.935,263.129,222.938,263.129z'/>
                                        </g>
                                    </svg>
                                </a>";
                    }
                ?>
            </nav>
        </header>
    <a href="../paginas/home_usuario.php" style="font-family: 'Maven Pro', sans-serif; font-size: 50px; margin-left: 20px;">&larr;</a>
    <main>
        <!-- Exibir as receitas -->
        <form class="filtros" action="listar_receita.php" method="get">
            <div>
                <label for="dificuldade">Dificuldade:</label><br>
                <select id="dificuldade" name="dificuldade" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <option value="facil" <?= $dificuldade == 'facil' ? 'selected' : '' ?>>Fácil</option>
                    <option value="medio" <?= $dificuldade == 'medio' ? 'selected' : '' ?>>Médio</option>
                    <option value="dificil" <?= $dificuldade == 'dificil' ? 'selected' : '' ?>>Difícil</option>
                </select>
            </div><br><br>

            <div>
                <label for="categoria">Categorias:</label><br>
                <div class="dropdown">
                    <button class="dropbtn" id="dropbtn" type="button" onclick="toggleDropdown()">Selecione Categorias</button>
                    <div class="dropdown-content" id="dropdown-content">
                        <label><input type="checkbox" name="categorias[]" value="Salgado" <?= in_array('Salgado', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Salgado</label>
                        <label><input type="checkbox" name="categorias[]" value="Doce" <?= in_array('Doce', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Doce</label>
                        <label><input type="checkbox" name="categorias[]" value="Almoço" <?= in_array('Almoço', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Almoço</label>
                        <label><input type="checkbox" name="categorias[]" value="Massa" <?= in_array('Massa', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Massa</label>
                        <label><input type="checkbox" name="categorias[]" value="Café da manhã" <?= in_array('Café da manhã', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Café da manhã</label>
                        <label><input type="checkbox" name="categorias[]" value="Carnes" <?= in_array('Carnes', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Carnes</label>
                        <label><input type="checkbox" name="categorias[]" value="Jantar" <?= in_array('Jantar', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Jantar</label>
                        <label><input type="checkbox" name="categorias[]" value="Frutos do mar" <?= in_array('Frutos do mar', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Frutos do mar</label>
                        <label><input type="checkbox" name="categorias[]" value="Vegetariano" <?= in_array('Vegetariano', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Vegetariano</label>
                        <label><input type="checkbox" name="categorias[]" value="Bebidas" <?= in_array('Bebidas', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Bebidas</label>
                        <label><input type="checkbox" name="categorias[]" value="Vegano" <?= in_array('Vegano', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Vegano</label>
                        <label><input type="checkbox" name="categorias[]" value="Sobremesa" <?= in_array('Sobremesa', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Sobremesa</label>
                        <label><input type="checkbox" name="categorias[]" value="Ensopados" <?= in_array('Ensopados', $categorias) ? 'checked' : '' ?> onchange="this.form.submit()"> Ensopados</label>
                    </div>
                </div>
            </div><br><br>
            

            <a href="listar_receita.php" class="btn-limpar">Limpar Filtros</a>
        </form>

        <?php if (!empty($receitas)) : ?>
            <ul>
                <?php foreach ($receitas as $receita) : ?>
                    <li>
                        <!-- Link para a página de visualização da receita -->
                        <h3><a href="visualizar_receita.php?id=<?= $receita['id_receita']; ?>"><?= htmlspecialchars($receita['titulo']); ?></a></h3>
                        <p>Postado por: <?= htmlspecialchars($receita['nome_usuario']); ?></p>
                        <img src="../uploads/<?= htmlspecialchars($receita['foto']); ?>" alt="Foto da receita de <?= htmlspecialchars($receita['titulo']); ?>" style="width:100px; height:auto;">
                        <p>Rendimento: <?= $receita['qtde_porcoes'] . ' ' . $receita['tipo_porcao']; ?></p>
                        <p>Tempo de preparo: <?= $receita['tempo_preparo']; ?></p>
                        <p class="descricao">Descrição: <?= $receita['descricao']; ?></p>
                        <p>Modo de preparo: <?= $receita['modo_preparo']; ?></p>
                        <p>Dificuldade: <?= $receita['dificuldade']; ?></p>
                        <p>Ingredientes: <?= $receita['ingredientes']; ?></p>
                        <p>Categorias: <?= $receita['categorias']; ?></p>
                        <!-- Botões de exclusão e alteração, mostrados apenas se o usuário está logado e é o autor da receita -->
                        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $receita['fk_id_usuario']) : ?>
                            <form method="post">
                                <input type="hidden" name="id_receita_excluir" value="<?= $receita['id_receita']; ?>">
                                <button type="submit" name="excluir_receita">Excluir Receita</button>
                            </form>
                            <form method="get" action="postar_receita.php">
                                <input type="hidden" name="id_receita" value="<?= htmlspecialchars($receita['id_receita']); ?>">
                                <button type="submit">Alterar Receita</button>
                            </form>
                        <?php else: ?>
                            <!-- Botão de denunciar receita apenas para usuários logados e que não são o dono da receita -->
                            <?php if (isset($_SESSION['usuario_id'])) : ?>
                                <form action="../processos/denunciar_receita.php" method="post" onsubmit="return validarMotivoDenuncia();">
                                    <input type="hidden" name="id_receita" value="<?= $receita['id_receita']; ?>">
                                    <button type="button" class="btn-denunciar" onclick="denunciarReceita(<?= $receita['id_receita']; ?>);">Denunciar Receita</button>
                                </form>
                        <?php endif; ?> 
                    <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p>Nenhuma receita encontrada.</p>
        <?php endif; ?>
    </main>
</body>
</html>

<!-- Modal para denunciar receita -->
<div id="modalDenuncia" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Denunciar Receita</h2>
        </div>
        <div class="modal-body">
        <form action="../processos/denunciar_receita.php" method="post" onsubmit="return validarMotivoDenuncia();">
                <input type="hidden" name="id_receita" id="idReceitaDenuncia" value="">
                <label for="motivoDenuncia">Motivo da Denúncia:</label>
                <textarea id="motivoDenuncia" name="motivo" required></textarea>
                <div class="modal-footer">
                    <button type="submit" name="denunciar" style="background-color:#4CAF50; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Enviar Denúncia</button>
                    <button type="button" onclick="document.getElementById('modalDenuncia').style.display='none'" style="background-color:red; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleDropdown() {
        var dropdownContent = document.getElementById("dropdown-content");
        var dropbtn = document.getElementById("dropbtn");
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
            dropbtn.classList.remove("active");
        } else {
            dropdownContent.style.display = "block";
            dropbtn.classList.add("active");
            // Ajusta a largura do conteúdo dropdown para ser igual à largura do botão
            dropdownContent.style.width = dropbtn.offsetWidth + "px";
        }
    }

    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn') && !event.target.matches('.dropdown-content') && !event.target.matches('.dropdown-content *')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var dropbtn = document.getElementById("dropbtn");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.style.display === "block") {
                    openDropdown.style.display = "none";
                    dropbtn.classList.remove("active");
                }
            }
        }
    }

    function denunciarReceita(idReceita) {
        document.getElementById('idReceitaDenuncia').value = idReceita;
        document.getElementById('modalDenuncia').style.display = 'block';
    }

    function validarMotivoDenuncia() {
    var motivo = document.getElementById('motivoDenuncia').value.trim();

    // Verificar se há pelo menos uma letra no motivo e se o motivo contém pelo menos 10 letras
    var letras = motivo.match(/[a-zA-Z]/g);

    if (!letras || letras.length < 10) {
        alert('O motivo da denúncia deve conter no mínimo 10 caracteres.');
        return false;
    }

    return true;
    }
</script>
