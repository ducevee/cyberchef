<?php
session_start();
include '../processos/inicializar_banco.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: ../paginas/login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id_receita = $_GET['id'];
    $pdo->beginTransaction();

    try {
        // Excluir denúncias associadas a avaliações da receita
        $stmt = $pdo->prepare("DELETE FROM Denuncia WHERE fk_Avaliacao_id_avaliacao IN (SELECT id_avaliacao FROM Avaliacao WHERE fk_receita = :id_receita)");
        $stmt->bindParam(':id_receita', $id_receita);
        $stmt->execute();

        // Excluir avaliações associadas à receita
        $stmt = $pdo->prepare("DELETE FROM Avaliacao WHERE fk_receita = :id_receita");
        $stmt->bindParam(':id_receita', $id_receita);
        $stmt->execute();

        // Excluir denúncias diretamente associadas à receita
        $stmt = $pdo->prepare("DELETE FROM Denuncia WHERE fk_id_receita = :id_receita");
        $stmt->bindParam(':id_receita', $id_receita);
        $stmt->execute();

        // Excluir relação receita-ingrediente
        $stmt = $pdo->prepare("DELETE FROM Receita_Ingrediente WHERE id_receita = :id_receita");
        $stmt->bindParam(':id_receita', $id_receita);
        $stmt->execute();

        // Excluir relação receita-categoria
        $stmt = $pdo->prepare("DELETE FROM Receita_Categoria WHERE id_receita = :id_receita");
        $stmt->bindParam(':id_receita', $id_receita);
        $stmt->execute();

        // Finalmente, excluir a receita
        $stmt = $pdo->prepare("DELETE FROM Receita WHERE id_receita = :id_receita");
        $stmt->bindParam(':id_receita', $id_receita);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $pdo->commit();
            $_SESSION['mensagem'] = "Receita e dependências excluídas com sucesso!";
        } else {
            $pdo->rollback();
            $_SESSION['mensagem'] = "Erro ao excluir receita: Nenhuma receita encontrada.";
        }
    } catch (Exception $e) {
        $pdo->rollback();
        $_SESSION['mensagem'] = "Erro ao excluir receita: " . $e->getMessage();
    }
} else {
    $_SESSION['mensagem'] = "ID da receita não especificado.";
}

header('Location: ../paginas/gerenciar_denuncia.php');
exit;
?>
