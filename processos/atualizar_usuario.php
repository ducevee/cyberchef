<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['is_admin'] !== 1) {
    header('Location: ../paginas/login.php');
    exit;
}

require_once '../conexao.php'; // Ajuste o caminho conforme necessário

// Verifique se os dados do formulário foram enviados
if (!isset($_POST['id'], $_POST['nome'], $_POST['email'])) {
    exit('Por favor, preencha todos os campos!');
}

// Atualização dos dados do usuário
try {
    $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $_POST['nome'],
        ':email' => $_POST['email'],
        ':id' => $_POST['id']
    ]);

    // Redirecionar de volta para a página de administração
    header('Location: ../paginas/home.php');
} catch (PDOException $e) {
    exit("Erro ao atualizar usuário: " . $e->getMessage());
}
?>
