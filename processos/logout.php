<?php
session_start(); // Resume a sessão existente
session_unset(); // Libera todas as variáveis de sessão
session_destroy(); // Destrói a sessão

header("Location: ../paginas/login.php"); // Redireciona o usuário para a página de login
exit;
?>
