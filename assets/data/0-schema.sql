-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: sge-db
-- Tempo de geração: 27/09/2025 às 20:39
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
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo_agendamento` enum('esportivo','nao_esportivo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `esporte_tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_agendamento` date NOT NULL,
  `periodo` enum('primeiro','segundo') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'primeiro: 19:15-20:55, segundo: 21:10-22:50',
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `status` enum('aprovado','pendente','rejeitado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pendente',
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
  `observacoes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `usuario_id`, `titulo`, `tipo_agendamento`, `esporte_tipo`, `data_agendamento`, `periodo`, `descricao`, `status`, `motivo_rejeicao`, `data_solicitacao`, `atletica_confirmada`, `atletica_id_confirmada`, `quantidade_atletica`, `quantidade_pessoas`, `subtipo_evento`, `responsavel_evento`, `possui_materiais`, `materiais_necessarios`, `responsabiliza_devolucao`, `lista_participantes`, `arquivo_participantes`, `arbitro_partida`, `estimativa_participantes`, `evento_aberto_publico`, `descricao_publico_alvo`, `infraestrutura_adicional`, `observacoes`) VALUES
(1, 8, 'Treino Aberto de Futsal', 'esportivo', 'Futsal', '2025-10-03', 'primeiro', 'Treino aberto para seleção de novos atletas para a equipe dos Lobos.', 'aprovado', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'Ana Silva', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 9, 'Campeonato Interno de Vôlei', 'esportivo', 'Voleibol', '2025-10-10', 'primeiro', 'Fase de grupos do campeonato interno de vôlei das Serpentes.', 'aprovado', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'Bruno Costa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 12, 'Palestra: IA e o Futuro da Engenharia', 'nao_esportivo', NULL, '2025-10-05', 'segundo', 'Palestra com especialista da área sobre o impacto da Inteligência Artificial.', 'pendente', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'Eduarda Martins', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 11, 'Reunião de Planejamento da Atlética', 'nao_esportivo', NULL, '2025-09-30', 'segundo', 'Reunião para definir o calendário de eventos do próximo semestre.', 'rejeitado', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'Prof. Douglas Lima', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 17, 'Workshop de Finanças para Universitários', 'nao_esportivo', 'Workshop', '2025-10-15', 'primeiro', 'Workshop sobre investimentos e planejamento financeiro.', 'aprovado', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'Juliana Rocha', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 13, 'Seletiva de Atletismo', 'esportivo', 'Atletismo', '2025-10-01', 'primeiro', 'Seletiva para todas as modalidades de atletismo.', 'aprovado', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'Felipe Souza', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 19, 'Torneio de E-Sports (LoL)', 'esportivo', 'E-sports', '2025-10-18', 'segundo', 'Torneio de League of Legends entre as atléticas.', 'pendente', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'Lucas Andrade', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 16, 'Evento Beneficente Comunitário', 'nao_esportivo', 'Evento Social', '2025-11-01', 'primeiro', 'Arrecadação de alimentos e agasalhos para a comunidade local.', 'aprovado', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'Igor Almeida', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 6, 'Final do Campeonato de Basquete', 'esportivo', 'Basquetebol', '2025-11-15', 'primeiro', 'Grande final do campeonato inter-atléticas de basquete.', 'aprovado', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'ADMIN DA ATLETICA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 7, 'Aula de Yoga aberta ao público', 'nao_esportivo', 'Bem-estar', '2025-10-08', 'primeiro', 'Aula de yoga gratuita para alunos e comunidade externa.', 'pendente', NULL, '2025-09-27 20:37:08', 0, NULL, 0, 0, NULL, 'COMUNIDADE EXTERNA (TESTE)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `atleticas`
--

CREATE TABLE `atleticas` (
  `id` int NOT NULL,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `logo_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atleticas`
--

INSERT INTO `atleticas` (`id`, `nome`, `descricao`, `logo_url`) VALUES
(1, 'Galinhas Cyberneticas', NULL, NULL),
(2, 'Tubarões da Engenharia', 'Atlética dos cursos de Engenharia.', 'https://example.com/logo_tubaroes.png'),
(3, 'Serpentes do Direito', 'Atlética do curso de Direito.', 'https://example.com/logo_serpentes.png'),
(4, 'Corujas da Medicina', 'Atlética do curso de Medicina.', 'https://example.com/logo_corujas.png'),
(5, 'Lobos da Computação', 'Atlética dos cursos de TI.', 'https://example.com/logo_lobos.png'),
(6, 'Águias da Administração', 'Atlética do curso de Administração.', 'https://example.com/logo_aguias.png'),
(7, 'Leões da Educação Física', 'Atlética do curso de Educação Física.', 'https://example.com/logo_leoes.png'),
(8, 'Tigres da Comunicação', 'Atlética dos cursos de Comunicação.', 'https://example.com/logo_tigres.png'),
(9, 'Panteras da Psicologia', 'Atlética do curso de Psicologia.', 'https://example.com/logo_panteras.png'),
(10, 'ursos da Arquitetura', 'Atlética do curso de Arquitetura.', 'https://example.com/logo_ursos.png'),
(11, 'Raposas da Biologia', 'Atlética dos cursos de Ciências Biológicas.', 'https://example.com/logo_raposas.png');

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

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nome`, `atletica_id`, `coordenador_id`) VALUES
(1, 'Engenharia de Software', 1, NULL),
(2, 'Direito', 3, 18),
(3, 'Medicina', 4, NULL),
(4, 'Ciência da Computação', 5, NULL),
(5, 'Administração', 6, NULL),
(6, 'Educação Física', 7, NULL),
(7, 'Jornalismo', 8, NULL),
(8, 'Psicologia', 9, NULL),
(9, 'Arquitetura e Urbanismo', 10, NULL),
(10, 'Ciências Biológicas', 11, NULL),
(11, 'Engenharia Civil', 2, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipes`
--

CREATE TABLE `equipes` (
  `id` int NOT NULL,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `modalidade_id` int NOT NULL,
  `atletica_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `equipes`
--

INSERT INTO `equipes` (`id`, `nome`, `modalidade_id`, `atletica_id`) VALUES
(1, 'Lobos Futsal Masculino', 2, 5),
(2, 'Serpentes Vôlei Feminino', 3, 3),
(3, 'Tubarões Basquete', 4, 2),
(4, 'Corujas Handebol', 5, 4),
(5, 'Galinhas Cyber E-sports', 10, 1),
(6, 'Águias Tênis de Mesa', 8, 6),
(7, 'Leões Atletismo', 7, 7),
(8, 'Tigres Xadrez', 9, 8),
(9, 'Panteras Natação', 6, 9),
(10, 'Lobos Vôlei Masculino', 3, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipe_membros`
--

CREATE TABLE `equipe_membros` (
  `id` int NOT NULL,
  `equipe_id` int NOT NULL,
  `aluno_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `equipe_membros`
--

INSERT INTO `equipe_membros` (`id`, `equipe_id`, `aluno_id`) VALUES
(2, 1, 4),
(1, 1, 8),
(3, 2, 9),
(11, 2, 14),
(4, 3, 12),
(5, 4, 3),
(7, 5, 6),
(6, 5, 19),
(8, 6, 17),
(9, 7, 13),
(10, 10, 8);

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int NOT NULL,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `ativo` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id`, `nome`, `data_inicio`, `data_fim`, `ativo`) VALUES
(1, 'Jogos Intercursos 2025', '2025-10-20', '2025-10-25', 1),
(2, 'Semana Acadêmica de Tecnologia', '2025-11-03', '2025-11-07', 1),
(3, 'Feira de Profissões', '2025-09-29', '2025-09-29', 1),
(4, 'Festa de Halloween das Atléticas', '2025-10-31', '2025-10-31', 1),
(5, 'Campanha de Doação de Sangue', '2025-11-10', '2025-11-12', 1),
(6, 'Congresso de Direito Constitucional', '2025-11-18', '2025-11-20', 0),
(7, 'Maratona de Programação (Hackathon)', '2025-12-05', '2025-12-06', 1),
(8, 'Apresentação Cultural de Fim de Ano', '2025-12-10', '2025-12-10', 1),
(9, 'Copa UNIFIO de Futsal', '2026-03-15', '2026-03-22', 1),
(10, 'Simpósio de Saúde e Bem-estar', '2026-04-01', '2026-04-03', 1);

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

--
-- Despejando dados para a tabela `inscricoes_eventos`
--

INSERT INTO `inscricoes_eventos` (`id`, `aluno_id`, `evento_id`, `atletica_id`, `status`, `data_inscricao`, `observacoes`) VALUES
(1, 8, 1, 5, 'aprovado', '2025-09-27 20:37:08', NULL),
(2, 9, 2, 3, 'aprovado', '2025-09-27 20:37:08', NULL),
(3, 10, 2, 4, 'pendente', '2025-09-27 20:37:08', NULL),
(4, 12, 9, 2, 'aprovado', '2025-09-27 20:37:08', NULL),
(5, 13, 6, 7, 'aprovado', '2025-09-27 20:37:08', NULL),
(6, 19, 7, 1, 'aprovado', '2025-09-27 20:37:08', NULL),
(7, 17, 5, 6, 'aprovado', '2025-09-27 20:37:08', NULL),
(8, 16, 8, 2, 'aprovado', '2025-09-27 20:37:08', NULL),
(9, 3, 6, 4, 'aprovado', '2025-09-27 20:37:08', NULL),
(10, 4, 1, 5, 'recusado', '2025-09-27 20:37:08', NULL);

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

--
-- Despejando dados para a tabela `inscricoes_modalidade`
--

INSERT INTO `inscricoes_modalidade` (`id`, `aluno_id`, `modalidade_id`, `atletica_id`, `status`, `data_inscricao`) VALUES
(1, 8, 2, 5, 'aprovado', '2025-09-27 20:37:08'),
(2, 9, 3, 3, 'aprovado', '2025-09-27 20:37:08'),
(3, 10, 4, 4, 'pendente', '2025-09-27 20:37:08'),
(4, 12, 4, 2, 'aprovado', '2025-09-27 20:37:08'),
(5, 13, 7, 7, 'aprovado', '2025-09-27 20:37:08'),
(6, 14, 3, 3, 'recusado', '2025-09-27 20:37:08'),
(7, 19, 10, 1, 'aprovado', '2025-09-27 20:37:08'),
(8, 3, 5, 4, 'aprovado', '2025-09-27 20:37:08'),
(9, 4, 2, 5, 'pendente', '2025-09-27 20:37:08'),
(10, 6, 10, 1, 'aprovado', '2025-09-27 20:37:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidades`
--

CREATE TABLE `modalidades` (
  `id` int NOT NULL,
  `nome` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modalidades`
--

INSERT INTO `modalidades` (`id`, `nome`) VALUES
(1, 'Futebol de Campo'),
(2, 'Futsal'),
(3, 'Voleibol'),
(4, 'Basquetebol'),
(5, 'Handebol'),
(6, 'Natação'),
(7, 'Atletismo'),
(8, 'Tênis de Mesa'),
(9, 'Xadrez'),
(10, 'E-sports');

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

--
-- Despejando dados para a tabela `presencas`
--

INSERT INTO `presencas` (`id`, `usuario_id`, `agendamento_id`, `data_presenca`) VALUES
(1, 8, 1, '2025-09-27 20:37:08'),
(2, 4, 1, '2025-09-27 20:37:08'),
(3, 9, 2, '2025-09-27 20:37:08'),
(4, 14, 2, '2025-09-27 20:37:08'),
(5, 17, 5, '2025-09-27 20:37:08'),
(6, 10, 5, '2025-09-27 20:37:08'),
(7, 16, 5, '2025-09-27 20:37:08'),
(8, 13, 6, '2025-09-27 20:37:08'),
(9, 3, 6, '2025-09-27 20:37:08'),
(10, 16, 8, '2025-09-27 20:37:08'),
(11, 7, 8, '2025-09-27 20:37:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores_cursos`
--

CREATE TABLE `professores_cursos` (
  `id` int NOT NULL,
  `professor_id` int NOT NULL,
  `curso_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professores_cursos`
--

INSERT INTO `professores_cursos` (`id`, `professor_id`, `curso_id`) VALUES
(2, 11, 1),
(1, 11, 4),
(8, 11, 9),
(5, 11, 11),
(3, 15, 2),
(6, 15, 5),
(9, 15, 8),
(4, 18, 2),
(7, 18, 5),
(10, 18, 7);

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
  `curso_id` int DEFAULT NULL,
  `role` enum('usuario','admin','superadmin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'usuario',
  `atletica_id` int DEFAULT NULL,
  `tipo_usuario_detalhado` enum('Membro das Atléticas','Professor','Aluno','Comunidade Externa') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_coordenador` tinyint(1) NOT NULL DEFAULT '0',
  `atletica_join_status` enum('none','pendente','aprovado') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'none',
  `login_code` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `login_code_expires` datetime DEFAULT NULL,
  `reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
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
(7, 'COMUNIDADE EXTERNA (TESTE)', 'comunidadeexterna@teste.com', '$2y$10$PV38c/uzr.PSE0w4UOWIk.juFa70nRFcEPn7IDD8xlNRRY1Y4BWz2', NULL, '0001-01-01', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none', NULL, NULL, NULL, NULL),
(8, 'Ana Silva', '000004@unifio.edu.br', '$2y$10$...placeholder...', '000004', '2002-05-15', 4, 'usuario', 5, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(9, 'Bruno Costa', '000005@unifio.edu.br', '$2y$10$...placeholder...', '000005', '2001-08-20', 2, 'usuario', 3, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(10, 'Carla Dias', '000006@unifio.edu.br', '$2y$10$...placeholder...', '000006', '2003-01-30', 3, 'usuario', NULL, 'Aluno', 0, 'none', NULL, NULL, NULL, NULL),
(11, 'Prof. Douglas Lima', 'professor.douglas@unifio.edu.br', '$2y$10$...placeholder...', NULL, '1980-11-10', 4, 'usuario', NULL, 'Professor', 0, 'none', NULL, NULL, NULL, NULL),
(12, 'Eduarda Martins', '000007@unifio.edu.br', '$2y$10$...placeholder...', '000007', '2000-03-25', 11, 'admin', 2, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(13, 'Felipe Souza', '000008@unifio.edu.br', '$2y$10$...placeholder...', '000008', '2002-07-12', 6, 'usuario', 7, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(14, 'Gabriela Pereira', '000009@unifio.edu.br', '$2y$10$...placeholder...', '000009', '2004-09-05', 8, 'usuario', NULL, 'Aluno', 0, 'pendente', NULL, NULL, NULL, NULL),
(15, 'Prof. Helena Santos', 'professora.helena@unifio.edu.br', '$2y$10$...placeholder...', NULL, '1985-04-18', 2, 'usuario', NULL, 'Professor', 1, 'none', NULL, NULL, NULL, NULL),
(16, 'Igor Almeida', 'visitante.igor@email.com', '$2y$10$...placeholder...', NULL, '1995-02-22', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none', NULL, NULL, NULL, NULL),
(17, 'Juliana Rocha', '000010@unifio.edu.br', '$2y$10$...placeholder...', '000010', '2001-12-01', 5, 'admin', 6, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(18, 'Prof. Coordenador Direito', 'coordenador.direito@unifio.edu.br', '$2y$10$...placeholder...', NULL, '1975-06-09', 2, 'admin', NULL, 'Professor', 1, 'none', NULL, NULL, NULL, NULL),
(19, 'Lucas Andrade', '000011@unifio.edu.br', '$2y$10$...placeholder...', '000011', '2002-10-18', 1, 'usuario', 1, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `atleticas`
--
ALTER TABLE `atleticas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `equipes`
--
ALTER TABLE `equipes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `equipe_membros`
--
ALTER TABLE `equipe_membros`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `inscricoes_modalidade`
--
ALTER TABLE `inscricoes_modalidade`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `presencas`
--
ALTER TABLE `presencas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `professores_cursos`
--
ALTER TABLE `professores_cursos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
