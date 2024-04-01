<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Aqui não é necessário verificar se é admin, pois essa página é para usuários comuns

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página do Usuário</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="user-home-page">
    <h2>Bem-vindo à sua Página Inicial</h2>
    <!-- Conteúdo para usuários comuns -->
    <!-- Você pode adicionar mais conteúdo aqui -->
    <a href="../paginas/receita.php">CLIQUE AQUI</a>
    <a href="../processos/logout.php" class="logout-btn">Sair</a>
</body>
</html>
