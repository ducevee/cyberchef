<?php
session_start(); // Inicia uma nova sessão ou resume uma sessão existente
include './inicializar_banco.php';

$email = $_POST['email'];
$senha = $_POST['senha'];

try {
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Armazenar dados do usuário na sessão
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['loggedin'] = true;
        $_SESSION['is_admin'] = $usuario['is_admin']; // Armazenar se o usuário é admin



        // Redirecionar para a página home
        header("Location: ../paginas/home.php");
    } else {
        echo "E-mail ou senha inválidos!";
    }
} catch(PDOException $e) {
    echo "Erro ao realizar login: " . $e->getMessage();
}
?>
