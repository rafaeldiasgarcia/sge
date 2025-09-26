--
-- Este arquivo SQL é executado automaticamente pelo Docker na primeira vez que o contêiner do banco de dados é criado.
-- Ele define a estrutura de todas as tabelas e insere os dados iniciais necessários para a aplicação funcionar.
-- O nome do banco de dados foi alterado de 'sga_db' para 'sge_db'.
--

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20/09/2025 às 21:38
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

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
CREATE DATABASE IF NOT EXISTS `sge_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sge_db`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `tipo_agendamento` enum('esportivo','nao_esportivo') NOT NULL,
  `esporte_tipo` varchar(100) DEFAULT NULL,
  `data_agendamento` date NOT NULL,
  `periodo` enum('primeiro','segundo','manha','tarde','noite') NOT NULL COMMENT 'primeiro: 19:15-20:55, segundo: 21:10-22:50',
  `descricao` text DEFAULT NULL,
  `status` enum('aprovado','pendente','rejeitado','cancelado') NOT NULL DEFAULT 'pendente',
  `motivo_rejeicao` text DEFAULT NULL,
  `data_solicitacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `atletica_confirmada` tinyint(1) NOT NULL DEFAULT 0,
  `atletica_id_confirmada` int(11) DEFAULT NULL,
  `quantidade_atletica` int(11) DEFAULT 0,
  `quantidade_pessoas` int(11) DEFAULT 0,
  `subtipo_evento` varchar(100) DEFAULT NULL COMMENT 'treino/campeonato para esportivos, palestra/workshop/formatura para nao_esportivos',
  `responsavel_evento` varchar(255) NOT NULL DEFAULT '',
  `possui_materiais` tinyint(1) DEFAULT NULL COMMENT '1=sim, 0=não',
  `materiais_necessarios` text DEFAULT NULL,
  `responsabiliza_devolucao` tinyint(1) DEFAULT NULL,
  `lista_participantes` text DEFAULT NULL,
  `arquivo_participantes` varchar(255) DEFAULT NULL,
  `arbitro_partida` varchar(255) DEFAULT NULL,
  `estimativa_participantes` int(11) DEFAULT NULL,
  `evento_aberto_publico` tinyint(1) DEFAULT NULL COMMENT '1=sim, 0=não',
  `descricao_publico_alvo` text DEFAULT NULL,
  `infraestrutura_adicional` text DEFAULT NULL,
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `usuario_id`, `titulo`, `tipo_agendamento`, `esporte_tipo`, `data_agendamento`, `periodo`, `descricao`, `status`, `motivo_rejeicao`, `data_solicitacao`, `atletica_confirmada`, `atletica_id_confirmada`, `quantidade_atletica`, `quantidade_pessoas`, `subtipo_evento`, `responsavel_evento`, `possui_materiais`, `materiais_necessarios`, `responsabiliza_devolucao`, `lista_participantes`, `arquivo_participantes`, `arbitro_partida`, `estimativa_participantes`, `evento_aberto_publico`, `descricao_publico_alvo`, `infraestrutura_adicional`, `observacoes`) VALUES
(1, 3, 'Treino de Futsal - A.T.I', 'esportivo', 'Futsal', '2025-09-20', 'primeiro', 'Treino preparatório para o campeonato intercursos.', 'aprovado', NULL, '2025-09-16 11:46:59', 1, 2, 1, 15, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 4, 'Treino de Vôlei - Faísca', 'esportivo', 'Vôlei', '2025-09-22', 'segundo', 'Treino focado em saque e recepção.', 'aprovado', NULL, '2025-09-16 11:46:59', 1, 1, 1, 12, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 13, 'Reunião do Centro Acadêmico', 'nao_esportivo', NULL, '2025-09-25', 'primeiro', 'Discussão sobre as próximas atividades do CA.', 'aprovado', NULL, '2025-09-16 11:46:59', 0, NULL, 0, 20, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 5, 'Amistoso de Basquete: Direito x Adm', 'esportivo', 'Basquete', '2025-10-01', 'segundo', 'Amistoso entre a Atlética de Direito e a de Administração.', 'aprovado', NULL, '2025-09-16 11:46:59', 1, 3, 2, 25, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 6, 'Treino de Handebol - Med', 'esportivo', 'Handebol', '2025-09-28', 'primeiro', 'Treino de ataque e defesa.', 'aprovado', NULL, '2025-09-16 11:46:59', 1, 4, 1, 18, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 8, 'Encontro de Tênis de Mesa', 'esportivo', 'Tênis de Mesa', '2025-09-30', 'segundo', 'Evento aberto para todos os amantes de tênis de mesa.', 'aprovado', NULL, '2025-09-16 11:46:59', 0, NULL, 0, 10, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 3, 'Campeonato Interno de Futebol A.T.I', 'esportivo', 'Futebol', '2025-10-10', 'primeiro', 'Início do campeonato interno da A.T.I.', 'aprovado', NULL, '2025-09-16 11:46:59', 1, 2, 1, 30, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 15, 'Palestra sobre Nutrição Esportiva', 'nao_esportivo', NULL, '2025-10-05', 'segundo', 'Palestra com nutricionista convidado.', 'aprovado', NULL, '2025-09-16 11:46:59', 0, NULL, 0, 50, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 4, 'Seletiva de Atletismo - Faísca', 'esportivo', 'Atletismo', '2025-10-12', 'primeiro', 'Seletiva para novos membros da equipe de atletismo.', 'rejeitado', 'd', '2025-09-16 11:46:59', 1, 1, 1, 8, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 5, 'Festa de Integração das Atléticas', 'nao_esportivo', NULL, '2025-10-18', 'segundo', 'Festa para celebrar o início dos jogos universitários.', 'rejeitado', NULL, '2025-09-16 11:46:59', 0, NULL, 0, 100, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 12, 'Jogos Universitários de Outono 2025', 'esportivo', NULL, '2025-10-20', 'primeiro', 'Evento principal com várias modalidades.', 'aprovado', NULL, '2025-09-16 11:46:59', 1, 10, 10, 200, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 17, 'TESTE', 'esportivo', 'atletismo', '2025-09-21', 'primeiro', NULL, 'aprovado', NULL, '2025-09-16 18:10:02', 0, NULL, 0, NULL, 'campeonato', 'TESTE1', 0, 'TESTE2', 1, '012345\r\n567890\r\n234567\r\n123456', NULL, 'TESTE3', NULL, NULL, NULL, NULL, 'TESTE4'),
(13, 17, 'TESTE', 'nao_esportivo', NULL, '2025-10-01', 'primeiro', NULL, 'aprovado', NULL, '2025-09-18 00:31:22', 0, NULL, 0, NULL, 'seminario', 'TESTE', NULL, NULL, NULL, NULL, NULL, NULL, 10, 1, '', '', ''),
(14, 1, 'Torneio de Futebol Universitário', 'esportivo', 'Futebol', '2025-09-10', 'manha', 'Torneio entre faculdades da região', 'aprovado', NULL, '2025-08-15 13:00:00', 0, NULL, 0, 22, 'competicao', 'João Silva', 1, 'Bolas, coletes, apitos', 1, 'Lista anexada', NULL, 'Árbitro profissional', NULL, NULL, NULL, NULL, NULL),
(15, 2, 'Palestra: Nutrição no Esporte', 'nao_esportivo', NULL, '2025-09-05', 'noite', 'Palestra sobre alimentação saudável para atletas', 'aprovado', NULL, '2025-08-20 17:30:00', 0, NULL, 0, 50, NULL, 'Dra. Maria Santos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 3, 'Campeonato Interno de Basquete', 'esportivo', 'Basquete', '2025-09-12', 'tarde', 'Campeonato entre turmas do curso', 'aprovado', NULL, '2025-08-25 19:45:00', 0, NULL, 0, 10, 'competicao', 'Pedro Costa', 1, 'Bolas de basquete, cronômetro', 1, 'Participantes confirmados', NULL, 'Professor de Educação Física', NULL, NULL, NULL, NULL, NULL),
(17, 4, 'Workshop: Primeiros Socorros no Esporte', 'nao_esportivo', NULL, '2025-09-08', 'manha', 'Treinamento básico de primeiros socorros', 'aprovado', NULL, '2025-08-18 12:15:00', 0, NULL, 0, 30, NULL, 'Enfermeiro Carlos Lima', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 5, 'Treino Preparatório - Vôlei Feminino', 'esportivo', 'Vôlei', '2025-09-15', 'tarde', 'Treino preparatório para competições', 'aprovado', NULL, '2025-09-01 14:20:00', 0, NULL, 0, 12, 'treino', 'Ana Oliveira', 1, 'Bolas de vôlei, rede', 1, 'Equipe feminina', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 1, 'qwe', 'nao_esportivo', NULL, '2025-09-01', 'primeiro', NULL, 'aprovado', NULL, '2025-09-20 19:02:56', 0, NULL, 0, NULL, 'palestra', 'qwe', NULL, NULL, NULL, NULL, NULL, NULL, 123, 1, '', 'qwe', 'qwe'),
(20, 1, '123', 'nao_esportivo', NULL, '2025-09-01', 'segundo', NULL, 'aprovado', NULL, '2025-09-20 19:03:41', 0, NULL, 0, NULL, 'palestra', '123', NULL, NULL, NULL, NULL, NULL, NULL, 123, 0, '123', '123', '123'),
(21, 1, '234', 'nao_esportivo', NULL, '2025-09-24', 'primeiro', 'Descrição do evento 234', 'aprovado', NULL, '2025-09-20 19:22:06', 0, NULL, 0, NULL, 'workshop', '234', NULL, NULL, NULL, NULL, NULL, NULL, 234, 1, '', '234', '234');

-- --------------------------------------------------------

--
-- Estrutura para tabela `atleticas`
--

CREATE TABLE `atleticas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atleticas`
--

INSERT INTO `atleticas` (`id`, `nome`, `descricao`, `logo_url`) VALUES
(1, 'Atlética de Engenharia Elétrica - Faísca', 'A atlética mais energizada do campus.', 'https://example.com/logo_eletrica.png'),
(2, 'Atlética de Ciência da Computação - A.T.I', 'Conectando mentes e promovendo o esporte.', 'https://example.com/logo_comp.png'),
(3, 'Atlética de Direito - Lex', 'Pela honra e pela glória do esporte e da justiça.', 'https://example.com/logo_direito.png'),
(4, 'Atlética de Medicina - Med', 'Saúde em primeiro lugar, dentro e fora das quadras.', 'https://example.com/logo_medicina.png'),
(5, 'Atlética de Arquitetura e Urbanismo - Traço', 'Construindo vitórias e grandes amizades.', 'https://example.com/logo_arq.png'),
(6, 'Atlética de Psicologia - Psique', 'Mente sã, corpo são e muita garra no esporte.', 'https://example.com/logo_psico.png'),
(7, 'Atlética de Educação Física - Movimento', 'O corpo alcança o que a mente acredita.', 'https://example.com/logo_edfisica.png'),
(8, 'Atlética de Relações Internacionais - Diplomacia', 'Unindo nações através do esporte.', 'https://example.com/logo_ri.png'),
(9, 'Atlética de Engenharia Civil - Concreta', 'Fortes como concreto, unidos pela vitória.', 'https://example.com/logo_civil.png'),
(10, 'Atlética de Administração - Gestores', 'Planejando o sucesso, executando a vitória.', 'https://example.com/logo_adm.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `atletica_id` int(11) DEFAULT NULL,
  `coordenador_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`id`, `nome`, `atletica_id`, `coordenador_id`) VALUES
