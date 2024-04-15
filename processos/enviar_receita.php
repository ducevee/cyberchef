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

    // Diretório para onde o arquivo será movido
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {    
        mkdir($target_dir, 0777, true);
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto = $_FILES['foto'];
        $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $nomeArquivo = uniqid() . "." . $extensao;
        $caminho = $target_dir . $nomeArquivo;

        if (move_uploaded_file($foto['tmp_name'], $caminho)) {
            // O arquivo foi movido para o diretório, inserir no banco de dados
            try {
                $pdo->beginTransaction();
                $sql_receita = "INSERT INTO Receita (foto, titulo, qtde_porcoes, tipo_porcao, descricao, modo_preparo, dificuldade, tempo_preparo, fk_id_usuario, data) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $pdo->prepare($sql_receita);
                $stmt->execute([$nomeArquivo, $titulo, $qtde_porcoes, $tipo_porcao, $descricao, $modo_preparo, $dificuldade, $tempo_preparo, $idUsuario]);
                $pdo->commit();
                
                header("Location: ../paginas/listar_receitas.php?mensagem=" . urlencode("Receita cadastrada com sucesso!"));
                exit;
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo "Erro ao cadastrar receita: " . $e->getMessage();
            }
        } else {
            echo "Falha ao mover o arquivo.";
        }
    } else {
        echo "Erro no upload da foto.";
    }
} else {
    echo "Erro: formulário não submetido!";
}
?>
