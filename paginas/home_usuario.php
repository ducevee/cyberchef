<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Aqui não é necessário verificar se é admin, pois essa página é para usuários comuns

?>

<!DOCTYPE html>
<html lang="PT-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login de Usuário</title>
        <link rel="stylesheet" href="../css/style_home.css">
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
                <div class="user">Bem-vindo, <b> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?><b>!</div>
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
            <h1>BEM-VINDO AO CYBER CHEF!</h1>
            <section class="btn-home">
                <a href="postar_receita.php">
                    POSTE
                </a>
                <a href="postar_receita.php">
                    COMPARTILHE
                </a>
                <a href="postar_receita.php">
                    AVALIE
                </a>
            </section>
            <section class="receita-home">
                <div>
                    <div>
                        Bombom de uva na travessa
                        <img src="../css/img/img_exemple.png" alt="exemple">
                    </div>
                    <div>
                        ⭐⭐⭐⭐⭐
                    </div>
                    Nome
                </div>
                <div>
                    <div>
                        Bombom de uva na travessa
                        <img src="../css/img/img_exemple.png" alt="exemple">
                    </div>
                    <div>
                        ⭐⭐⭐⭐⭐
                    </div>
                    Nome
                </div>
                <div>
                    <div>
                        Bombom de uva na travessa
                        <img src="../css/img/img_exemple.png" alt="exemple">
                    </div>
                    <div>
                        ⭐⭐⭐⭐⭐
                    </div>
                    Nome
                </div>
            </section>
        </main>
    </body>
</html>