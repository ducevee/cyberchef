<?php
session_start();

include_once '../processos/inicializar_banco.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../paginas/login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_receita = $_POST['id_receita'] ?? null; // ID da receita para edição
    $foto = $_POST['foto'];
    $titulo = $_POST['titulo'];
    $tempo_preparo = $_POST['tempo_preparo'];
    $qtde_porcoes = intval($_POST['qtde_porcoes']);
    $tipo_porcao = $_POST['tipo_porcao']; 
    $descricao = $_POST['descricao'];
    $modo_preparo = $_POST['modo_preparo'];
    $dificuldade = $_POST['dificuldade'];
    $categorias = $_POST['filtros'];
    $idUsuario = $_SESSION['usuario_id']; // Captura o ID do usuário da sessão

    if ($id_receita) {
        // Atualizar receita existente
        $sql_update = "UPDATE Receita SET foto=?, titulo=?, qtde_porcoes=?, tipo_porcao=?, descricao=?, modo_preparo=?, dificuldade=?, tempo_preparo=?, fk_id_usuario=?, data=NOW() WHERE id_receita=?";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([$foto, $titulo, $qtde_porcoes, $tipo_porcao, $descricao, $modo_preparo, $dificuldade, $tempo_preparo, $idUsuario, $id_receita]);

    } else {
        // Inserir nova receita
        $sql_insert = "INSERT INTO Receita (foto, titulo, qtde_porcoes, tipo_porcao, descricao, modo_preparo, dificuldade, tempo_preparo, fk_id_usuario, data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql_insert);
        $stmt->execute([$foto, $titulo, $qtde_porcoes, $tipo_porcao, $descricao, $modo_preparo, $dificuldade, $tempo_preparo, $idUsuario]);
        $id_receita = $pdo->lastInsertId();

        // Inserir as categorias relacionadas à receita na tabela Receita_Categoria
        foreach ($categorias as $categoria) {
            $sql_categoria = "INSERT INTO Categoria (categoria) VALUES ('$categoria')";
            $pdo->query($sql_categoria);
            $id_categoria = $pdo->lastInsertId();

            $sql_relacionamento = "INSERT INTO Receita_Categoria (id_receita, id_categoria) VALUES ('$id_receita', '$id_categoria')";
            $pdo->query($sql_relacionamento);
        }

        // Verificar se foram enviados ingredientes
        if (isset($_POST['ingredientes']) && isset($_POST['quantidades']) && isset($_POST['unidades'])) {
            $ingredientes = $_POST['ingredientes'];
            $quantidades = $_POST['quantidades'];
            $unidades = $_POST['unidades'];

            // Inserir cada ingrediente na tabela Ingredientes e relacioná-lo à receita na tabela Receita_Ingrediente
            for ($i = 0; $i < count($ingredientes); $i++) {
                $ingrediente = $ingredientes[$i];
                $quantidade = $quantidades[$i];
                $unidade = $unidades[$i];

                $sql_ingrediente = "INSERT INTO Ingredientes (ingrediente, quantidade, unidade) 
                                    VALUES ('$ingrediente', '$quantidade', '$unidade')";
                $pdo->query($sql_ingrediente);
                $id_ingrediente = $pdo->lastInsertId();

                $sql_relacionamento_ingrediente = "INSERT INTO Receita_Ingrediente (id_receita, id_ingrediente) 
                                                    VALUES ('$id_receita', '$id_ingrediente')";
                $pdo->query($sql_relacionamento_ingrediente);
            }
        }
    }

    echo "Receita " . ($id_receita ? "atualizada" : "enviada") . " com sucesso!";
} else {
    echo "Erro: formulário não submetido!";
}
?>
