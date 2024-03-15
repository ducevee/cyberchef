<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['is_admin'] !== 1) {
    header('Location: ../../paginas/login.php');
    exit;
}

require_once '../processos/inicializar_banco.php'; // Ajuste o caminho conforme necessário

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
    <link rel="stylesheet" href="../../css/style.css"> <!-- Ajuste o caminho conforme necessário -->
</head>
<body>
    <h2>Editar Usuário</h2>
    <form action="atualizar_usuario.php" method="post"> <!-- Crie e ajuste o caminho para o script de atualização -->
        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
        Nome: <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>"><br>
        E-mail: <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>"><br>
        <!-- Não inclua a senha aqui a menos que queira permitir que ela seja alterada -->
        <input type="submit" value="Atualizar">
    </form>
</body>
</html>
