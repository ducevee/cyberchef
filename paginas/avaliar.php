<?php
if (isset($_SESSION['usuario_id'])) {
    $id_usuario = $_SESSION['usuario_id'];
} else {
    header("Location: ../paginas/login.php");
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliação de Receita</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style_avaliar.css"/>
</head>
<body>
<h1>Avalie</h1>

<?php
if(isset($_SESSION['msg'])){
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
}
$id_receita = isset($_GET['id']) ? $_GET['id'] : null;

?>

<form method="POST" action="../processos/processa_avaliacoes.php">

    <div class="estrelas">
    <input type="radio" name="estrela" id="vazio" value="" checked>
    <label for="estrela1"><i class="opcao fa" aria-hidden="true"></i></label>
    <input type="radio" name="estrela" id="estrela1" value="1">
    <label for="estrela2"><i class="opcao fa" aria-hidden="true"></i></label>
    <input type="radio" name="estrela" id="estrela2" value="2" >
    <label for="estrela3"><i class="opcao fa" aria-hidden="true"></i></label>
    <input type="radio" name="estrela" id="estrela3" value="3" >
    <label for="estrela4"><i class="opcao fa" aria-hidden="true"></i></label>
    <input type="radio" name="estrela" id="estrela4" value="4" >
    <label for="estrela5"><i class="opcao fa" aria-hidden="true"></i></label>
    <input type="radio" name="estrela" id="estrela5" value="5" > <br><br>
    <textarea name="mensagem" id="" cols="50" rows="4" placeholder="Digite seu comentário"></textarea> <br><br>
    <label for="foto">Inclua uma foto para complementar sua avaliação (opcional): </label><br><br>
    <input type="file" id="foto" name="foto" accept="image/*"><br><br>
    <input type="hidden" name="id_receita" value="<?php echo $id_receita; ?>">

    <input type="submit" value="cadastrar" ;>
    
    <br><br>
    </div>
</form>

</body>
</html>
