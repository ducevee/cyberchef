<?php
session_start();
include_once '../processos/inicializar_banco.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../paginas/login.php');
    exit;
}

$id_receita = isset($_REQUEST['id_receita']) ? $_REQUEST['id_receita'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_receita = $_POST['id_receita'] ?? null;
    $titulo = $_POST['titulo'];
    $tempo_preparo = $_POST['tempo_preparo'];
    $qtde_porcoes = intval($_POST['qtde_porcoes']);
    $tipo_porcao = $_POST['tipo_porcao'];
    $descricao = $_POST['descricao'];
    $modo_preparo = $_POST['modo_preparo'];
    $dificuldade = $_POST['dificuldade'];
    $categorias = $_POST['categorias'];
    $idUsuario = $_SESSION['usuario_id'];

    $nomeArquivo = ''; // Inicializa o nome do arquivo como vazio

    // Verifica se existe um arquivo sendo enviado e se não há erro de upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto = $_FILES['foto'];
        $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $nomeArquivo = uniqid() . "." . $extensao;
        $caminho = "../uploads/" . $nomeArquivo;

        if (!move_uploaded_file($foto['tmp_name'], $caminho)) {
            die(header("Location: ../paginas/postar_receita.php?mensagem=" . urlencode('Erro: Falha ao mover o arquivo.')));
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
            die(header("Location: ../paginas/postar_receita.php?mensagem=" . urlencode('Erro no upload: ' . $_FILES['foto']['error'])));
        } else {
            die(header("Location: ../paginas/postar_receita.php?mensagem=" . urlencode('Erro: Nenhum arquivo enviado e é uma nova receita.')));
        }
    }

    try {
        $pdo->beginTransaction();
        if ($id_receita) {
            // Consultar o banco de dados para verificar se a receita existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Receita WHERE id_receita = :id_receita");
            $stmt->bindParam(':id_receita', $id_receita);
            $stmt->execute();
            $receitaExiste = $stmt->fetchColumn() > 0;
            
            // Atualizar receita existente
            $sql_receita = "UPDATE Receita SET foto=?, titulo=?, qtde_porcoes=?, tipo_porcao=?, descricao=?, modo_preparo=?, dificuldade=?, tempo_preparo=?, fk_id_usuario=?, data=NOW() WHERE id_receita=?";
            $stmt = $pdo->prepare($sql_receita);
            $stmt->execute([$nomeArquivo, $titulo, $qtde_porcoes, $tipo_porcao, $descricao, $modo_preparo, $dificuldade, $tempo_preparo, $idUsuario, $id_receita]);
            // Limpar as entradas existentes em Receita_Ingrediente
            $stmt = $pdo->prepare("DELETE FROM Receita_Ingrediente WHERE id_receita = ?");
            $stmt->execute([$id_receita]);
            // Limpar as entradas existentes em Receita_Categoria
            $stmt = $pdo->prepare("DELETE FROM Receita_Categoria WHERE id_receita = ?");
            $stmt->execute([$id_receita]);
        } else {
            // Inserir nova receita
            $sql_receita = "INSERT INTO Receita (foto, titulo, qtde_porcoes, tipo_porcao, descricao, modo_preparo, dificuldade, tempo_preparo, fk_id_usuario, data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql_receita);
            $stmt->execute([$nomeArquivo, $titulo, $qtde_porcoes, $tipo_porcao, $descricao, $modo_preparo, $dificuldade, $tempo_preparo, $idUsuario]);
            $id_receita = $pdo->lastInsertId(); // Obter o ID da nova receita inserida
        }

        // Adicionar os ingredientes atualizados
        foreach ($_POST['ingredientes'] as $index => $nomeIngrediente) {
            $quantidade = $_POST['quantidades'][$index];
            $unidade = $_POST['unidades'][$index];

            // Verificar se o ingrediente já existe
            $stmt = $pdo->prepare("SELECT id_ingrediente FROM Ingredientes WHERE ingrediente = ? AND unidade = ?");
            $stmt->execute([$nomeIngrediente, $unidade]);
            $ingredienteExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ingredienteExistente) {
                $idIngrediente = $ingredienteExistente['id_ingrediente'];
            } else {
                // Inserir o novo ingrediente
                $stmt = $pdo->prepare("INSERT INTO Ingredientes (ingrediente, unidade, quantidade) VALUES (?, ?, ?)");
                $stmt->execute([$nomeIngrediente, $unidade, $quantidade]);
                $idIngrediente = $pdo->lastInsertId();
            }

            // Associar o ingrediente com a receita
            $stmt = $pdo->prepare("INSERT INTO Receita_Ingrediente (id_receita, id_ingrediente) VALUES (?, ?)");
            $stmt->execute([$id_receita, $idIngrediente]);
        }

        // Adicionar as categorias atualizadas
        foreach ($categorias as $categoriaNome) {
            $stmt = $pdo->prepare("SELECT id_categoria FROM Categoria WHERE categoria = ?");
            $stmt->execute([$categoriaNome]);
            $categoriaExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($categoriaExistente) {
                $idCategoria = $categoriaExistente['id_categoria'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO Categoria (categoria) VALUES (?)");
                $stmt->execute([$categoriaNome]);
                $idCategoria = $pdo->lastInsertId();
            }

            $stmt = $pdo->prepare("INSERT INTO Receita_Categoria (id_receita, id_categoria) VALUES (?, ?)");
            $stmt->execute([$id_receita, $idCategoria]);
        }

        $pdo->commit();
        if ($receitaExiste) {
            // Atualizar a receita existente
            header("Location: ../paginas/listar_receita.php?mensagem=" . urlencode("Receita atualizada com sucesso!"));
        } else {
            // Inserir uma nova receita
            header("Location: ../paginas/listar_receita.php?mensagem=" . urlencode("Receita cadastrada com sucesso!"));
        }
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die(header("Location: ../paginas/postar_receita.php?mensagem=" . urlencode('Erro ao salvar receita: ' . $e->getMessage())));
    }
}
?>
