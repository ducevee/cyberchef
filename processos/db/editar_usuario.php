<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);


if ($_SESSION['is_admin'] != 1 && isset($_SESSION['loggedin'])) {
    header('Location: ../../paginas/home.php');
    exit;
}


require_once '../inicializar_banco.php';

// Verifica se o ID do usuário foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    exit('ID de usuário não fornecido.');
}

$userId = $_GET['id'];

try {
    $sql = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        exit('Usuário não encontrado.');
    }
} catch (PDOException $e) {
    exit("Erro ao buscar usuário: " . $e->getMessage());
}


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../../css/style.css"> 
</head>
<body>
    <h2>Editar Usuário</h2>
    <form action="./atualizar_usuario.php" method="post"> 
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
        Nome: <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>"><br>
        E-mail: <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>"><br>
        <input type="submit" value="Atualizar">
    </form>
</body>
</html>
