<?php
include '../inicializar_banco.php';

$email = $_POST['email'];

try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuarioExistente = $stmt->fetch();

    if ($usuarioExistente) {
        echo "usuario_existe";
    } else {
        echo "usuario_nao_existe";
    }
} catch(PDOException $e) {
    echo "erro_ao_verificar_usuario";
}
?>
