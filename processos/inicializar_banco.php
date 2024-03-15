<?php
$host = '127.0.0.1';
$dbname = 'cyberchef';
$username = 'edu';
$password = 'Senha123';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Criar banco de dados se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`;
                CREATE USER IF NOT EXISTS '$username'@'localhost' IDENTIFIED BY '$password';
                GRANT ALL ON `$dbname`.* TO '$username'@'localhost';
                FLUSH PRIVILEGES;");

    // Selecionar o banco de dados
    $pdo->exec("USE `$dbname`");

    // Criar a tabela de usuários se não existir
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
                
} catch (PDOException $e) {
    die("Erro ao configurar banco de dados: " . $e->getMessage());
}
?>
