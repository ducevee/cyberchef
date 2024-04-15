<?php
session_start();

include_once '../processos/inicializar_banco.php';

if(isset($_GET['mensagem'])) {
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

// Consulta SQL para obter todas as receitas com seus ingredientes, filtros e usuário associados
$sql = "SELECT r.*, 
                GROUP_CONCAT(DISTINCT i.ingrediente SEPARATOR ', ') AS ingredientes, 
                GROUP_CONCAT(DISTINCT c.categoria SEPARATOR ', ') AS categorias,
                u.nome AS nome_usuario
        FROM Receita r
        LEFT JOIN Receita_Ingrediente ri ON r.id_receita = ri.id_receita
        LEFT JOIN Ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        LEFT JOIN Receita_Categoria rc ON r.id_receita = rc.id_receita
        LEFT JOIN Categoria c ON rc.id_categoria = c.id_categoria
        LEFT JOIN usuarios u ON r.fk_id_usuario = u.id
        GROUP BY r.id_receita"; // Agrupar para evitar duplicatas de receitas
$stmt = $pdo->query($sql);
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
                <a href="<?php echo ($_SESSION['is_admin'] == 1) ? 'home_admin.php' : 'home_usuario.php'; ?>" id="link-logo" title="Página inicial">
                    <img src="../css/img/cyber_chef_logo.png" alt="logo" id="logo">
                </a>
                <div class="search-container">
                <input type="search" class="search-input" placeholder="Busque por uma receita, Chef ou Categoria.">
                <button class="search-button">
                    <svg width="19" height="21" viewBox="0 0 28 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20.0114 15.7233H18.7467L18.2985 15.3373C19.8674 13.7078 20.8119 11.5923 20.8119 9.29102C20.8119 4.15952 16.1532 0 10.4059 0C4.65866 0 0 4.15952 0 9.29102C0 14.4225 4.65866 18.582 10.4059 18.582C12.9834 18.582 15.3528 17.7387 17.1778 16.3379L17.6101 16.7381V17.8674L25.6146 25L28 22.8702L20.0114 15.7233ZM10.4059 15.7233C6.41967 15.7233 3.20183 12.8502 3.20183 9.29102C3.20183 5.73185 6.41967 2.85878 10.4059 2.85878C14.3922 2.85878 17.6101 5.73185 17.6101 9.29102C17.6101 12.8502 14.3922 15.7233 10.4059 15.7233Z" fill="white"/>
                    </svg>                  
                </button>
                </div>
                <ul id="lista">
                    <li>
                    <a class="linksHeader" href=".">EM ALTA</a>
                    </li>
                    <li>
                    <a class="linksHeader" href="../paginas/listar_receita.php">NOVIDADES</li></a>
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
    <main>
        <!-- Exibir as receitas -->
        <?php if (!empty($receitas)) : ?>
            <ul>
                <?php foreach ($receitas as $receita) : ?>
                    <li>
                        <h3><?php echo $receita['titulo']; ?></h3>
                        <p>Postado por: <?php echo $receita['nome_usuario']; ?></p>
                        <img src="../uploads/<?php echo htmlspecialchars($receita['foto']); ?>" alt="Foto da receita de <?php echo htmlspecialchars($receita['titulo']); ?>">
                        <p>Rendimento: <?php echo $receita['qtde_porcoes'] . ' ' . $receita['tipo_porcao']; ?></p>
                        <p>Tempo de preparo: <?php echo $receita['tempo_preparo']; ?></p>
                        <p>Descrição: <?php echo $receita['descricao']; ?></p>
                        <p>Modo de preparo: <?php echo $receita['modo_preparo']; ?></p>
                        <p>Dificuldade: <?php echo $receita['dificuldade']; ?></p>
                        <p>Ingredientes: <?php echo $receita['ingredientes']; ?></p>
                        <p>Filtros: <?php echo $receita['categorias']; ?></p>
                        <!-- Botões de exclusão e alteração -->
                        <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $receita['fk_id_usuario']) : ?>
                            <form method="post">
                                <input type="hidden" name="id_receita_excluir" value="<?php echo $receita['id_receita']; ?>">
                                <button type="submit" name="excluir_receita">Excluir Receita</button>
                            </form>
                            <form method="get" action="postar_receita.php">
                                <input type="hidden" name="id_receita" value="<?php echo htmlspecialchars($receita['id_receita']); ?>">
                                <button type="submit">Alterar Receita</button>
                            </form>
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
