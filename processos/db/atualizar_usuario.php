<?php
session_start();
require_once '../inicializar_banco.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['id'];
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);

    try {
        // Verificar se existe outro usuário com o mesmo email
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email AND id != :id");
        $stmt->execute(['email' => $email, 'id' => $userId]);
        if ($stmt->fetch()) {
            header('Location: editar_usuario.php?id=' . $userId . '&mensagem=' . urlencode('Erro: O e-mail já está cadastrado para outro usuário.'));
            exit;
        }

        // Verificar se existe outro usuário com o mesmo nome
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE nome = :nome AND id != :id");
        $stmt->execute(['nome' => $nome, 'id' => $userId]);
        if ($stmt->fetch()) {
            header('Location: editar_usuario.php?id=' . $userId . '&mensagem=' . urlencode('Erro: O nome já está cadastrado para outro usuário.'));
            exit;
        }

        // Se não houver conflito de nome ou email, atualize o usuário
        $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nome' => $nome, 'email' => $email, 'id' => $userId]);

        // Redirecionamento para a página de configurações se a atualização for bem sucedida
        header('Location: ../../paginas/home_admin.php');
        exit;
    } catch (PDOException $e) {
        header('Location: editar_usuario.php?id=' . $userId . '&mensagem=' . urlencode('Erro ao atualizar o usuário: ' . $e->getMessage()));
        exit;
    }
} else {
    // Se a requisição não for POST, também redireciona para a página de edição com mensagem de erro
    header('Location: editar_usuario.php?id=' . $userId . '&mensagem=' . urlencode('Requisição inválida.'));
    exit;
}
?>
