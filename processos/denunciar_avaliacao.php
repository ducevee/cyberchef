<?php
session_start();
include 'inicializar_banco.php';

if (isset($_POST['denunciar'])) {
    $id_avaliacao = $_POST['id_avaliacao'];
    $id_usuario = $_SESSION['usuario_id'];
    $motivo = $_POST['motivo']; // Capturar o motivo da denúncia

    $sql = "INSERT INTO Denuncia (fk_Avaliacao_id_avaliacao, fk_id_usuario, data_denuncia, motivo) VALUES (:id_avaliacao, :id_usuario, NOW(), :motivo)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_avaliacao', $id_avaliacao);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':motivo', $motivo);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Avaliação denunciada com sucesso!'); window.location.href = '../paginas/visualizar_receita.php';</script>";
    } else {
        echo "<script>alert('Erro ao denunciar avaliação.'); window.history.back();</script>";
    }
}
?>
