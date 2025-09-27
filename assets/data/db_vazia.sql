-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: sge-db
-- Tempo de geração: 27/09/2025 às 20:15
-- Versão do servidor: 9.4.0
-- Versão do PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sge_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
                                `id` int NOT NULL,
                                `usuario_id` int NOT NULL,
                                `titulo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                                `tipo_agendamento` enum('esportivo','nao_esportivo') COLLATE utf8mb4_general_ci NOT NULL,
                                `esporte_tipo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
                                `data_agendamento` date NOT NULL,
                                `periodo` enum('primeiro','segundo') COLLATE utf8mb4_general_ci NOT NULL COMMENT 'primeiro: 19:15-20:55, segundo: 21:10-22:50',
                                `descricao` text COLLATE utf8mb4_general_ci,
                                `status` enum('aprovado','pendente','rejeitado','cancelado') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendente',
                                `motivo_rejeicao` text COLLATE utf8mb4_general_ci,
                                `data_solicitacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                `atletica_confirmada` tinyint(1) NOT NULL DEFAULT '0',
                                `atletica_id_confirmada` int DEFAULT NULL,
                                `quantidade_atletica` int DEFAULT '0',
                                `quantidade_pessoas` int DEFAULT '0',
                                `subtipo_evento` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'treino/campeonato para esportivos, palestra/workshop/formatura para nao_esportivos',
                                `responsavel_evento` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
                                `possui_materiais` tinyint(1) DEFAULT NULL COMMENT '1=sim, 0=não',
                                `materiais_necessarios` text COLLATE utf8mb4_general_ci,
                                `responsabiliza_devolucao` tinyint(1) DEFAULT NULL,
                                `lista_participantes` text COLLATE utf8mb4_general_ci,
                                `arquivo_participantes` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
                                `arbitro_partida` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
                                `estimativa_participantes` int DEFAULT NULL,
                                `evento_aberto_publico` tinyint(1) DEFAULT NULL COMMENT '1=sim, 0=não',
                                `descricao_publico_alvo` text COLLATE utf8mb4_general_ci,
                                `infraestrutura_adicional` text COLLATE utf8mb4_general_ci,
                                `observacoes` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atleticas`
--

