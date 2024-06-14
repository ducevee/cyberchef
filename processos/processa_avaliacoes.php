<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

include_once '../processos/inicializar_banco.php';

// Verificar se o usuário está logado
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    try {
        // Verificar se o ID da receita está definido
        if (!empty($_POST['id_receita'])) {
            $id_receita = $_POST['id_receita'];

            // Verificar se a quantidade de estrelas está definida
            if (isset($_POST['estrela']) && $_POST['estrela'] !== '') {
                $estrela = (int) $_POST['estrela'];
                $mensagem = isset($_POST['mensagem']) ? $_POST['mensagem'] : '';

                // Preparar e executar a inserção da avaliação no banco de dados
                $query = "INSERT INTO Avaliacao (qtde_estrelas, mensagem, created, fk_receita, fk_id_usuario) 
                          VALUES (:qtde_estrelas, :mensagem, :created, :fk_receita, :fk_id_usuario)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':qtde_estrelas', $estrela, PDO::PARAM_INT);
                $stmt->bindParam(':mensagem', $mensagem, PDO::PARAM_STR);
                $stmt->bindValue(':created', date('Y-m-d H:i:s'), PDO::PARAM_STR);
                $stmt->bindParam(':fk_id_usuario', $_SESSION['usuario_id'], PDO::PARAM_INT);
                $stmt->bindParam(':fk_receita', $id_receita, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $_SESSION['msg'] = "<script>alert('Avaliação feita com sucesso!');</script>";
                } else {
                    throw new PDOException("<script>alert('Erro ao cadastrar avaliação');</script>");
                }
            } else {
                $_SESSION['msg'] = "<script>alert('Erro: Quantidade de estrelas não está definida.');</script>";
            }

            // Redirecionar de volta para a página de visualização da receita após o processamento
            header("Location: ../paginas/visualizar_receita.php?id=$id_receita");
            exit();
        } else {
            $_SESSION['msg'] = "<script>alert('Erro: ID da receita não está definido.');</script>";
            header("Location: ../paginas/listar_receita.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['msg'] = "<p>Erro: " . $e->getMessage() . "</p>";
        header("Location: ../paginas/visualizar_receita.php?id=" . (isset($id_receita) ? $id_receita : ''));
        exit();
    }
} else {
    // Se o usuário não estiver logado, redirecionar para a página de login
    header("Location: ../paginas/login.php");
    exit();
}
?>