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

        if ($usuario['is_admin'] == 1) {
            header('Location: ../paginas/home_admin.php');
        } else {
            header('Location: ../paginas/home_usuario.php');
        }
        
    } else {
        $mensagem = "E-mail ou senha inválidos!";
        header("Location: ../paginas/login.php?mensagem=" . urlencode($mensagem));
    }
} catch(PDOException $e) {
    echo "Erro ao realizar login: " . $e->getMessage();
}
?>
