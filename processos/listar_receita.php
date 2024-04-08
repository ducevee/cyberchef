<?php

include_once '../processos/inicializar_banco.php';


$sql = "SELECT r.*, GROUP_CONCAT(DISTINCT i.ingrediente SEPARATOR ', ') AS ingredientes, 
                GROUP_CONCAT(DISTINCT c.categoria SEPARATOR ', ') AS categorias
        FROM Receita r
        LEFT JOIN Receita_Ingrediente ri ON r.id_receita = ri.id_receita
        LEFT JOIN Ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        LEFT JOIN Receita_Categoria rc ON r.id_receita = rc.id_receita
        LEFT JOIN Categoria c ON rc.id_categoria = c.id_categoria
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
</head>
<body>
    <h1>Receitas</h1>

   
    <?php if (!empty($receitas)) : ?>
        <ul>
            <?php foreach ($receitas as $receita) : ?>
                <li>
                    <h3><?php echo $receita['titulo']; ?></h3>
                    <img src="<?php echo $receita['foto']; ?>" alt="<?php echo $receita['titulo']; ?>">
                    <p>Rendimento: <?php echo $receita['qtde_porcoes'] . ' ' . $receita['tipo_porcao']; ?></p>
                    <p>Tempo de preparo: <?php echo $receita['tempo_preparo']; ?></p>
                    <p>Descrição: <?php echo $receita['descricao']; ?></p>
                    <p>Modo de preparo: <?php echo $receita['modo_preparo']; ?></p>
                    <p>Dificuldade: <?php echo $receita['dificuldade']; ?></p>
                    <p>Ingredientes: <?php echo $receita['ingredientes']; ?></p>
                    <p>Filtros: <?php echo $receita['categorias']; ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>Nenhuma receita encontrada.</p>
    <?php endif; ?>
</body>
</html>
