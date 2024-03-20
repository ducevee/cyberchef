<?php

include '../inicializar_banco.php';

$emailAdmin = "admin@admin.com"; 
$senha = "admin"; 
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $sql = "UPDATE usuarios SET senha = ? WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$senhaHash, $emailAdmin]);

    echo "Senha atualizada com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao atualizar senha: " . $e->getMessage();
}

?>
