<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SESSION['is_admin'] != 1 || !isset($_SESSION['loggedin'])) {
    header('Location: ../../paginas/home.php');
    exit;
}

require_once '../inicializar_banco.php';

// Verifica se o ID do usuário foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../../paginas/home.php?mensagem=' . urlencode('ID de usuário não fornecido.'));
    exit;
}

$userId = $_GET['id'];

try {
    $sql = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header('Location: ../../paginas/home.php?mensagem=' . urlencode('Usuário não encontrado.'));
        exit;
    }
} catch (PDOException $e) {
    header('Location: ../../paginas/home.php?mensagem=' . urlencode("Erro ao buscar usuário: " . $e->getMessage()));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Editar Usuário</h2>
        <?php
        // Verificar se existe mensagem via GET para exibir como alert
        if (isset($_GET['mensagem'])) {
            $mensagem = $_GET['mensagem'];
            // Usando htmlspecialchars para prevenir Cross-Site Scripting (XSS)
            echo "<script>alert('" . htmlspecialchars(urldecode($mensagem)) . "');</script>";
        }
        ?>
        <form action="./atualizar_usuario.php" method="post"> 
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">
            Nome: <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>"><br>
            E-mail: <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>"><br>
            <input type="submit" value="Atualizar">
        </form>
    </div>
</body>
</html>
