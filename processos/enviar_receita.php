<?php
session_start();
include_once '../processos/inicializar_banco.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../paginas/login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_receita = $_POST['id_receita'] ?? null;
    $titulo = $_POST['titulo'];
    $tempo_preparo = $_POST['tempo_preparo'];
    $qtde_porcoes = intval($_POST['qtde_porcoes']);
    $tipo_porcao = $_POST['tipo_porcao'];
    $descricao = $_POST['descricao'];
    $modo_preparo = $_POST['modo_preparo'];
    $dificuldade = $_POST['dificuldade'];
    $categorias = $_POST['filtros'];
    $idUsuario = $_SESSION['usuario_id'];

    $nomeArquivo = ''; // Inicializa o nome do arquivo como vazio

    // Verifica se existe um arquivo sendo enviado e se não há erro de upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto = $_FILES['foto'];
        $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $nomeArquivo = uniqid() . "." . $extensao;
        $caminho = "../uploads/" . $nomeArquivo;

        if (!move_uploaded_file($foto['tmp_name'], $caminho)) {
            die('Erro: Falha ao mover o arquivo.');
        }
    } elseif (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE && $id_receita) {
        // Nenhum arquivo enviado e estamos editando uma receita existente, busca o nome atual da foto
        $query = $pdo->prepare("SELECT foto FROM Receita WHERE id_receita = ?");
        $query->execute([$id_receita]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $nomeArquivo = $result['foto']; // Mantém a foto atual
    } else {
        // Erro no upload ou nenhum arquivo enviado para uma nova receita
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
            die('Erro no upload: ' . $_FILES['foto']['error']);
        } else {
            die('Erro: Nenhum arquivo enviado e é uma nova receita.');
        }
    }

    try {
        $pdo->beginTransaction();
        if ($id_receita) {
            // Atualizar receita existente
            $sql_receita = "UPDATE Receita SET foto=?, titulo=?, qtde_porcoes=?, tipo_porcao=?, descricao=?, modo_preparo=?, dificuldade=?, tempo_preparo=?, fk_id_usuario=?, data=NOW() WHERE id_receita=?";
            $stmt = $pdo->prepare($sql_receita);
            $stmt->execute([$nomeArquivo, $titulo, $qtde_porcoes, $tipo_porcao, $descricao, $modo_preparo, $dificuldade, $tempo_preparo, $idUsuario, $id_receita]);
        } else {
            // Inserir nova receita
            $sql_receita = "INSERT INTO Receita (foto, titulo, qtde_porcoes, tipo_porcao, descricao, modo_preparo, dificuldade, tempo_preparo, fk_id_usuario, data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql_receita);
            $stmt->execute([$nomeArquivo, $titulo, $qtde_porcoes, $tipo_porcao, $descricao, $modo_preparo, $dificuldade, $tempo_preparo, $idUsuario]);
        }
        $pdo->commit();
        header("Location: ../paginas/listar_receita.php?mensagem=" . urlencode("Receita " . ($id_receita ? "atualizada" : "cadastrada") . " com sucesso!"));
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die('Erro ao ' . ($id_receita ? "atualizar" : "cadastrar") . ' receita: ' . $e->getMessage());
    }
}
?>
