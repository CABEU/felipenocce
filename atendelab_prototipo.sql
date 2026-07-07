-- =====================================================================
-- AtendeLab - Sistema de Controle de Atendimentos Acadêmicos
-- Script de banco de dados: database/atendelab.sql
-- Versão reduzida (protótipo): apenas 3 pessoas cadastradas.
-- Campos oficiais: id, pessoa_id, tipo_atendimento_id, usuario_id,
-- data_atendimento, documento (NÃO usar cpf, id_pessoa, id_usuario, data_hora)
--
-- OBS: Todos os nomes de pessoas/usuários usam apenas variações de
-- "Felipe Nocce Lucena", conforme solicitado.
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
--
-- Todos os nomes abaixo (usuários e pessoas) usam apenas variações de
-- "Felipe Nocce Lucena". Versão reduzida: 3 pessoas apenas.
-- =====================================================================

INSERT INTO usuarios (nome, email, senha, perfil, status) VALUES
('Felipe Nocce Lucena', 'admin@atendelab.com',
 '$2b$10$06XgmhXn7UzGZ6RLrp5apeDa5eTZfY.uBs87s8azgTPUEHbHfrs5O',
 'admin', 'ativo'),
('Felipe Lucena', 'atendente@atendelab.com',
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
('Felipe Nocce Lucena', '987.654.321-00', '(47) 99111-1111', 'felipe.nocce.lucena1@univille.br', 'Sistemas de Informação', '7', NULL, 'ativo'),
('Felipe Lucena', '123.123.132-00', '(47) 99222-2222', 'felipe.lucena2@email.com', 'Engenharia de Software', '5', NULL, 'ativo'),
('Felipe Nocce', '159.357.486-00', '(47) 99333-3333', 'felipe.nocce3@univille.br', 'Direito', '5', 'Formado.', 'inativo');

-- Atendimentos de exemplo (usuario_id = 1 = Felipe Nocce Lucena logado na sessão)
INSERT INTO atendimentos
    (pessoa_id, tipo_atendimento_id, usuario_id, descricao, status, data_atendimento, horario_atendimento, observacao_final)
VALUES
    (1, 1, 1, 'Solicitação de acesso ao laboratório de redes.', 'aberto',       '2026-06-07', '09:00:00', NULL),
    (2, 3, 1, 'Dúvida sobre a disciplina de Banco de Dados.',   'em_andamento', '2026-06-07', '10:30:00', NULL),
    (1, 6, 1, 'Orientação sobre entrega do TCC.',                'concluido',    '2026-06-06', '14:00:00', 'Orientação concluída, aluno seguirá o cronograma revisado.'),
    (3, 4, 1, 'Justificativa de falta por atestado médico.',     'aberto',       '2026-06-06', '11:15:00', NULL),
    (2, 5, 1, 'Solicitação de histórico escolar.',                'concluido',    '2026-06-05', '08:45:00', 'Documento emitido e enviado por e-mail.'),
    (3, 8, 1, 'Problema de acesso ao sistema acadêmico.',         'em_andamento', '2026-06-05', '13:20:00', NULL);

-- =====================================================================
-- Validação rápida após importar
-- =====================================================================
-- SELECT * FROM usuarios;
-- SELECT * FROM tipos_atendimentos;
-- SELECT * FROM pessoas;
-- SELECT * FROM atendimentos;
