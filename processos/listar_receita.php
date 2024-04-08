<?php

session_start();

include_once '../processos/inicializar_banco.php';

// Função para excluir a receita
function excluir_receita($id_receita) {
    global $pdo;

    try {
        // Verificar se o usuário logado é o proprietário da receita
        $id_usuario_logado = $_SESSION['usuario_id'];
        $sql_check_owner = "SELECT fk_id_usuario FROM Receita WHERE id_receita = :id_receita";
        $stmt_check_owner = $pdo->prepare($sql_check_owner);
        $stmt_check_owner->bindParam(':id_receita', $id_receita);
        $stmt_check_owner->execute();
        $result_check_owner = $stmt_check_owner->fetch(PDO::FETCH_ASSOC);

        if (!$result_check_owner || $result_check_owner['fk_id_usuario'] != $id_usuario_logado) {
            return false; // Não é o proprietário da receita
        }

        // Excluir registros da tabela Receita_Ingrediente
        $sql_delete_ingrediente = "DELETE FROM Receita_Ingrediente WHERE id_receita = :id_receita";
        $stmt_ingrediente = $pdo->prepare($sql_delete_ingrediente);
        $stmt_ingrediente->bindParam(':id_receita', $id_receita);
        $stmt_ingrediente->execute();

        // Excluir registros da tabela Receita_Categoria
        $sql_delete_categoria = "DELETE FROM Receita_Categoria WHERE id_receita = :id_receita";
        $stmt_categoria = $pdo->prepare($sql_delete_categoria);
        $stmt_categoria->bindParam(':id_receita', $id_receita);
        $stmt_categoria->execute();

        // Excluir a receita da tabela Receita
        $sql_delete_receita = "DELETE FROM Receita WHERE id_receita = :id_receita";
        $stmt_receita = $pdo->prepare($sql_delete_receita);
        $stmt_receita->bindParam(':id_receita', $id_receita);
        $stmt_receita->execute();

        // Verificar se a exclusão foi bem-sucedida
        return $stmt_receita->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Verificar se o formulário de exclusão foi submetido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['excluir_receita'])) {
    $id_receita_excluir = $_POST['id_receita_excluir'];

    if (!empty($id_receita_excluir)) {
        $exclusao_sucesso = excluir_receita($id_receita_excluir);
        if ($exclusao_sucesso) {
            echo "<script>alert('Receita excluída com sucesso!');</script>";
            // Recarregar a página após a exclusão
            echo "<script>window.location.href = 'nome_da_pagina.php';</script>";
            exit;
        } else {
            echo "<script>alert('Erro ao excluir a receita. Por favor, tente novamente.');</script>";
        }
    }
}

// Consulta SQL para obter todas as receitas com seus ingredientes, filtros e usuário associados
$sql = "SELECT r.*, 
                GROUP_CONCAT(DISTINCT i.ingrediente SEPARATOR ', ') AS ingredientes, 
                GROUP_CONCAT(DISTINCT c.categoria SEPARATOR ', ') AS categorias,
                u.nome AS nome_usuario
        FROM Receita r
        LEFT JOIN Receita_Ingrediente ri ON r.id_receita = ri.id_receita
        LEFT JOIN Ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        LEFT JOIN Receita_Categoria rc ON r.id_receita = rc.id_receita
        LEFT JOIN Categoria c ON rc.id_categoria = c.id_categoria
        LEFT JOIN usuarios u ON r.fk_id_usuario = u.id
        GROUP BY r.id_receita"; // Agrupar para evitar duplicatas de receitas
$stmt = $pdo->query($sql);
$receitas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Receitas</title>
    <link rel="stylesheet" href="../css/style_listar.css">
</head>
<body>
    <h1>Receitas</h1>

    <!-- Exibir as receitas -->
    <?php if (!empty($receitas)) : ?>
        <ul>
            <?php foreach ($receitas as $receita) : ?>
                <li>
                    <h3><?php echo $receita['titulo']; ?></h3>
                    <p>Postado por: <?php echo $receita['nome_usuario']; ?></p>
                    <img src="<?php echo $receita['foto']; ?>" alt="<?php echo $receita['titulo']; ?>">
                    <p>Rendimento: <?php echo $receita['qtde_porcoes'] . ' ' . $receita['tipo_porcao']; ?></p>
                    <p>Tempo de preparo: <?php echo $receita['tempo_preparo']; ?></p>
                    <p>Descrição: <?php echo $receita['descricao']; ?></p>
                    <p>Modo de preparo: <?php echo $receita['modo_preparo']; ?></p>
                    <p>Dificuldade: <?php echo $receita['dificuldade']; ?></p>
                    <p>Ingredientes: <?php echo $receita['ingredientes']; ?></p>
                    <p>Filtros: <?php echo $receita['categorias']; ?></p>
                    <!-- Botão de exclusão -->
                    <?php if ($_SESSION['usuario_id'] == $receita['fk_id_usuario']) : ?>
                        <form method="post">
                            <input type="hidden" name="id_receita_excluir" value="<?php echo $receita['id_receita']; ?>">
                            <button type="submit" name="excluir_receita">Excluir Receita</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Nenhuma receita encontrada.</p>
    <?php endif; ?>
</body>
</html>
