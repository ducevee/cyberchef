<?php
session_start();
include 'inicializar_banco.php';

if (isset($_POST['denunciar'])) {
    $id_receita = $_POST['id_receita'];
    $id_usuario = $_SESSION['usuario_id'];
    $motivo = $_POST['motivo']; // Capturar o motivo da denúncia

    $sql = "INSERT INTO Denuncia (fk_id_receita, fk_id_usuario, data_denuncia, motivo) VALUES (:id_receita, :id_usuario, NOW(), :motivo)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_receita', $id_receita);
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':motivo', $motivo);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Receita denunciada com sucesso!'); window.location.href = '../paginas/listar_receita.php';</script>";
    } else {
        echo "<script>alert('Erro ao denunciar receita.'); window.history.back();</script>";
    }
}
?>
