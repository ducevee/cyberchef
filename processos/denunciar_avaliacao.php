<?php
session_start();

// Incluindo o arquivo de inicialização do banco de dados
include 'inicializar_banco.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: ../paginas/login.php");
    exit();
}

// Verifica se o formulário foi enviado por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos necessários foram enviados
    if (isset($_POST['id_avaliacao']) && isset($_POST['fk_receita'])) {
        // Recebe os dados do formulário
        $id_avaliacao = $_POST['id_avaliacao'];
        $fk_receita = $_POST['fk_receita'];

        // Exibe um formulário para informar o motivo da denúncia
        echo "
        <form method='POST' action='processar_denuncia.php'>
            <input type='hidden' name='id_avaliacao' value='$id_avaliacao'>
            <input type='hidden' name='fk_receita' value='$fk_receita'>
            <label for='motivo'>Motivo da Denúncia:</label><br>
            <textarea name='motivo' id='motivo' rows='4' cols='50' required></textarea><br>
            <button type='submit'>Enviar</button>
            <button type='button' onclick='history.back()'>Cancelar</button>
        </form>";
        exit();
    } else {
        // Se os campos necessários não foram enviados, redireciona para a página de erro
        header("Location: ../paginas/erro.php");
        exit();
    }
} else {
    // Se o arquivo não foi acessado via POST, redireciona para a página de erro
    header("Location: ../paginas/erro.php");
    exit();
}

// Verifica se o formulário de denúncia foi submetido
if (isset($_POST['denunciar'])) {
    // Recebe os dados do formulário de denúncia
    $id_avaliacao = $_POST['id_avaliacao'];
    $id_receita = $_POST['fk_receita']; // Adicionando o id da receita
    $id_usuario = $_SESSION['usuario_id'];
    $motivo = $_POST['motivo']; // Capturar o motivo da denúncia

    // Prepara e executa a consulta SQL para inserir a denúncia no banco de dados
    $sql = "INSERT INTO Denuncia (fk_id_avaliacao, fk_id_receita, fk_id_usuario, data_denuncia, motivo) VALUES (:id_avaliacao, :id_receita, :id_usuario, NOW(), :motivo)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_avaliacao', $id_avaliacao);
    $stmt->bindParam(':id_receita', $id_receita); // Adicionando o bind para o id da receita
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':motivo', $motivo);
    $stmt->execute();

    // Verifica se a denúncia foi inserida com sucesso
    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Avaliação denunciada com sucesso!'); window.location.href = '../paginas/listar_avaliacoes.php';</script>";
    } else {
        echo "<script>alert('Erro ao denunciar avaliação.'); window.history.back();</script>";
    }
}
?>
