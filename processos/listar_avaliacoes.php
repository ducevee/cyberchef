<?php

include_once '../processos/inicializar_banco.php';

$id_receita = isset($_GET['id']) ? $_GET['id'] : null;
if (!$id_receita) {
    echo "<script>alert('Nenhuma receita especificada.'); window.location.href='listar_receita.php';</script>";
    exit;
}

try {
    // Busca detalhada da receita, incluindo ingredientes e categorias
    $stmt = $pdo->prepare("
        SELECT r.*, u.nome AS nome_usuario, 
            GROUP_CONCAT(DISTINCT i.ingrediente SEPARATOR ', ') AS ingredientes, 
            GROUP_CONCAT(DISTINCT c.categoria SEPARATOR ', ') AS categorias
        FROM Receita r
        JOIN usuarios u ON r.fk_id_usuario = u.id
        LEFT JOIN Receita_Ingrediente ri ON r.id_receita = ri.id_receita
        LEFT JOIN Ingredientes i ON ri.id_ingrediente = i.id_ingrediente
        LEFT JOIN Receita_Categoria rc ON r.id_receita = rc.id_receita
        LEFT JOIN Categoria c ON rc.id_categoria = c.id_categoria
        WHERE r.id_receita = ?
        GROUP BY r.id_receita
    ");
    $stmt->execute([$id_receita]);
    $receita = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receita) {
        echo "<script>alert('Receita não encontrada.'); window.location.href='listar_receita.php';</script>";
        exit;
    }

    $query_avaliacoes = "SELECT a.id_avaliacao, a.qtde_estrelas, a.mensagem, a.created, u.id AS id_usuario, u.nome AS nome_usuario
    FROM Avaliacao AS a
    INNER JOIN usuarios AS u ON a.fk_id_usuario = u.id
    WHERE a.fk_receita = ?
    ORDER BY a.created DESC";

    $stmt_avaliacoes = $pdo->prepare($query_avaliacoes);
    $stmt_avaliacoes->execute([$id_receita]);

    $totalEstrelas = 0;
    $totalAvaliacoes = 0;

    if ($stmt_avaliacoes->rowCount() > 0) {  
    while ($row_avaliacao = $stmt_avaliacoes->fetch(PDO::FETCH_ASSOC)) {
    extract($row_avaliacao);
    $id_usuario = $row_avaliacao['id_usuario']; // Aqui corrigimos para extrair o ID do usuário


            $totalEstrelas += $qtde_estrelas;
            $totalAvaliacoes++;

            echo "<div class='avaliacao' style>";
            echo "<p><strong>Avaliação feita por:</strong> $nome_usuario</p>";
            echo "<p><strong>Data:</strong> $created</p>";
            echo "<p><strong>Estrelas:</strong>";
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $qtde_estrelas) {
                    echo '<i class="estrela-preenchida fa-solid fa-star"></i>';
                } else {
                    echo '<i class="estrela-vazia fa-solid fa-star"></i>';
                }
            }
            echo "</p>";
            if (!empty($mensagem)){
                echo "<p><strong>Comentário:</strong> $mensagem</p>";
            }
            echo "</div>";
            if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id_usuario) {
                echo "<form method='POST' action='../processos/excluir_avaliacao.php'>";
                echo "<input type='hidden' name='id_avaliacao' value='$id_avaliacao'>";
                echo "<input type='hidden' name='fk_receita' value='$id_receita'>"; // Adicionando o id da receita
                echo "<button type='submit' style='padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-right: 5px; background-color: #dc3545; color: white;'>Excluir Avaliação</button>";
                echo "</form>";}
            echo "<br><hr>";
        }
    if ($stmt_avaliacoes->rowCount() > 0) {  
    while ($row_avaliacao = $stmt_avaliacoes->fetch(PDO::FETCH_ASSOC)) {
    extract($row_avaliacao);
    $id_usuario = $row_avaliacao['id_usuario']; // Aqui corrigimos para extrair o ID do usuário


            $totalEstrelas += $qtde_estrelas;
            $totalAvaliacoes++;

            echo "<div class='avaliacao' style>";
            echo "<p><strong>Avaliação feita por:</strong> $nome_usuario</p>";
            echo "<p><strong>Data:</strong> $created</p>";
            echo "<p><strong>Estrelas:</strong>";
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $qtde_estrelas) {
                    echo '<i class="estrela-preenchida fa-solid fa-star"></i>';
                } else {
                    echo '<i class="estrela-vazia fa-solid fa-star"></i>';
                }
            }
            echo "</p>";
            if (!empty($mensagem)){
                echo "<p><strong>Comentário:</strong> $mensagem</p>";
            }
            echo "</div>";
            if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id_usuario) {
                echo "<form method='POST' action='../processos/excluir_avaliacao.php'>";
                echo "<input type='hidden' name='id_avaliacao' value='$id_avaliacao'>";
                echo "<input type='hidden' name='fk_receita' value='$id_receita'>"; // Adicionando o id da receita
                echo "<button type='submit' style='padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-right: 5px; background-color: #dc3545; color: white;'>Excluir Avaliação</button>";
                echo "</form>";}
            echo "<br><hr>";
        }
        
        // Calcula a média das avaliações
        $mediaAvaliacoes = $totalEstrelas / $totalAvaliacoes;
        $mediaFormatada = number_format($mediaAvaliacoes, 1); // Formata para uma casa decimal
        echo "<p><strong>Média das avaliações:</strong> $mediaFormatada</p>";
    } else {
        echo "<p>Não há avaliações para esta receita ainda.</p>"; 
    }
} catch (PDOException $e) {
    die("Erro de banco de dados: " . $e->getMessage());
}
?>