CREATE TABLE `atleticas` (
                             `id` int NOT NULL,
                             `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                             `descricao` text COLLATE utf8mb4_general_ci,
                             `logo_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atleticas`
--

INSERT INTO `atleticas` (`id`, `nome`, `descricao`, `logo_url`) VALUES
    (1, 'Galinhas Cyberneticas', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
                          `id` int NOT NULL,
                          `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                          `atletica_id` int DEFAULT NULL,
                          `coordenador_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nome`, `atletica_id`, `coordenador_id`) VALUES
    (1, 'Engenharia de Software', 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipes`
--

CREATE TABLE `equipes` (
                           `id` int NOT NULL,
                           `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                           `modalidade_id` int NOT NULL,
                           `atletica_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipe_membros`
--

CREATE TABLE `equipe_membros` (
                                  `id` int NOT NULL,
                                  `equipe_id` int NOT NULL,
                                  `aluno_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
                           `id` int NOT NULL,
                           `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                           `data_inicio` date NOT NULL,
                           `data_fim` date NOT NULL,
                           `ativo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscricoes_eventos`
--

CREATE TABLE `inscricoes_eventos` (
                                      `id` int NOT NULL,
                                      `aluno_id` int NOT NULL,
                                      `evento_id` int NOT NULL,
                                      `atletica_id` int NOT NULL,
                                      `status` enum('pendente','aprovado','recusado') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aprovado',
                                      `data_inscricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                      `observacoes` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscricoes_modalidade`
--

CREATE TABLE `inscricoes_modalidade` (
                                         `id` int NOT NULL,
                                         `aluno_id` int NOT NULL,
                                         `modalidade_id` int NOT NULL,
                                         `atletica_id` int NOT NULL,
                                         `status` enum('pendente','aprovado','recusado') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendente',
                                         `data_inscricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidades`
--

CREATE TABLE `modalidades` (
                               `id` int NOT NULL,
                               `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `presencas`
--

CREATE TABLE `presencas` (
                             `id` int NOT NULL,
                             `usuario_id` int NOT NULL,
                             `agendamento_id` int NOT NULL,
                             `data_presenca` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores_cursos`
--

CREATE TABLE `professores_cursos` (
                                      `id` int NOT NULL,
                                      `professor_id` int NOT NULL,
                                      `curso_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
                            `id` int NOT NULL,
                            `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                            `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                            `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
                            `ra` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
                            `data_nascimento` date DEFAULT NULL,
                            `curso_id` int DEFAULT NULL,
                            `role` enum('usuario','admin','superadmin') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'usuario',
                            `atletica_id` int DEFAULT NULL,
                            `tipo_usuario_detalhado` enum('Membro das Atléticas','Professor','Aluno','Comunidade Externa') COLLATE utf8mb4_general_ci DEFAULT NULL,
                            `is_coordenador` tinyint(1) NOT NULL DEFAULT '0',
                            `atletica_join_status` enum('none','pendente','aprovado') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
                            `login_code` varchar(6) COLLATE utf8mb4_general_ci DEFAULT NULL,
                            `login_code_expires` datetime DEFAULT NULL,
                            `reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
                            `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ra`, `data_nascimento`, `curso_id`, `role`, `atletica_id`, `tipo_usuario_detalhado`, `is_coordenador`, `atletica_join_status`, `login_code`, `login_code_expires`, `reset_token`, `reset_token_expires`) VALUES
                                                                                                                                                                                                                                                                      (1, 'Super Admin', 'sadmin', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, NULL, NULL, 'superadmin', NULL, NULL, 0, 'none', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                      (3, 'ALUNO (TESTE)', '000001@unifio.edu.br', '$2y$10$rUz4Ne7K0zUpm6yaAsJhd.Eb0Zn6NHOzDpwo3BeVZLudOSkfTh.Yy', '000001', '0001-01-01', 1, 'usuario', NULL, 'Aluno', 0, 'none', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                      (4, 'MEMBRO DAS ATLETICAS (TESTE)', '000002@unifio.edu.br', '$2y$10$IRCmWw7KmarlrgNNwcwa..ulmrVTu9vnniMMEEnlxBhM4hnpoUWti', '000002', '0001-01-01', 1, 'usuario', 1, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                      (6, 'ADMIN DA ATLETICA', '000003@unifio.edu.br', '$2y$10$T67giIBSIqcFvFwUUDlv.eDlZhl5tZou27ht2F91Lv9AxyKYy.VYO', '000003', '0001-01-01', 1, 'admin', 1, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                      (7, 'COMUNIDADE EXTERNA (TESTE)', 'comunidadeexterna@teste.com', '$2y$10$PV38c/uzr.PSE0w4UOWIk.juFa70nRFcEPn7IDD8xlNRRY1Y4BWz2', NULL, '0001-01-01', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none', NULL, NULL, NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
    ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `atletica_id_confirmada` (`atletica_id_confirmada`);

--
-- Índices de tabela `atleticas`
--
ALTER TABLE `atleticas`
    ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `cursos`
--
ALTER TABLE `cursos`
    ADD PRIMARY KEY (`id`),
  ADD KEY `atletica_id` (`atletica_id`),
  ADD KEY `coordenador_id` (`coordenador_id`);

--
-- Índices de tabela `equipes`
--
ALTER TABLE `equipes`
    ADD PRIMARY KEY (`id`),
  ADD KEY `modalidade_id` (`modalidade_id`),
  ADD KEY `atletica_id` (`atletica_id`);

--
-- Índices de tabela `equipe_membros`
--
ALTER TABLE `equipe_membros`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipe_id` (`equipe_id`,`aluno_id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices de tabela `eventos`
--
ALTER TABLE `eventos`
    ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aluno_evento_unique` (`aluno_id`,`evento_id`),
  ADD KEY `idx_evento` (`evento_id`),
  ADD KEY `idx_atletica` (`atletica_id`),
  ADD KEY `idx_status` (`status`);

--
-- Índices de tabela `inscricoes_modalidade`
--
ALTER TABLE `inscricoes_modalidade`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `aluno_id` (`aluno_id`,`modalidade_id`),
  ADD KEY `modalidade_id` (`modalidade_id`),
  ADD KEY `atletica_id` (`atletica_id`);

--
-- Índices de tabela `modalidades`
--
ALTER TABLE `modalidades`
    ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `presencas`
--
ALTER TABLE `presencas`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`agendamento_id`),
  ADD KEY `agendamento_id` (`agendamento_id`);

--
-- Índices de tabela `professores_cursos`
--
ALTER TABLE `professores_cursos`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `professor_id` (`professor_id`,`curso_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ra` (`ra`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `atletica_id` (`atletica_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `atleticas`
--
ALTER TABLE `atleticas`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `equipes`
--
ALTER TABLE `equipes`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `equipe_membros`
--
ALTER TABLE `equipe_membros`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `inscricoes_modalidade`
--
ALTER TABLE `inscricoes_modalidade`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `presencas`
--
ALTER TABLE `presencas`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `professores_cursos`
--
ALTER TABLE `professores_cursos`
    MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
    MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
    ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`atletica_id_confirmada`) REFERENCES `atleticas` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `cursos`
--
ALTER TABLE `cursos`
    ADD CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`atletica_id`) REFERENCES `atleticas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cursos_ibfk_2` FOREIGN KEY (`coordenador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `equipes`
--
ALTER TABLE `equipes`
    ADD CONSTRAINT `equipes_ibfk_1` FOREIGN KEY (`modalidade_id`) REFERENCES `modalidades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipes_ibfk_2` FOREIGN KEY (`atletica_id`) REFERENCES `atleticas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `equipe_membros`
--
ALTER TABLE `equipe_membros`
    ADD CONSTRAINT `equipe_membros_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipe_membros_ibfk_2` FOREIGN KEY (`aluno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
    ADD CONSTRAINT `fk_inscricoes_eventos_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inscricoes_eventos_atletica` FOREIGN KEY (`atletica_id`) REFERENCES `atleticas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inscricoes_eventos_evento` FOREIGN KEY (`evento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `inscricoes_modalidade`
--
ALTER TABLE `inscricoes_modalidade`
    ADD CONSTRAINT `inscricoes_modalidade_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscricoes_modalidade_ibfk_2` FOREIGN KEY (`modalidade_id`) REFERENCES `modalidades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscricoes_modalidade_ibfk_3` FOREIGN KEY (`atletica_id`) REFERENCES `atleticas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `presencas`
--
ALTER TABLE `presencas`
    ADD CONSTRAINT `presencas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presencas_ibfk_2` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `professores_cursos`
--
ALTER TABLE `professores_cursos`
    ADD CONSTRAINT `professores_cursos_ibfk_1` FOREIGN KEY (`professor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `professores_cursos_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
    ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`atletica_id`) REFERENCES `atleticas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;