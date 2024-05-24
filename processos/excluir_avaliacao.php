<?php
session_start();
include '../processos/inicializar_banco.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../paginas/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id_avaliacao = $_GET['id'];
    $pdo->beginTransaction();

    try {
        // Excluir denúncias associadas à avaliação
        $stmt = $pdo->prepare("DELETE FROM Denuncia WHERE fk_Avaliacao_id_avaliacao = :id_avaliacao");
        $stmt->bindParam(':id_avaliacao', $id_avaliacao);
        $stmt->execute();

        // Excluir a avaliação
        $stmt = $pdo->prepare("DELETE FROM Avaliacao WHERE id_avaliacao = :id_avaliacao");
        $stmt->bindParam(':id_avaliacao', $id_avaliacao);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $pdo->commit();
            $_SESSION['mensagem'] = "Avaliação e dependências excluídas com sucesso!";
        } else {
            $pdo->rollback();
            $_SESSION['mensagem'] = "Erro ao excluir avaliação: Nenhuma avaliação encontrada.";
        }
    } catch (Exception $e) {
        $pdo->rollback();
        $_SESSION['mensagem'] = "Erro ao excluir avaliação: " . $e->getMessage();
    }
} else {
    $_SESSION['mensagem'] = "ID da avaliação não especificado.";
}

header('Location: ../paginas/gerenciar_denuncia.php');
exit;
?>
