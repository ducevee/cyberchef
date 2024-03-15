<!DOCTYPE html>
<html>
<head>
    <title>Login de Usuário</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php
session_start();
if(isset($_SESSION['erro_login'])): ?>
<div class="error-message">
    <?= $_SESSION['erro_login']; ?>
</div>
<?php 
unset($_SESSION['erro_login']); // Limpa a mensagem de erro após exibição
endif; 
?>

<div class="form-container">
    <form action="../processos/verificar_login.php" method="post">
        <h2>Login</h2>
        E-mail: <input type="email" name="email" required><br>
        Senha: <input type="password" name="senha" required><br>
        <input type="submit" value="Entrar">
        <div class="link-cadastro">
            Não tem conta ainda? <a href="cadastro.php">Cadastre-se</a>
        </div>
    </form>
</div>

</body>
</html>
