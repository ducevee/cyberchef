<?php
session_start();
include_once '../processos/inicializar_banco.php';

if (isset($_GET['id_avaliacao'])) {
    $id_avaliacao = $_GET['id_avaliacao'];
    
    
    if (isset($_SESSION['usuario_id'])) {
        $id_usuario = $_SESSION['usuario_id'];
        //pega na tabela o id da avaliação e verifica se o id usuario condiz com quem quer apagar 
        $query_verificar = "SELECT fk_id_usuario FROM avaliacao WHERE id_avaliacao = :id_avaliacao";
        $stmt_verificar = $pdo->prepare($query_verificar);
        $stmt_verificar->bindParam(':id_avaliacao', $id_avaliacao, PDO::PARAM_INT);
        $stmt_verificar->execute();
        
        if ($stmt_verificar->rowCount() > 0) {
            $resultado = $stmt_verificar->fetch(PDO::FETCH_ASSOC);
            if ($resultado['fk_id_usuario'] == $id_usuario) {
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
?>
<script>
    window.location.href = '../paginas/receita.php';
</script>
