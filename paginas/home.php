<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}


// Verifica se o usuário é administrador
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

include '../processos/inicializar_banco.php';

if ($is_admin) {
    // Prepara uma consulta SQL para buscar usuários apenas se for admin
    $stmt = $pdo->query("SELECT id, nome, email FROM usuarios");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Página Inicial</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="<?php echo $is_admin ? 'admin-home-page' : 'user-home-page'; ?>"> <!-- verifica se é o usuário admin, caso não seja ele irá para a página de usuário comum -->
    <?php if ($is_admin): ?>
        <!-- Conteúdo para usuários administradores -->
        <h2>Usuários Cadastrados</h2> 
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Ações</th>
            </tr>
            <?php while ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td>
                        <a href="../processos/editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="editar-btn">Editar</a>
                        <a href="../processos/deletar_usuario.php?id=<?php echo $usuario['id']; ?>" class="excluir-btn" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <!-- Conteúdo para usuários comuns -->
        <h2>Bem-vindo à sua Página Inicial</h2>
        <!-- Você pode adicionar mais conteúdo aqui -->
    <?php endif; ?>
    <a href="../processos/logout.php" class="logout-btn">Sair</a>
</body>
</html>
