<?php
session_start();
include '../processos/inicializar_banco.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../paginas/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id_denuncia = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM Denuncia WHERE id_denuncia = :id_denuncia");
    $stmt->bindParam(':id_denuncia', $id_denuncia);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['mensagem'] = "Denúncia excluída com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao excluir denúncia.";
    }
}

header('Location: ../paginas/gerenciar_denuncia.php');
exit;
?>
