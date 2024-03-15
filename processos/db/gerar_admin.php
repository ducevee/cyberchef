<?php

include '../inicializar_banco.php'; // Assegure-se de que este caminho está correto

$emailAdmin = "admin@admin.com"; // Use o e-mail do usuário administrador
$senha = "admin"; // A senha desejada
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
