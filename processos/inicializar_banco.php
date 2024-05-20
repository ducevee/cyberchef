<?php
$host = '127.0.0.1';
$dbname = 'cyberchef';
$username = 'cyberchef';
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

    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_admin TINYINT(1) NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    
    CREATE TABLE IF NOT EXISTS Receita (
        id_receita INT AUTO_INCREMENT PRIMARY KEY,
        tempo_preparo VARCHAR(50),
        modo_preparo TEXT,
        qtde_porcoes INT,
        tipo_porcao VARCHAR(25),
        foto VARCHAR(50),
        descricao VARCHAR(200),
        data DATETIME,
        titulo VARCHAR(50),
        dificuldade VARCHAR(10),
        fk_id_usuario INT,
        denunciada TINYINT(1) DEFAULT 0,
        FOREIGN KEY (fk_id_usuario) REFERENCES usuarios(id)
    );
    
    CREATE TABLE IF NOT EXISTS Categoria (
        id_categoria INT AUTO_INCREMENT PRIMARY KEY,
        categoria VARCHAR(25)
    );
    
    CREATE TABLE IF NOT EXISTS Avaliacao (
        id_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
        qtde_estrelas INT,
        mensagem VARCHAR(200),
        foto VARCHAR(50),
        created DATETIME,
        fk_receita INT,
        fk_id_usuario INT,
        FOREIGN KEY (fk_receita) REFERENCES Receita(id_receita),
        FOREIGN KEY (fk_id_usuario) REFERENCES usuarios(id)
    );
    
    CREATE TABLE IF NOT EXISTS Ingredientes (
        id_ingrediente INT AUTO_INCREMENT PRIMARY KEY,
        ingrediente VARCHAR(50),
        unidade VARCHAR(20),
        quantidade INT
    );
    
    CREATE TABLE IF NOT EXISTS Denuncia (
        id_denuncia INT AUTO_INCREMENT PRIMARY KEY,
        motivo TEXT,
        fk_id_receita INT,
        data_denuncia DATETIME,
        fk_Avaliacao_id_avaliacao INT,
        data_moderacao DATETIME,
        fk_id_usuario INT,
        fk_id_denunciante INT,
        FOREIGN KEY (fk_id_receita) REFERENCES Receita(id_receita),
        FOREIGN KEY (fk_Avaliacao_id_avaliacao) REFERENCES Avaliacao(id_avaliacao),
        FOREIGN KEY (fk_id_usuario) REFERENCES usuarios(id), -- Moderador
        FOREIGN KEY (fk_id_denunciante) REFERENCES usuarios(id) -- Consumidor que fez a denúncia
    );
    
    CREATE TABLE IF NOT EXISTS Receita_Ingrediente (
        id_receita_ingrediente INT AUTO_INCREMENT PRIMARY KEY,
        id_receita INT,
        id_ingrediente INT,
        FOREIGN KEY (id_receita) REFERENCES Receita(id_receita),
        FOREIGN KEY (id_ingrediente) REFERENCES Ingredientes(id_ingrediente)
    );
    
    CREATE TABLE IF NOT EXISTS Receita_Categoria (
        id_receita_categoria INT AUTO_INCREMENT PRIMARY KEY,
        id_receita INT,
        id_categoria INT,
        FOREIGN KEY (id_receita) REFERENCES Receita(id_receita),
        FOREIGN KEY (id_categoria) REFERENCES Categoria(id_categoria)
    );";

    // Criar as tabelas no banco de dados
    $pdo->exec($sql);
                
} catch (PDOException $e) {
    die("Erro ao configurar banco de dados: " . $e->getMessage());
}
?>
