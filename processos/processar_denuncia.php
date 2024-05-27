<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se o formulário foi enviado por POST

    include_once 'inicializar_banco.php'; // Inclui o arquivo de inicialização do banco de dados

    // Verifica se os campos necessários foram enviados
    if (isset($_POST['id_avaliacao']) && isset($_POST['fk_receita']) && isset($_POST['motivo'])) {
        // Recebe os dados do formulário
        $id_avaliacao = $_POST['id_avaliacao'];
        $fk_receita = $_POST['fk_receita'];
        $motivo = $_POST['motivo'];

        // Insira aqui a lógica para processar a denúncia, como por exemplo, inserir o motivo da denúncia no banco de dados
        // Depois de processar a denúncia, você pode redirecionar o usuário para a página inicial ou para uma página de confirmação

        // Por exemplo:
        // $stmt = $pdo->prepare("INSERT INTO denuncias (id_avaliacao, motivo) VALUES (?, ?)");
        // $stmt->execute([$id_avaliacao, $motivo]);

        // Redireciona para a página de confirmação
        header("Location: ../paginas/confirmacao_denuncia.php");
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
