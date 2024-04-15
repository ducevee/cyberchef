<?php
session_start();
include_once '../processos/inicializar_banco.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../paginas/login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $tempo_preparo = $_POST['tempo_preparo'];
    $qtde_porcoes = intval($_POST['qtde_porcoes']);
    $tipo_porcao = $_POST['tipo_porcao'];
    $descricao = $_POST['descricao'];
    $modo_preparo = $_POST['modo_preparo'];
    $dificuldade = $_POST['dificuldade'];
    $categorias = $_POST['filtros'];
    $idUsuario = $_SESSION['usuario_id'];

    $target_dir = "../uploads/";
    if (!file_exists($target_dir) && !mkdir($target_dir, 0777, true)) {
        die('Erro: Não foi possível criar a pasta de uploads.');
    }

    if (isset($_FILES['foto'])) {
        if ($_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = $_FILES['foto'];
            $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
            $nomeArquivo = uniqid() . "." . $extensao;
            $caminho = $target_dir . $nomeArquivo;

            if (move_uploaded_file($foto['tmp_name'], $caminho)) {
                try {
                    $pdo->beginTransaction();
                    $sql_receita = "INSERT INTO Receita (foto, titulo, qtde_porcoes, tipo_porcao, descricao, modo_preparo, dificuldade, tempo_preparo, fk_id_usuario, data) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $pdo->prepare($sql_receita);
                    $stmt->execute([$nomeArquivo, $titulo, $qtde_porcoes, $tipo_porcao, $descricao, $modo_preparo, $dificuldade, $tempo_preparo, $idUsuario]);
                    $pdo->commit();

                    header("Location: ../processos/listar_receita.php?mensagem=" . urlencode("Receita cadastrada com sucesso!"));
                    exit;
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    die('Erro ao cadastrar receita: ' . $e->getMessage());
                }
            } else {
                die('Erro: Falha ao mover o arquivo.');
            }
        } else {
            die('Erro no upload: ' . $_FILES['foto']['error']);
        }
    } else {
        die('Erro: Nenhum arquivo enviado.');
    }
} else {
    die('Erro: Formulário não submetido!');
}
?>
