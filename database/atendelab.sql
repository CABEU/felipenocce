-- =====================================================================
-- AtendeLab - Sistema de Controle de Atendimentos Acadêmicos
-- Script de banco de dados: database/atendelab.sql
-- Compatível com o contrato de dados descrito no Apêndice B da Aula 006
-- Campos oficiais: id, pessoa_id, tipo_atendimento_id, usuario_id,
-- data_atendimento, documento (NÃO usar cpf, id_pessoa, id_usuario, data_hora)
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS atendelab;
CREATE DATABASE atendelab
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE atendelab;

-- ---------------------------------------------------------------------
-- Tabela: usuarios
-- perfil usa 'admin' ou 'atendente' (NUNCA 'administrador')
-- ---------------------------------------------------------------------
CREATE TABLE usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(120)        NOT NULL,
    email         VARCHAR(150)        NOT NULL,
    senha         VARCHAR(255)        NOT NULL,
    perfil        ENUM('admin', 'atendente') NOT NULL DEFAULT 'atendente',
    status        ENUM('ativo', 'inativo')   NOT NULL DEFAULT 'ativo',
    criado_em     TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabela: pessoas
-- documento substitui cpf no projeto atual
-- ---------------------------------------------------------------------
CREATE TABLE pessoas (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(150)        NOT NULL,
    documento     VARCHAR(20)         NOT NULL,
    telefone      VARCHAR(20)         NULL,
    email         VARCHAR(150)        NOT NULL,
    curso         VARCHAR(120)        NULL,
    periodo       VARCHAR(10)         NULL,
    observacoes   TEXT                NULL,
    status        ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em     TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_pessoas_documento (documento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabela: tipos_atendimentos
-- ---------------------------------------------------------------------
CREATE TABLE tipos_atendimentos (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome          VARCHAR(120)        NOT NULL,
    descricao     TEXT                NULL,
    status        ENUM('ativo', 'inativo') NOT NULL DEFAULT 'ativo',
    criado_em     TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Tabela: atendimentos
-- usuario_id deve vir da sessão (usuário logado), nunca de campo livre
-- Não usar data_hora, id_pessoa ou id_usuario
-- ---------------------------------------------------------------------
CREATE TABLE atendimentos (
    id                    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pessoa_id             INT UNSIGNED NOT NULL,
    tipo_atendimento_id   INT UNSIGNED NOT NULL,
    usuario_id            INT UNSIGNED NOT NULL,
    descricao             TEXT         NOT NULL,
    status                ENUM('aberto', 'em_andamento', 'concluido')
                              NOT NULL DEFAULT 'aberto',
    data_atendimento      DATE         NOT NULL,
    horario_atendimento   TIME         NOT NULL,
    observacao_final      TEXT         NULL,
    criado_em             TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em         TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                                          ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_atendimentos_pessoa
        FOREIGN KEY (pessoa_id) REFERENCES pessoas (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_atendimentos_tipo
        FOREIGN KEY (tipo_atendimento_id) REFERENCES tipos_atendimentos (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_atendimentos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    KEY idx_atendimentos_status (status),
    KEY idx_atendimentos_data (data_atendimento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- SEED - Dados iniciais para testes (login usado no checklist da aula)
-- e-mail: admin@atendelab.com | senha: 123456
-- Hash gerado com password_hash('123456', PASSWORD_DEFAULT) (bcrypt)
-- =====================================================================

INSERT INTO usuarios (nome, email, senha, perfil, status) VALUES
('Administrador', 'admin@atendelab.com',
 '$2b$10$06XgmhXn7UzGZ6RLrp5apeDa5eTZfY.uBs87s8azgTPUEHbHfrs5O',
 'admin', 'ativo'),
('Atendente Teste', 'atendente@atendelab.com',
 '$2b$10$06XgmhXn7UzGZ6RLrp5apeDa5eTZfY.uBs87s8azgTPUEHbHfrs5O',
 'atendente', 'ativo');

INSERT INTO tipos_atendimentos (nome, descricao, status) VALUES
('Acesso ao laboratório', 'Liberação de uso e agendamento dos laboratórios.', 'ativo'),
('Apoio à extensão', 'Orientações relacionadas a projetos de extensão e atividades comunitárias.', 'ativo'),
('Dúvida acadêmica', 'Dúvidas sobre disciplinas, conteúdos, avaliações e atividades.', 'ativo'),
('Justificar Faltas', 'Justificar falta com documentos.', 'ativo'),
('Matrícula e documentação', 'Solicitações relacionadas à matrícula, declarações e históricos.', 'ativo'),
('Orientação de atividade', 'Orientações sobre trabalhos, TCC, projetos e entregas acadêmicas.', 'ativo'),
('Revisão de avaliação', 'Solicitações de revisão de provas, trabalhos e atividades avaliativas.', 'ativo'),
('Suporte técnico', 'Problemas com sistemas, equipamentos, acessos e recursos digitais.', 'ativo'),
('Outros', 'Atendimentos diversos ainda não classificados.', 'inativo');

INSERT INTO pessoas (nome, documento, telefone, email, curso, periodo, observacoes, status) VALUES
('Ana Carolina', '987.654.321-00', '(47) 99111-1111', 'ana.carolina@univille.br', 'Sistemas de Informação', '7', NULL, 'ativo'),
('Antonio Silva', '123.123.132-00', '(47) 99222-2222', 'antonio@email.com', 'Engenharia de Software', '5', NULL, 'ativo'),
('Beatriz Martins', '159.357.486-00', '(47) 99333-3333', 'beatriz.martins@univille.br', 'Direito', '5', NULL, 'ativo'),
('Camila Fernandes', '486.159.357-00', '(47) 99444-4444', 'camila.fernandes@univille.br', 'Sistemas de Informação', '4', NULL, 'ativo'),
('Carlos Henrique Souza', '321.654.987-10', '(47) 99555-5555', 'carlos.souza@exemplo.com', 'Engenharia de Software', '3', NULL, 'ativo'),
('Felipe Rocha', '963.852.741-00', '(47) 99666-6666', 'felipe.rocha@univille.br', 'Administração', '1', NULL, 'ativo'),
('Gabriel Oliveira', '357.159.486-00', '(47) 99777-7777', 'gabriel.oliveira@univille.br', 'Engenharia de Software', '3', NULL, 'ativo'),
('João da Silva', '123.456.789-00', '(47) 99888-8888', 'joao.silva@univille.br', 'Engenharia de Software', '5', 'Trancou o curso.', 'inativo'),
('Juliana Castro', '852.741.963-00', '(47) 99999-9999', 'juliana.castro@univille.br', 'Sistemas de Informação', '6', 'Transferida de campus.', 'inativo'),
('Larissa Alves', '321.654.987-00', '(47) 98111-1111', 'larissa.alves@univille.br', 'Direito', '2', NULL, 'ativo'),
('Marcos Pereira', '741.852.963-00', '(47) 98222-2222', 'marcos.pereira@univille.br', 'Engenharia de Software', '4', NULL, 'ativo'),
('Maria de Souza Silva', '111.222.333-44', '(47) 98333-3333', 'maria.souza@exemplo.com', 'Engenharia de Software', '5', NULL, 'ativo'),
('Mariana Oliveira Costa', '741.852.963-20', '(47) 98444-4444', 'mariana.oliveira@exemplo.com', 'Sistemas de Informação', '5', NULL, 'ativo'),
('Pedro Santos', '456.789.123-00', '(47) 98555-5555', 'pedro.santos@univille.br', 'Administração', '3', NULL, 'ativo'),
('Rafael Almeida', '654.123.987-00', '(47) 98666-6666', 'rafael.almeida@univille.br', 'Engenharia de Software', '6', NULL, 'ativo'),
('Roberto Mendes', '147.258.369-00', '(47) 98777-7777', 'roberto.m@univille.br', 'Engenharia de Software', '8', 'Formado.', 'inativo');

-- Atendimentos de exemplo (usuario_id = 1 = Administrador logado na sessão)
INSERT INTO atendimentos
    (pessoa_id, tipo_atendimento_id, usuario_id, descricao, status, data_atendimento, horario_atendimento, observacao_final)
VALUES
    (8,  1, 1, 'Solicitação de acesso ao laboratório de redes.', 'aberto',       '2026-06-07', '09:00:00', NULL),
    (1,  3, 1, 'Dúvida sobre a disciplina de Banco de Dados.',   'em_andamento', '2026-06-07', '10:30:00', NULL),
    (14, 6, 1, 'Orientação sobre entrega do TCC.',                'concluido',    '2026-06-06', '14:00:00', 'Orientação concluída, aluno seguirá o cronograma revisado.'),
    (10, 4, 1, 'Justificativa de falta por atestado médico.',     'aberto',       '2026-06-06', '11:15:00', NULL),
    (6,  5, 1, 'Solicitação de histórico escolar.',                'concluido',    '2026-06-05', '08:45:00', 'Documento emitido e enviado por e-mail.'),
    (11, 8, 1, 'Problema de acesso ao sistema acadêmico.',         'em_andamento', '2026-06-05', '13:20:00', NULL);

-- =====================================================================
-- Validação rápida após importar
-- =====================================================================
-- SELECT * FROM usuarios;
-- SELECT * FROM tipos_atendimentos;
-- SELECT * FROM pessoas;
-- SELECT * FROM atendimentos;
