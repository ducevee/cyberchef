<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

include '../processos/inicializar_banco.php';

// Prepara uma consulta SQL para buscar usuários
$stmt = $pdo->query("SELECT id, nome, email, data_criacao FROM usuarios");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Administrativa</title>
    <link rel="stylesheet" href="../css/style_home.css"> <!-- Aponte para o CSS comum -->
</head>
<body class="admin-home-page">
    <header>
        <nav class="navHeader">
            <a href="index.html" id="link-logo" title="Página inicial">
            <img src="../css/img/cyber_chef_logo.png" alt="logo" id="logo">
            </a>
            <div class="search-container">
            <!-- Omitir funcionalidades de pesquisa se não forem relevantes -->
            </div>
            <ul id="lista">
                <!-- Adaptar links para funcionalidades do admin -->
                <li>
                <a class="linksHeader" href="./gerenciar_denuncia.php">GERENCIAR DENÚNCIAS</a>
                </li>
                <li>
                <a class="linksHeader" href="home_admin.php">GERENCIAR USUÁRIOS</a>
                </li>
            </ul>
            <div class="user">Bem-vindo, Admin!</div>
            <a href="../processos/logout.php" alt="Sair" title="Sair">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#FFF" version="1.1" id="Capa_1" width="25px" height="25px" viewBox="0 0 492.5 492.5" xml:space="preserve">
                    <g>
                        <path d="M184.646,0v21.72H99.704v433.358h31.403V53.123h53.539V492.5l208.15-37.422v-61.235V37.5L184.646,0z M222.938,263.129   c-6.997,0-12.67-7.381-12.67-16.486c0-9.104,5.673-16.485,12.67-16.485s12.67,7.381,12.67,16.485   C235.608,255.748,229.935,263.129,222.938,263.129z"/>
                    </g>
                </svg>
            </a>
        </nav>
    </header>
    <main class="main-home">
        <h1>ADMINISTRAÇÃO DO CYBER CHEF</h1>
        <section class="user-list">
            <h2>Usuários Cadastrados</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Data de Criação</th> 
                    <th>Ações</th>
                </tr>
                <?php while ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['data_criacao']); ?></td> 
                    <td>
                        <a href="../processos/db/editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="editar-btn">Editar</a>
                        <a href="../processos/db/deletar_usuario.php?id=<?php echo $usuario['id']; ?>" class="excluir-btn" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        </section>
    </main>
</body>
</html>
