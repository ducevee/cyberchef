<?php
session_start();
include_once '../processos/inicializar_banco.php';

// Verificar se o usuário está logado
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){  
    try {
        // Verificar se o ID do usuário está definido
        if (!empty($_SESSION['usuario_id'])) {
            $id_usuario = $_SESSION['usuario_id'];

            // Verificar se selecionou estrela
            if (!empty($_POST['estrela']) && !empty($_POST['id_receita'])) {
                $estrela = (int) filter_input(INPUT_POST, 'estrela', FILTER_DEFAULT);
                $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_DEFAULT);
                $id_receita = (int) $_POST['id_receita']; 
                
                // CADASTRAR NO BANCO
                $query_avaliacoes = "INSERT INTO avaliacao (qtde_estrelas, mensagem, created, fk_id_usuario, fk_receita) 
                                     VALUES (:qtde_estrelas, :mensagem, :created, :fk_id_usuario, :fk_receita)";

                $cad_avaliacoes = $pdo->prepare($query_avaliacoes);

                $cad_avaliacoes->bindParam(':qtde_estrelas', $estrela, PDO::PARAM_INT);
                $cad_avaliacoes->bindParam(':mensagem', $mensagem, PDO::PARAM_STR);
                $created = date('Y-m-d H:i:s');
                $cad_avaliacoes->bindParam(':created', $created, PDO::PARAM_STR);
                $cad_avaliacoes->bindParam(':fk_id_usuario', $id_usuario, PDO::PARAM_INT); 
                $cad_avaliacoes->bindParam(':fk_receita', $id_receita, PDO::PARAM_INT); 

                if ($cad_avaliacoes->execute()) {
                    $_SESSION['msg'] = "Avaliação cadastrada com sucesso!";
                } else {
                    throw new PDOException("Erro ao cadastrar avaliação.");
                }
            } else {
                throw new PDOException("Erro: é necessário selecionar pelo menos 1 estrela e fornecer o ID da receita.");
            }
        } else {
            throw new PDOException("Erro: ID do usuário não está definido.");
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = "<p>Erro: " . $e->getMessage() . "</p>";
    }

    header("Location: ../paginas/receita.php?id_receita=$id_receita"); // Redirecionando de volta para a página da receita
    exit(); 
} else {
    header("Location: ../paginas/login.php");
    exit(); 
}
?>
