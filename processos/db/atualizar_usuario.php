<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SESSION['is_admin'] != 1 || !isset($_SESSION['loggedin'])) {
    header('Location: ../../paginas/home.php');
    exit;
}

require_once '../inicializar_banco.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id'], $_POST['nome'], $_POST['email']) && !empty($_POST['id']) && !empty($_POST['nome']) && !empty($_POST['email'])) {
        $userId = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];

        try {
            $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['nome' => $nome, 'email' => $email, 'id' => $userId]);

            // Redirecionar para a home do admin após a atualização
            header('Location: ../../paginas/home_admin.php');
            exit;
        } catch (PDOException $e) {
            exit("Erro ao atualizar o usuário: " . $e->getMessage());
        }
    } else {
        exit('Dados incompletos.');
    }
} else {
    // Se não for POST, redirecionar de volta para a página de edição
    header('Location: editar_usuario.php?id=' . $_POST['id']);
    exit;
}
?>
