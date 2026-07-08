-- =====================================================================
-- AtendeLab - schema de referência (MySQL / MariaDB)
--
-- OBS.: a aplicação cria o banco, as tabelas e os dados iniciais
-- automaticamente na primeira execução (ver app/Core/Database.php).
-- Este arquivo serve apenas como referência / importação manual.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS atendelab
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE atendelab;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    login VARCHAR(60) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS pessoas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    documento VARCHAR(40) NOT NULL,
    email VARCHAR(160) NOT NULL,
    curso VARCHAR(120) NOT NULL,
    periodo VARCHAR(40) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pessoa_id INT NOT NULL,
    tipo_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data DATE NOT NULL,
    status ENUM('aberto','concluido') NOT NULL DEFAULT 'aberto',
    observacao TEXT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pessoa_id) REFERENCES pessoas(id),
    FOREIGN KEY (tipo_id) REFERENCES tipos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dados iniciais (o hash do admin é gerado pela aplicação; aqui é ilustrativo).
INSERT INTO pessoas (nome, documento, email, curso, periodo, ativo) VALUES
('Felipe Nocce Lucena', '52988177340', 'felipe.nocce.lucena@gmail.com', 'Ciência da Computação', '6º semestre', 1),
('Felipe Lucena', '48712095522', 'felipe.lucena@gmail.com', 'Análise e Desenvolvimento de Sistemas', '2º semestre', 0),
('Felipe Nocce', '30945612788', 'felipe.nocce@gmail.com', 'Engenharia da Computação', '8º semestre', 1),
('F. Nocce Lucena', '77123408866', 'f.noccelucena@gmail.com', 'Sistemas de Informação', '4º semestre', 0),
('Felipe N. Lucena', '61508329411', 'felipe.n.lucena@gmail.com', 'Ciência de Dados', '1º semestre', 0);

INSERT INTO tipos (nome, descricao, ativo) VALUES
('teste', 'teste 2', 0),
('orientação', 'teste', 1),
('Admistrativo', 'Atendimento relacionado a dúvidas administrativas, documentos e solicitações acadêmicas.', 1),
('Orientação acadêmica', 'Atendimento para orientação de atividades e projetos.', 0),
('Suporte técnico', 'Atendimento relacionado a problemas em laboratório ou sistemas.', 1),
('Monitoria', 'Atendimento para dúvidas de conteúdo acadêmico.', 1);
