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
        <link rel="stylesheet" href="../css/style_cadastro.css">
    </head>
    <body>
        <header>
            <nav class="navHeader">
                <a href="index.html" id="link-logo" title="Página inicial">
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
                    <a class="linksHeader" href="../processos/listar_receita.php">NOVIDADES</li></a>
                    </li>
                    <li>
                    <a class="linksHeader" href=".">CATEGORIA</a>
                    </li>
                </ul>
            </nav>
        </header>
        <main class="main-home">
            <h1>BEM-VINDO AO CYBER CHEF!</h1>
            <section class="btn-home">
                <a href="postarreceita.php">
                    POSTE
                </a>
                <a href="postarreceita.php">
                    COMPARTILHE
                </a>
                <a href="postarreceita.php">
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
             <a href="../processos/logout.php" class="logout-btn">Sair</a> 
        </main>
    </body>
</html>