(1, 'Engenharia Elétrica', 1, 13),
(2, 'Ciência da Computação', 2, 14),
(3, 'Direito', 3, NULL),
(4, 'Medicina', 4, NULL),
(5, 'Arquitetura e Urbanismo', 5, NULL),
(6, 'Psicologia', 6, NULL),
(7, 'Educação Física', 7, NULL),
(8, 'Relações Internacionais', 8, NULL),
(9, 'Engenharia Civil', 9, NULL),
(10, 'Administração', 10, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipes`
--

CREATE TABLE `equipes` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `modalidade_id` int(11) NOT NULL,
  `atletica_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `equipes`
--

INSERT INTO `equipes` (`id`, `nome`, `modalidade_id`, `atletica_id`) VALUES
(1, 'A.T.I Futsal Masculino', 1, 2),
(2, 'Faísca Vôlei Feminino', 2, 1),
(3, 'Lex Basquete', 3, 3),
(4, 'Med Handebol', 4, 4),
(5, 'Psique Tênis de Mesa', 6, 6),
(6, 'A.T.I Atletismo', 8, 2),
(7, 'Gestores Futebol de Campo', 5, 10),
(8, 'Movimento Vôlei Masculino', 2, 7),
(9, 'Concreta Futsal Feminino', 1, 9),
(10, 'Traço Basquete', 3, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipe_membros`
--

CREATE TABLE `equipe_membros` (
  `id` int(11) NOT NULL,
  `equipe_id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `equipe_membros`
--

INSERT INTO `equipe_membros` (`id`, `equipe_id`, `aluno_id`) VALUES
(1, 1, 3),
(2, 2, 4),
(7, 2, 10),
(3, 3, 5),
(8, 3, 12),
(4, 4, 6),
(5, 5, 8),
(9, 6, 3),
(10, 7, 12);

-- --------------------------------------------------------

--
-- Estrutura para tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `eventos`
--

INSERT INTO `eventos` (`id`, `nome`, `data_inicio`, `data_fim`, `ativo`) VALUES
(1, 'Jogos Universitários de Outono 2025', '2025-10-20', '2025-10-28', 1),
(2, 'Copa de Futsal Interatléticas', '2025-11-05', '2025-11-15', 1),
(3, 'Festival de Verão de Esportes de Praia', '2026-01-15', '2026-01-20', 1),
(4, 'Semana da Saúde e Bem-estar', '2025-09-22', '2025-09-26', 1),
(5, 'Campeonato de E-Sports', '2025-11-20', '2025-11-22', 1),
(6, 'Olimpíadas Internas', '2026-03-10', '2026-03-20', 1),
(7, 'Torneio de Tênis de Mesa', '2025-10-05', '2025-10-05', 1),
(8, 'Corrida Rústica Universitária', '2025-12-07', '2025-12-07', 1),
(9, 'Festival Cultural e Esportivo', '2026-04-18', '2026-04-21', 1),
(10, 'Jogos de Inverno', '2026-07-10', '2026-07-18', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscricoes_eventos`
--

CREATE TABLE `inscricoes_eventos` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `atletica_id` int(11) NOT NULL,
  `status` enum('pendente','aprovado','recusado') NOT NULL DEFAULT 'aprovado',
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp(),
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `inscricoes_eventos`
--

INSERT INTO `inscricoes_eventos` (`id`, `aluno_id`, `evento_id`, `atletica_id`, `status`, `data_inscricao`, `observacoes`) VALUES
(2, 4, 11, 1, 'aprovado', '2025-09-16 11:46:59', 'Inscrição para Vôlei.'),
(3, 5, 11, 3, 'aprovado', '2025-09-16 11:46:59', 'Inscrição para Basquete.'),
(4, 6, 11, 4, 'pendente', '2025-09-16 11:46:59', 'Aguardando confirmação da equipe de Handebol.'),
(5, 8, 11, 6, 'aprovado', '2025-09-16 11:46:59', 'Inscrição para Tênis de Mesa.'),
(6, 3, 7, 2, 'aprovado', '2025-09-16 11:46:59', 'Capitão da equipe de Futsal da A.T.I.'),
(7, 5, 4, 3, 'recusado', '2025-09-16 11:46:59', 'Inscrição duplicada.'),
(8, 4, 8, 1, 'aprovado', '2025-09-16 11:46:59', 'Participação na palestra de abertura.'),
(9, 6, 8, 4, 'aprovado', '2025-09-16 11:46:59', NULL),
(10, 9, 11, 7, 'aprovado', '2025-09-16 11:46:59', 'Inscrição para Natação.'),
(11, 10, 11, 8, 'pendente', '2025-09-16 11:46:59', 'Aguardando aprovação na atlética para confirmar inscrição no evento.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `inscricoes_modalidade`
--

CREATE TABLE `inscricoes_modalidade` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `modalidade_id` int(11) NOT NULL,
  `atletica_id` int(11) NOT NULL,
  `status` enum('pendente','aprovado','recusado') NOT NULL DEFAULT 'pendente',
  `data_inscricao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `inscricoes_modalidade`
--

INSERT INTO `inscricoes_modalidade` (`id`, `aluno_id`, `modalidade_id`, `atletica_id`, `status`, `data_inscricao`) VALUES
(1, 3, 1, 2, 'aprovado', '2025-09-16 11:46:59'),
(2, 4, 2, 1, 'aprovado', '2025-09-16 11:46:59'),
(3, 5, 3, 3, 'aprovado', '2025-09-16 11:46:59'),
(4, 6, 4, 4, 'aprovado', '2025-09-16 11:46:59'),
(5, 7, 5, 5, 'pendente', '2025-09-16 11:46:59'),
(6, 8, 6, 6, 'aprovado', '2025-09-16 11:46:59'),
(7, 3, 8, 2, 'aprovado', '2025-09-16 11:46:59'),
(8, 4, 7, 1, 'pendente', '2025-09-16 11:46:59'),
(9, 9, 7, 7, 'aprovado', '2025-09-16 11:46:59'),
(10, 10, 2, 8, 'pendente', '2025-09-16 11:46:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `modalidades`
--

CREATE TABLE `modalidades` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `modalidades`
--

INSERT INTO `modalidades` (`id`, `nome`) VALUES
(1, 'Futsal'),
(2, 'Vôlei'),
(3, 'Basquete'),
(4, 'Handebol'),
(5, 'Futebol'),
(6, 'Tênis de Mesa'),
(7, 'Natação'),
(8, 'Atletismo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `presencas`
--

CREATE TABLE `presencas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `agendamento_id` int(11) NOT NULL,
  `data_presenca` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `presencas`
--

INSERT INTO `presencas` (`id`, `usuario_id`, `agendamento_id`, `data_presenca`) VALUES
(1, 3, 1, '2025-09-16 11:46:59'),
(2, 4, 2, '2025-09-16 11:46:59'),
(3, 5, 4, '2025-09-16 11:46:59'),
(4, 12, 4, '2025-09-16 11:46:59'),
(5, 6, 5, '2025-09-16 11:46:59'),
(6, 8, 6, '2025-09-16 11:46:59'),
(7, 3, 7, '2025-09-16 11:46:59'),
(8, 15, 8, '2025-09-16 11:46:59'),
(9, 4, 1, '2025-09-16 11:46:59'),
(10, 5, 1, '2025-09-16 11:46:59'),
(13, 1, 5, '2025-09-16 12:56:45'),
(14, 1, 7, '2025-09-16 12:56:47'),
(15, 1, 11, '2025-09-16 12:56:51'),
(17, 1, 1, '2025-09-16 14:02:19'),
(18, 1, 2, '2025-09-16 14:02:20'),
(24, 16, 4, '2025-09-16 16:57:10'),
(25, 16, 3, '2025-09-16 16:57:32'),
(26, 16, 8, '2025-09-16 16:57:33'),
(27, 16, 7, '2025-09-16 17:15:33'),
(28, 16, 11, '2025-09-16 17:15:35'),
(30, 17, 8, '2025-09-16 17:19:35'),
(31, 17, 2, '2025-09-16 17:19:36'),
(32, 17, 7, '2025-09-16 17:19:37'),
(33, 17, 11, '2025-09-16 17:19:39'),
(34, 17, 3, '2025-09-16 23:09:16'),
(35, 17, 1, '2025-09-16 23:09:21'),
(36, 16, 12, '2025-09-17 00:23:47'),
(37, 1, 14, '2025-09-18 01:10:30'),
(38, 2, 15, '2025-09-18 01:10:30'),
(39, 1, 16, '2025-09-18 01:10:30'),
(40, 3, 17, '2025-09-18 01:10:30'),
(41, 2, 18, '2025-09-18 01:10:30'),
(47, 1, 8, '2025-09-20 19:23:15'),
(48, 1, 3, '2025-09-20 19:23:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores_cursos`
--

CREATE TABLE `professores_cursos` (
  `id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professores_cursos`
--

INSERT INTO `professores_cursos` (`id`, `professor_id`, `curso_id`) VALUES
(1, 13, 1),
(5, 13, 4),
(7, 13, 6),
(9, 13, 8),
(2, 13, 9),
(3, 14, 2),
(4, 14, 3),
(6, 14, 5),
(8, 14, 7),
(10, 14, 10);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `ra` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL,
  `role` enum('usuario','admin','superadmin') NOT NULL DEFAULT 'usuario',
  `atletica_id` int(11) DEFAULT NULL,
  `tipo_usuario_detalhado` enum('Membro das Atléticas','Professor','Aluno','Comunidade Externa') DEFAULT NULL,
  `is_coordenador` tinyint(1) NOT NULL DEFAULT 0,
  `atletica_join_status` enum('none','pendente','aprovado') NOT NULL DEFAULT 'none',
  `login_code` varchar(6) DEFAULT NULL,
  `login_code_expires` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ra`, `data_nascimento`, `curso_id`, `role`, `atletica_id`, `tipo_usuario_detalhado`, `is_coordenador`, `atletica_join_status`, `login_code`, `login_code_expires`, `reset_token`, `reset_token_expires`) VALUES
(1, 'Super Admin', 'super@admin.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', NULL, NULL, NULL, 'superadmin', NULL, NULL, 0, 'none', NULL, NULL, NULL, NULL),
(2, 'Admin Geral', 'admin@sge.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', NULL, '1990-01-01', NULL, 'admin', NULL, NULL, 0, 'none', NULL, NULL, NULL, NULL),
(3, 'João Silva', 'joao.silva@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '123456', '2002-05-10', 2, 'usuario', 2, 'Aluno', 0, 'aprovado', NULL, NULL, NULL, NULL),
(4, 'Maria Oliveira', 'maria.oliveira@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '234567', '2001-08-22', 1, 'usuario', 1, 'Aluno', 0, 'aprovado', NULL, NULL, NULL, NULL),
(5, 'Pedro Martins', 'pedro.martins@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '345678', '2003-02-28', 3, 'usuario', 3, 'Aluno', 0, 'aprovado', NULL, NULL, NULL, NULL),
(6, 'Juliana Santos', 'juliana.santos@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '456789', '2000-07-12', 4, 'admin', 4, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(7, 'Fernanda Almeida', 'fernanda.almeida@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '567890', '2002-12-01', 5, 'usuario', 5, 'Aluno', 0, 'pendente', NULL, NULL, NULL, NULL),
(8, 'Ricardo Souza', 'ricardo.souza@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '678901', '2001-04-18', 6, 'admin', 6, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(9, 'Lucas Ferreira', 'lucas.ferreira@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '789012', '2003-01-20', 7, 'usuario', 7, 'Aluno', 0, 'aprovado', NULL, NULL, NULL, NULL),
(10, 'Beatriz Gonçalves', 'beatriz.goncalves@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '890123', '2002-06-14', 8, 'usuario', 8, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(12, 'Laura Azevedo', 'laura.azevedo@aluno.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', '012345', '2000-03-25', 10, 'usuario', 10, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(13, 'Carlos Pereira (Professor)', 'carlos.pereira@professor.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', NULL, '1985-03-15', NULL, 'usuario', NULL, 'Professor', 0, 'none', NULL, NULL, NULL, NULL),
(14, 'Marcos Lima (Professor Coordenador)', 'marcos.lima@professor.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', NULL, '1978-09-05', NULL, 'usuario', NULL, 'Professor', 1, 'none', NULL, NULL, NULL, NULL),
(15, 'Ana Costa (Comunidade Externa)', 'ana.costa@externo.com', '$2y$10$d8SoY8sdOkYci2Q/de.uye4c6j7Cu.CUYVxEm55Lk43l4Am7KBbqi', NULL, '1995-11-30', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none', NULL, NULL, NULL, NULL),
(16, 'ALUNO TESTE', '000001@unifio.edu.br', '$2y$10$m03FUqNSkSmZMpbYWpmSVuMKdIxl0nVsZaQymFhbspOkowFhI6W/.', '000001', '0001-01-01', 2, 'usuario', 2, 'Aluno', 0, 'none', NULL, NULL, NULL, NULL),
(17, 'PROFESSOR TESTE', 'professorteste@fio.edu.br', '$2y$10$BEQjDm7/WCV3t8/oIlLvze2BpPOWOCL4zcOJwOy6NZT.EwsXNjxwK', NULL, '0001-01-01', NULL, 'usuario', NULL, 'Professor', 0, 'none', NULL, NULL, NULL, NULL),
(18, 'ADMIN DA ATLETICA TESTE', '000002@unifio.edu.br', '$2y$10$vJsXaBgnElKTsRETwK6QxeXr9IwuZ3Dyne7TBtatjIVafKz7R7WQe', '000002', '0001-01-01', 2, 'admin', 2, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `atleticas`
--
ALTER TABLE `atleticas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `equipes`
--
ALTER TABLE `equipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `equipe_membros`
--
ALTER TABLE `equipe_membros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `inscricoes_eventos`
--
ALTER TABLE `inscricoes_eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `inscricoes_modalidade`
--
ALTER TABLE `inscricoes_modalidade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `presencas`
--
ALTER TABLE `presencas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT de tabela `professores_cursos`
--
ALTER TABLE `professores_cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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