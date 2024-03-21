<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

include '../processos/inicializar_banco.php';

// Prepara uma consulta SQL para buscar usuários
$stmt = $pdo->query("SELECT id, nome, email, data_criacao FROM usuarios");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página Administrativa</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="admin-home-page">
    <h2>Usuários Cadastrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Data de Criação</th> 
            <th>Ações</th>
        </tr>
        <?php while ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo htmlspecialchars($usuario['data_criacao']); ?></td> 
                <td>
                    <a href="../processos/db/editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="editar-btn">Editar</a>
                    <a href="../processos/db/deletar_usuario.php?id=<?php echo $usuario['id']; ?>" class="excluir-btn" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <a href="../processos/logout.php" class="logout-btn">Sair</a>
</body>
</html>
