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
    <header>
                <nav class="navHeader">
                    <a href="<?php echo (isset($_SESSION['usuario_id']) && $_SESSION['is_admin'] == 1) ? 'home_admin.php' : 'home_usuario.php'; ?>" id="link-logo" title="Página inicial">
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
            <a href="../paginas/listar_receita.php" style="font-family: 'Maven Pro', sans-serif; font-size: 50px; margin-left: 20px;">
    &larr; 
</a>
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