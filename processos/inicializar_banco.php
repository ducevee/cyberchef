<?php
$host = 'localhost';
$dbname = 'cyberchef';
$username = 'root';
$password = 'PUC@1234';

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

    // Cria as tabelas se não existirem no banco de dados 
    $pdo->exec("CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
                        
    // Cria a tabela de Receita
    $pdo->exec("CREATE TABLE IF NOT EXISTS Receita (
        id_receita INT PRIMARY KEY,
        tempo_preparo VARCHAR(20),
        modo_preparo TEXT,
        qtde_porcoes INT,
        foto VARCHAR(50),
        descricao VARCHAR(200),
        data DATETIME,
        titulo VARCHAR(50),
        dificuldade VARCHAR(10),
        fk_id_usuario INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Cria a tabela de Categoria
    $pdo->exec("CREATE TABLE IF NOT EXISTS Categoria (
        id_categoria INT PRIMARY KEY,
        categoria VARCHAR(25)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");


    // Cria a tabela de Avaliacao
    $pdo->exec("CREATE TABLE IF NOT EXISTS Avaliacao (
        id_avaliacao INT PRIMARY KEY,
        qtde_estrelas INT,
        mensagem VARCHAR(200),
        foto VARCHAR(50),
        created DATETIME,
        fk_receita INT,
        fk_id_usuario INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            
    // Cria a tabela de Ingredientes
    $pdo->exec("CREATE TABLE IF NOT EXISTS Ingredientes (
        id_ingrediente INT PRIMARY KEY,
        ingrediente VARCHAR(50),
        unidade VARCHAR(20),
        quantidade INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Cria a tabela de Denuncia
    $pdo->exec("CREATE TABLE IF NOT EXISTS Denuncia (
        id_denuncia INT PRIMARY KEY,
        fk_id_receita INT,
        data_denuncia DATETIME,
        fk_Avaliacao_id_avaliacao INT,
        data_moderacao DATETIME,
        fk_id_usuario INT,
        fk_id_denunciante INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Cria a tabela de Receita_Ingrediente
    $pdo->exec("CREATE TABLE IF NOT EXISTS Receita_Ingrediente (
        id_receita_ingrediente INT PRIMARY KEY,
        id_receita INT,
        id_ingrediente INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Cria a tabela de Receita_Categoria
    $pdo->exec("CREATE TABLE IF NOT EXISTS Receita_Categoria (
        id_receita_categoria INT PRIMARY KEY,
        id_receita INT,
        id_categoria INT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
 
                
    // Adiciona as chaves estrangeiras
    // $pdo->exec("ALTER TABLE Receita ADD CONSTRAINT FK_Receita_Usuario FOREIGN KEY (fk_id_usuario) REFERENCES usuarios(id);");
    // $pdo->exec("ALTER TABLE Avaliacao ADD CONSTRAINT FK_Avaliacao_Receita FOREIGN KEY (fk_receita) REFERENCES Receita(id_receita);");
    // $pdo->exec("ALTER TABLE Avaliacao ADD CONSTRAINT FK_Avaliacao_Usuario FOREIGN KEY (fk_id_usuario) REFERENCES usuarios(id);");
    // $pdo->exec("ALTER TABLE Denuncia ADD CONSTRAINT FK_Denuncia_Receita FOREIGN KEY (fk_id_receita) REFERENCES Receita(id_receita);");
    // $pdo->exec("ALTER TABLE Denuncia ADD CONSTRAINT FK_Denuncia_Avaliacao FOREIGN KEY (fk_Avaliacao_id_avaliacao) REFERENCES Avaliacao(id_avaliacao);");
    // $pdo->exec("ALTER TABLE Denuncia ADD CONSTRAINT FK_Denuncia_Moderador FOREIGN KEY (fk_id_usuario) REFERENCES usuarios(id);");
    // $pdo->exec("ALTER TABLE Denuncia ADD CONSTRAINT FK_Denuncia_Denunciante FOREIGN KEY (fk_id_denunciante) REFERENCES usuarios(id);");
    // $pdo->exec("ALTER TABLE Receita_Ingrediente ADD CONSTRAINT FK_Receita_Ingrediente_Receita FOREIGN KEY (id_receita) REFERENCES Receita(id_receita);");
    // $pdo->exec("ALTER TABLE Receita_Ingrediente ADD CONSTRAINT FK_Receita_Ingrediente_Ingrediente FOREIGN KEY (id_ingrediente) REFERENCES Ingredientes(id_ingrediente);");
    // $pdo->exec("ALTER TABLE Receita_Categoria ADD CONSTRAINT FK_Receita_Categoria_Receita FOREIGN KEY (id_receita) REFERENCES Receita(id_receita);");
    // $pdo->exec("ALTER TABLE Receita_Categoria ADD CONSTRAINT FK_Receita_Categoria_Categoria FOREIGN KEY (id_categoria) REFERENCES Categoria(id_categoria);");

                
} catch (PDOException $e) {
    die("Erro ao configurar banco de dados: " . $e->getMessage());
}
?>