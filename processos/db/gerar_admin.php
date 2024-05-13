<?php

include '../inicializar_banco.php';

$emailAdmin = "admin@admin.com";
$nomeAdmin = "Administrador"; // Nome do usuário admin
$senha = "admin";
$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

try {
    // Tentar inserir o usuário admin
    $sql = "INSERT INTO usuarios (nome, email, senha, is_admin) VALUES (?, ?, ?, 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nomeAdmin, $emailAdmin, $senhaHash]);

    echo "Usuário admin criado com sucesso!";
} catch (PDOException $e) {
    // Se um erro de duplicidade (1062) acontecer, significa que o usuário já existe.
    if ($e->getCode() == 23000) {
        echo "Usuário admin já existe.";
    } else {
        echo "Erro ao criar usuário admin: " . $e->getMessage();
    }
}

?>
