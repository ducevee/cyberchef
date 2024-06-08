<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Receita</title>
    <link rel="stylesheet" href="../css/style_listar_receita.css">
    <style>
        .botao-denunciar {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            background-color: #007bff; /* Azul */
            color: white;
        }
    </style>
</head>
<body>
    <?php
    include_once '../processos/inicializar_banco.php';

    $id_receita = isset($_GET['id']) ? $_GET['id'] : null;
    if (!$id_receita) {
        echo "<script>alert('Nenhuma receita especificada.'); window.location.href='listar_receita.php';</script>";
        exit;
    }

    try {
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
                $id_usuario = $row_avaliacao['id_usuario'];

                $totalEstrelas += $qtde_estrelas;
                $totalAvaliacoes++;

                // Formatar a data no formato dia + "às" + horário sem segundos
                $data_formatada = date('d/m/Y \à\s H:i', strtotime($created));

                echo "<div class='avaliacao'>";
                echo "<p><strong>Avaliação feita por:</strong> $nome_usuario</p>";
                echo "<p><strong>Data:</strong> $data_formatada</p>";
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
                if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $id_usuario) {
                    echo "<form method='POST' action='../processos/excluir_avaliacao.php'>";
                    echo "<input type='hidden' name='id_avaliacao' value='$id_avaliacao'>";
                    echo "<input type='hidden' name='fk_receita' value='$id_receita'>";
                    echo "<button type='submit' style='padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-right: 5px; background-color: #dc3545; color: white;'>Excluir Avaliação</button>";
                    echo "</form>";
                }
                if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] != $id_usuario) {
                    echo "<input type='hidden' name='id_avaliacao' value='$id_avaliacao'>";
                    echo "<input type='hidden' name='fk_receita' value='$id_receita'>";
                    echo "<button onclick='denunciarAvaliacao($id_avaliacao)' class='botao-denunciar'>Denunciar Avaliação</button>"; 
                    echo "</form>";
                }
                echo "</div>";
                echo "<br><br>";
                echo "<hr>"; // Linha divisória entre as avaliações
            }

            $mediaAvaliacoes = $totalEstrelas / $totalAvaliacoes;
            $mediaFormatada = number_format($mediaAvaliacoes, 1);
            echo "<p><strong>Média das avaliações:</strong> $mediaFormatada</p>";
        } else {
            echo "<p>Não há avaliações para esta receita ainda.</p>";
        }
    } catch (PDOException $e) {
        die("Erro de banco de dados: " . $e->getMessage());
    }
    ?>

    <!-- Modal para denunciar avaliação -->
    <div id="modalDenunciaAvaliacao" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Denunciar Avaliação</h2>
                <span class="close" onclick="document.getElementById('modalDenunciaAvaliacao').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <form action="../processos/denunciar_avaliacao.php" method="post" onsubmit="return validarDenuncia()">
                    <input type="hidden" name="id_avaliacao" id="idAvaliacaoDenuncia" value="">
                    <label for="motivoDenunciaAvaliacao">Motivo da Denúncia:</label>
                    <textarea id="motivoDenunciaAvaliacao" name="motivo" required></textarea>
                    <div class="modal-footer">
                        <button type="submit" name="denunciar" style="background-color:#4CAF50; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Enviar Denúncia</button>
                        <button type="button" onclick="document.getElementById('modalDenunciaAvaliacao').style.display='none'" style="background-color:red; color:white; padding:10px 20px; border:none; border-radius:5px; cursor:pointer;">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function denunciarAvaliacao(idAvaliacao) {
            document.getElementById('idAvaliacaoDenuncia').value = idAvaliacao;
            document.getElementById('modalDenunciaAvaliacao').style.display = 'block';
        }

        function fecharModal() {
            document.getElementById('modalDenunciaAvaliacao').style.display = 'none';
        }

        function validarDenuncia() {
            var motivo = document.getElementById('motivoDenunciaAvaliacao').value.trim();
            if (motivo.length < 10) {
                alert('O motivo da denúncia deve conter no mínimo 10 caracteres.');
                return false;
            }
            return true;
        }

        window.onclick = function(event) {
            var modal = document.getElementById('modalDenunciaAvaliacao');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>

<!-- Botão para denunciar avaliação -->
</body>
</html>
