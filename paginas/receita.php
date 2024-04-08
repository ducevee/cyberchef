<?php
session_start();
include_once '../processos/inicializar_banco.php';
if (isset($_SESSION['msg'])) {
  echo "<script>alert('" . $_SESSION['msg'] . "');</script>";
  unset($_SESSION['msg']);}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receita</title>
    <link rel="stylesheet" href="../css/style_avaliacao.css">
</head>
<body>
    <h1>Teste de página de receita</h1>
    <p>Bla bla bla tem uma receita aqui </p>
    <p>Nossa olha que legal não sei o que não sei o que lá</p> 
    <p><img src="../css/img/polenta.jpeg" alt=""></p>

    <?php 
      if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        echo '<a href="../paginas/avaliar.php">Clique aqui para avaliar essa receita</a><br>';
      } 
      else {
        echo '<p>Você precisa estar logado para avaliar esta receita.</p>';
        echo '<a href="../paginas/login.php">Clique aqui para logar</a><br>';}
      include '../processos/listar_avaliacoes.php';
    ?>
</body>
</html>
