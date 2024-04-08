<?php
session_start();
include_once '../processos/inicializar_banco.php';

if (isset($_GET['id_receita'])) {
    $id_receita = $_GET['id_receita'];

    $query_avaliacoes = "SELECT a.id_avaliacao, a.qtde_estrelas, a.mensagem, a.created, u.id AS id_usuario, u.nome as usr 
                         FROM avaliacao AS a 
                         INNER JOIN usuarios AS u ON a.fk_id_usuario = u.id 
                         WHERE a.fk_receita = :fk_receita
                         ORDER BY a.id_avaliacao DESC";

    $result_avaliacoes = $pdo->prepare($query_avaliacoes);
    $result_avaliacoes->bindParam(':fk_receita', $id_receita, PDO::PARAM_INT);
    $result_avaliacoes->execute();

    $totalEstrelas = 0;
    $totalAvaliacoes = 0;

    if ($result_avaliacoes->rowCount() > 0) {  
        while ($row_avaliacao = $result_avaliacoes->fetch(PDO::FETCH_ASSOC)) {
            extract($row_avaliacao);
            
            $totalEstrelas += $qtde_estrelas;
            $totalAvaliacoes++;
            
            echo "<p>Avaliação feita por: $usr <br> $created </p>";

            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $qtde_estrelas) {
                    echo '<i class="estrela-preenchida fa-solid fa-star"></i>';
                } else {
                    echo '<i class="estrela-vazia fa-solid fa-star"></i>';
                }
            }
            echo "<p>Comentário: $mensagem</p>";
            if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id_usuario) {
                echo "<a href='../processos/excluir_avaliacao.php?id_avaliacao=$id_avaliacao'>Excluir avaliação</a>";
            }
            echo "<br><hr>";
           
        }
        
        $mediaAvaliacoes = $totalEstrelas / $totalAvaliacoes;
        $mediaFormatada = sprintf("%.1f", $mediaAvaliacoes); //vai limitar o número de casas decimais
        echo "<p>Média das avaliações: $mediaFormatada</p>";
    } else {
        echo "<p>Não há avaliações cadastradas para esta receita.</p>"; 
    }
} else {
    echo "<p>ID da receita não especificado.</p>"; 
}
?>
