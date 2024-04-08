<?php
include '../inicializar_banco.php';

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

try {
    // Verifica se o usuário já existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuarioExistente = $stmt->fetch();

    if ($usuarioExistente) {
        // Se o usuário já existe, retorna uma mensagem de erro.
        echo "Erro: o usuário com este email já existe.";
    } else {
        // Se o usuário não existe, insere o novo usuário no banco de dados.
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
        $stmt= $pdo->prepare($sql);
        $stmt->execute([$nome, $email, $senha]);
        echo "Usuário cadastrado com sucesso!";
        
    }
} catch(PDOException $e) {
    echo "Erro ao cadastrar usuário: " . $e->getMessage();
}
?>
