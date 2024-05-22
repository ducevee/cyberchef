<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    // Se o usuário não estiver logado, redireciona para a página de login
    header("Location: ../paginas/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o formulário foi enviado por POST

    include_once 'inicializar_banco.php'; // Inclui o arquivo de inicialização do banco de dados

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

?>
