<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../../paginas/login.php');
    exit;
}

include '../inicializar_banco.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);

        header('Location: ../../paginas/home_admin.php');
    } catch (Exception $e) {
        exit('Não foi possível excluir o usuário: ' . $e->getMessage());
    }
}
?>
