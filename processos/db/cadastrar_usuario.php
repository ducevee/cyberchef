<?php
include '../inicializar_banco.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

try {
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt= $pdo->prepare($sql);
    $stmt->execute([$nome, $email, $senha]);
    echo "Usuário cadastrado com sucesso!";
} catch(PDOException $e) {
    echo "Erro ao cadastrar usuário: " . $e->getMessage();
}
?>
