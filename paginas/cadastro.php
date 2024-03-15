<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="form-container">
    <form action="../processos/cadastrar_usuario.php" method="post">
        <h2>Cadastro</h2>
        Nome: <input type="text" name="nome" required><br>
        E-mail: <input type="email" name="email" required><br>
        Senha: <input type="password" name="senha" required><br>
        <input type="submit" value="Cadastrar">
        <div class="link-login">
            Já tem conta? <a href="login.php">Acesse aqui</a>
        </div>
    </form>
</div>

</body>
</html>
