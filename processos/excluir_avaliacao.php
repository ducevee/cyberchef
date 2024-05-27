<?php
session_start();
include_once '../processos/inicializar_banco.php';
if (isset($_POST['id_avaliacao']) && isset($_POST['fk_receita'])) {
    $id_avaliacao = $_POST['id_avaliacao'];
    $id_receita = $_POST['fk_receita'];
    
    if (isset($_SESSION['usuario_id'])) {
        $id_usuario = $_SESSION['usuario_id'];
        
        // Verificar se o usuário tem permissão para excluir a avaliação
        $query_verificar = "SELECT fk_id_usuario FROM avaliacao WHERE id_avaliacao = :id_avaliacao";
        $stmt_verificar = $pdo->prepare($query_verificar);
        $stmt_verificar->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
        $stmt_verificar->execute();
        
        if ($stmt_verificar->rowCount() > 0) {
            $resultado = $stmt_verificar->fetch(PDO::FETCH_ASSOC);
            
            if ($resultado['fk_id_usuario'] == $id_usuario) {
                // Excluir a avaliação
                $query_excluir = "DELETE FROM avaliacao WHERE id_avaliacao = :id_avaliacao";
                $stmt_excluir = $pdo->prepare($query_excluir);
                $stmt_excluir->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
                
                if ($stmt_excluir->execute()) {
                    echo "<script>alert('Avaliação excluída com sucesso.');</script>";
                } else {
                    echo "<script>alert('Erro ao excluir avaliação.');</script>";
                }
            } else {
                echo "<script>alert('Você não tem permissão para excluir esta avaliação.');</script>";
            }
        } else {
            echo "<script>alert('Avaliação não encontrada.');</script>";
        }
    } else {
        echo "<script>alert('Você precisa estar logado para excluir uma avaliação.');</script>";
    }
}

// Redirecionar de volta para a página de visualização da receita após o processamento
if (isset($id_receita)) {
    echo "<script>window.location.href = '../paginas/visualizar_receita.php?id=$id_receita';</script>";
} else {
    echo "<script>window.location.href = '../paginas/listar_receita.php';</script>";
}

?>