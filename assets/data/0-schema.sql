-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: sge-db
-- Tempo de geração: 28/09/2025 às 03:46
-- Versão do servidor: 9.4.0
-- Versão do PHP: 8.2.27
-- MODIFICADO: Adicionado campo 'telefone' na tabela usuarios
-- MODIFICADO: Adicionada tabela 'notificacoes'
-- MODIFICADO: Adicionadas colunas para controle de edições/cancelamentos por admin na tabela agendamentos

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `application`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_agendamento` enum('esportivo','nao_esportivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `esporte_tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_agendamento` date NOT NULL,
  `periodo` enum('primeiro','segundo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'primeiro: 19:15-20:55, segundo: 21:10-22:50',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('aprovado','pendente','rejeitado','cancelado','finalizado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendente',
  `motivo_rejeicao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `data_solicitacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atletica_confirmada` tinyint(1) NOT NULL DEFAULT '0',
  `atletica_id_confirmada` int DEFAULT NULL,
  `quantidade_atletica` int DEFAULT '0',
  `quantidade_pessoas` int DEFAULT '0',
  `subtipo_evento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'treino/campeonato para esportivos, palestra/workshop/formatura para nao_esportivos',
  `responsavel_evento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `possui_materiais` tinyint(1) DEFAULT NULL COMMENT '1=sim, 0=não',
  `materiais_necessarios` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `responsabiliza_devolucao` tinyint(1) DEFAULT NULL,
  `lista_participantes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `arquivo_participantes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `arbitro_partida` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estimativa_participantes` int DEFAULT NULL,
  `evento_aberto_publico` tinyint(1) DEFAULT NULL COMMENT '1=sim, 0=não',
  `descricao_publico_alvo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `infraestrutura_adicional` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `foi_editado` tinyint(1) DEFAULT 0 COMMENT 'Indica se o agendamento foi editado após criação',
  `data_edicao` datetime DEFAULT NULL COMMENT 'Data e hora da última edição',
  `observacoes_admin` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Observações adicionadas pelo admin ao editar o evento',
  `data_ultima_alteracao` datetime DEFAULT NULL COMMENT 'Data da última alteração feita pelo admin',
  `alterado_por_admin` tinyint(1) DEFAULT 0 COMMENT 'Indica se foi alterado por um admin',
  `data_cancelamento` datetime DEFAULT NULL COMMENT 'Data do cancelamento pelo admin',
  `cancelado_por_admin` tinyint(1) DEFAULT 0 COMMENT 'Indica se foi cancelado por um admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atleticas`
--

CREATE TABLE `atleticas` (
  `id` int NOT NULL,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int NOT NULL,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `atletica_id` int DEFAULT NULL,
  `coordenador_id` int DEFAULT NULL
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
  `status` enum('pendente','aprovado','recusado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aprovado',
  `data_inscricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
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
  `status` enum('pendente','aprovado','recusado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendente',
  `data_inscricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidades`
--

CREATE TABLE `modalidades` (
  `id` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
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
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ra` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `curso_id` int DEFAULT NULL,
  `role` enum('usuario','admin','superadmin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'usuario',
  `atletica_id` int DEFAULT NULL,
  `tipo_usuario_detalhado` enum('Membro das Atleticas','Professor','Aluno','Comunidade Externa') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_coordenador` tinyint(1) NOT NULL DEFAULT '0',
  `atletica_join_status` enum('none','pendente','aprovado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `login_code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `login_code_expires` datetime DEFAULT NULL,
  `reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mensagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('agendamento_aprovado','agendamento_rejeitado','agendamento_cancelado','agendamento_cancelado_admin','agendamento_editado','agendamento_alterado','presenca_confirmada','lembrete_evento','info','aviso','sistema') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `agendamento_id` int DEFAULT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ra` (`ra`),
  ADD KEY `curso_id` (`curso_id`),
  ADD KEY `atletica_id` (`atletica_id`);

--
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `agendamento_id` (`agendamento_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

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
-- Restrições para tabelas `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`atletica_id`) REFERENCES `atleticas` (`id`);

--
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_ibfk_2` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
