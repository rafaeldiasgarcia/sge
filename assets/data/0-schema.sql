-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: sge-db
-- Tempo de geração: 02/10/2025 às 20:14
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
(1, 11, 'Treino de Férias - Futsal', 'esportivo', 'Futsal', '2025-07-02', 'primeiro', 'Treino leve de manutenção durante as férias.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Lucas Mendes', NULL, NULL, NULL, NULL, NULL, NULL, 15, NULL, NULL, NULL, NULL),
(2, 12, 'Jogo Amistoso Vôlei', 'esportivo', 'Voleibol', '2025-07-05', 'segundo', 'Amistoso contra time convidado.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Julia Alves', NULL, NULL, NULL, NULL, NULL, NULL, 20, NULL, NULL, NULL, NULL),
(3, 8, 'Curso de Extensão: Programação em R', 'nao_esportivo', NULL, '2025-07-08', 'primeiro', 'Curso de férias para a comunidade.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Profa. Beatriz Lima', NULL, NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL, NULL),
(4, 15, 'Planejamento de Eventos MAGNA', 'nao_esportivo', NULL, '2025-07-10', 'primeiro', 'Reunião de diretoria para o próximo semestre.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Gabriel Pereira', NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL),
(5, 11, 'Treino Físico Geral', 'esportivo', 'Atletismo', '2025-07-15', 'segundo', 'Preparação física geral para atletas.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Lucas Mendes', NULL, NULL, NULL, NULL, NULL, NULL, 25, NULL, NULL, NULL, NULL),
(6, 20, 'Palestra: Saúde Mental no Esporte', 'nao_esportivo', NULL, '2025-07-18', 'primeiro', 'Palestra com psicólogo convidado.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Sr. Jorge Santos', NULL, NULL, NULL, NULL, NULL, NULL, 80, NULL, NULL, NULL, NULL),
(7, 13, 'Campeonato Relâmpago de CS:GO', 'esportivo', 'CS:GO', '2025-07-22', 'segundo', 'Torneio de um dia entre os alunos.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Pedro Martins', NULL, NULL, NULL, NULL, NULL, NULL, 16, NULL, NULL, NULL, NULL),
(8, 14, 'Ação Social SANGUINÁRIA', 'nao_esportivo', NULL, '2025-07-26', 'primeiro', 'Campanha de doação de sangue.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Fernanda Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 100, NULL, NULL, NULL, NULL),
(9, 11, 'Volta aos Treinos - Futsal', 'esportivo', 'Futsal', '2025-08-01', 'primeiro', 'Início oficial dos treinos do semestre.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Lucas Mendes', NULL, NULL, NULL, NULL, NULL, NULL, 18, NULL, NULL, NULL, NULL),
(10, 12, 'Seletiva Vôlei PREDADORA', 'esportivo', 'Voleibol', '2025-08-04', 'segundo', 'Seleção de novas atletas.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Julia Alves', NULL, NULL, NULL, NULL, NULL, NULL, 30, NULL, NULL, NULL, NULL),
(11, 7, 'Aula Magna Engenharia Civil', 'nao_esportivo', NULL, '2025-08-05', 'primeiro', 'Evento de boas-vindas aos calouros.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Prof. Carlos Andrade', NULL, NULL, NULL, NULL, NULL, NULL, 150, NULL, NULL, NULL, NULL),
(12, 13, 'Treino Tático Valorant', 'esportivo', NULL, '2025-08-07', 'segundo', 'Análise de mapas e estratégias.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Pedro Martins', NULL, NULL, NULL, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL),
(13, 16, 'Treino Handebol VENENOSA', 'esportivo', 'Handebol', '2025-08-11', 'primeiro', 'Foco em jogadas ensaiadas.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Mariana Ferreira', NULL, NULL, NULL, NULL, NULL, NULL, 14, NULL, NULL, NULL, NULL),
(14, 9, 'Simpósio de Direito Penal', 'nao_esportivo', NULL, '2025-08-15', 'primeiro', 'Evento com palestras e debates.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Prof. Ricardo Souza', NULL, NULL, NULL, NULL, NULL, NULL, 120, NULL, NULL, NULL, NULL),
(15, 11, 'Treino de Rugby', 'esportivo', NULL, '2025-08-19', 'segundo', 'Treino de contato e táticas de jogo.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Lucas Mendes', NULL, NULL, NULL, NULL, NULL, NULL, 22, NULL, NULL, NULL, NULL),
(16, 17, 'Festival de Queimada', 'esportivo', 'Queimada', '2025-08-23', 'primeiro', 'Evento de integração para calouros.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Bruno Rodrigues', NULL, NULL, NULL, NULL, NULL, NULL, 50, NULL, NULL, NULL, NULL),
(17, 4, 'Reunião Geral - Admin Atlética', 'nao_esportivo', NULL, '2025-08-26', 'primeiro', 'Alinhamento com a diretoria de esportes.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Admin Atletica Teste', NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL),
(18, 12, 'Treino de Polo Aquático', 'esportivo', NULL, '2025-08-28', 'segundo', 'Treino em piscina olímpica.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Julia Alves', NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL),
(19, 7, 'Palestra: Engenharia e Inovação', 'nao_esportivo', NULL, '2025-09-02', 'primeiro', 'Evento do curso de Engenharia de Produção.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Prof. Carlos Andrade', NULL, NULL, NULL, NULL, NULL, NULL, 90, NULL, NULL, NULL, NULL),
(20, 11, 'Jogo-Treino Futsal vs SANGUINÁRIA', 'esportivo', 'Futsal', '2025-09-05', 'segundo', 'Jogo preparatório.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Lucas Mendes', NULL, NULL, NULL, NULL, NULL, NULL, 35, NULL, NULL, NULL, NULL),
(21, 8, 'Treino de Cobertura de Eventos', 'nao_esportivo', NULL, '2025-09-09', 'primeiro', 'Atividade prática para alunos de Jornalismo.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Profa. Beatriz Lima', NULL, NULL, NULL, NULL, NULL, NULL, 25, NULL, NULL, NULL, NULL),
(22, 13, 'Treino Cancelado (Chuva)', 'esportivo', 'League of Legends', '2025-09-11', 'segundo', 'Treino cancelado por problemas na rede elétrica.', 'cancelado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Pedro Martins', NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL),
(23, 15, 'Semana do Administrador', 'nao_esportivo', NULL, '2025-09-16', 'primeiro', 'Ciclo de palestras e workshops.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Gabriel Pereira', NULL, NULL, NULL, NULL, NULL, NULL, 60, NULL, NULL, NULL, NULL),
(24, 4, 'Manutenção do E-Sports', 'nao_esportivo', NULL, '2025-09-23', 'segundo', 'Atualização dos computadores da sala de e-sports.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Admin Atletica Teste', NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL, NULL, NULL, NULL),
(25, 18, 'Cine Debate - Psicologia', 'nao_esportivo', NULL, '2025-09-26', 'primeiro', 'Exibição de filme seguida de debate.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Larissa Gonçalves', NULL, NULL, NULL, NULL, NULL, NULL, 45, NULL, NULL, NULL, NULL),
(26, 11, 'Treino Futsal Masculino - FURIOSA', 'esportivo', 'Futsal', '2025-10-06', 'primeiro', 'Treino preparatório para o Intercursos.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Lucas Mendes', NULL, NULL, NULL, NULL, NULL, NULL, 20, NULL, NULL, NULL, NULL),
(27, 12, 'Treino Vôlei Feminino - PREDADORA', 'esportivo', 'Voleibol', '2025-10-06', 'segundo', 'Treino tático e físico.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Julia Alves', NULL, NULL, NULL, NULL, NULL, NULL, 16, NULL, NULL, NULL, NULL),
(28, 13, 'Treino League of Legends - ALFA', 'esportivo', 'League of Legends', '2025-10-07', 'primeiro', 'Treino de estratégias e team play.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Pedro Martins', NULL, NULL, NULL, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL),
(29, 20, 'Palestra sobre Mercado de Trabalho', 'nao_esportivo', NULL, '2025-10-08', 'primeiro', 'Palestra com convidado externo para alunos.', 'pendente', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Sr. Jorge Santos', NULL, NULL, NULL, NULL, NULL, NULL, 75, NULL, NULL, NULL, NULL),
(30, 14, 'Treino Basquete - SANGUINÁRIA', 'esportivo', 'Basquetebol', '2025-10-08', 'segundo', 'Foco em arremessos e defesa.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Fernanda Oliveira', NULL, NULL, NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL),
(31, 8, 'Workshop de Python para iniciantes', 'nao_esportivo', NULL, '2025-10-09', 'primeiro', 'Organizado pelo curso de Ciência da Computação.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Profa. Beatriz Lima', NULL, NULL, NULL, NULL, NULL, NULL, 30, NULL, NULL, NULL, NULL),
(32, 11, 'Amistoso Futsal FURIOSA x ALFA', 'esportivo', 'Futsal', '2025-10-10', 'segundo', 'Jogo amistoso entre as atléticas.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Lucas Mendes', NULL, NULL, NULL, NULL, NULL, NULL, 40, NULL, NULL, NULL, NULL),
(33, 15, 'Reunião da Atlética MAGNA', 'nao_esportivo', NULL, '2025-10-13', 'primeiro', 'Planejamento de eventos do semestre.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Gabriel Pereira', NULL, NULL, NULL, NULL, NULL, NULL, 15, NULL, NULL, NULL, NULL),
(34, 17, 'Uso da quadra para Lazer', 'esportivo', 'Futsal', '2025-10-13', 'segundo', 'Solicitação de aluno para jogo com amigos.', 'rejeitado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Bruno Rodrigues', NULL, NULL, NULL, NULL, NULL, NULL, 8, NULL, NULL, NULL, NULL),
(35, 16, 'Treino de Handebol - VENENOSA', 'esportivo', 'Handebol', '2025-10-14', 'primeiro', 'Treino de ataque e contra-ataque.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Mariana Ferreira', NULL, NULL, NULL, NULL, NULL, NULL, 18, NULL, NULL, NULL, NULL),
(36, 6, 'Manutenção da Quadra', 'nao_esportivo', NULL, '2025-10-15', 'primeiro', 'Reserva para manutenção e pintura.', 'aprovado', NULL, '2025-10-02 20:12:20', 0, NULL, 0, 0, NULL, 'Admin Esportes', NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `atleticas`
--

CREATE TABLE `atleticas` (
  `id` int NOT NULL,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atleticas`
--

INSERT INTO `atleticas` (`id`, `nome`) VALUES
(1, 'A.A.A. FURIOSA'),
(2, 'A.A.A. PREDADORA'),
(3, 'A.A.A. SANGUINÁRIA'),
(4, 'A.A.A. INSANA'),
(5, 'A.A.A. MAGNA'),
(6, 'A.A.A. ALFA'),
(7, 'A.A.A. IMPÉRIO'),
(8, 'A.A.A. VENENOSA'),
(9, 'A.A.A. LETAL'),
(10, 'A.A.A. ATÔMICA');

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
(1, 'Engenharia Civil', 1, 7),
(2, 'Engenharia de Software', 6, NULL),
(3, 'Direito', 2, 9),
(4, 'Medicina', 3, NULL),
(5, 'Psicologia', 4, NULL),
(6, 'Administração', 5, NULL),
(7, 'Ciência da Computação', 6, 8),
(8, 'Publicidade e Propaganda', 7, NULL),
(9, 'Farmácia', 8, NULL),
(10, 'Ciências Biológicas', 9, NULL);

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
(1, 11, 7, 1, 'aprovado', '2025-10-02 20:12:20', NULL),
(2, 13, 3, 6, 'aprovado', '2025-10-02 20:12:20', NULL),
(3, 17, 6, 6, 'aprovado', '2025-10-02 20:12:20', NULL),
(4, 18, 6, 6, 'aprovado', '2025-10-02 20:12:20', NULL),
(5, 12, 2, 2, 'aprovado', '2025-10-02 20:12:20', NULL);

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
(1, 11, 1, 1, 'aprovado', '2025-10-02 20:12:20'),
(2, 12, 2, 2, 'aprovado', '2025-10-02 20:12:20'),
(3, 13, 12, 6, 'aprovado', '2025-10-02 20:12:20'),
(4, 14, 3, 3, 'aprovado', '2025-10-02 20:12:20'),
(5, 15, 11, 5, 'aprovado', '2025-10-02 20:12:20'),
(6, 16, 4, 8, 'aprovado', '2025-10-02 20:12:20'),
(7, 17, 1, 1, 'pendente', '2025-10-02 20:12:20'),
(8, 18, 2, 2, 'pendente', '2025-10-02 20:12:20');

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
(1, 'Futsal'),
(2, 'Voleibol'),
(3, 'Basquetebol'),
(4, 'Handebol'),
(5, 'Natação'),
(6, 'Atletismo'),
(7, 'Judô'),
(8, 'Karatê'),
(9, 'Tênis de Mesa'),
(10, 'Tênis de Campo'),
(11, 'Xadrez'),
(12, 'League of Legends'),
(13, 'CS:GO'),
(14, 'Vôlei de Praia'),
(15, 'Queimada');

-- --------------------------------------------------------

--
-- Estrutura para tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `mensagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('agendamento_aprovado','agendamento_rejeitado','agendamento_cancelado','presenca_confirmada','lembrete_evento','info','aviso') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `agendamento_id` int DEFAULT NULL,
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id`, `usuario_id`, `titulo`, `mensagem`, `tipo`, `agendamento_id`, `lida`, `data_criacao`) VALUES
(1, 11, 'Agendamento Aprovado', 'Seu agendamento \"Treino de Férias - Futsal\" foi aprovado para o dia 02/07/2025 no primeiro período.', 'agendamento_aprovado', 1, 1, '2025-07-01 13:00:00'),
(2, 12, 'Agendamento Aprovado', 'Seu agendamento \"Jogo Amistoso Vôlei\" foi aprovado para o dia 05/07/2025 no segundo período.', 'agendamento_aprovado', 2, 1, '2025-07-04 17:30:00'),
(3, 8, 'Agendamento Aprovado', 'Seu agendamento \"Curso de Extensão: Programação em R\" foi aprovado para o dia 08/07/2025 no primeiro período.', 'agendamento_aprovado', 3, 1, '2025-07-07 12:15:00'),
(4, 15, 'Agendamento Aprovado', 'Seu agendamento \"Planejamento de Eventos MAGNA\" foi aprovado para o dia 10/07/2025 no primeiro período.', 'agendamento_aprovado', 4, 1, '2025-07-09 19:45:00'),
(5, 20, 'Agendamento Aprovado', 'Seu agendamento \"Palestra: Saúde Mental no Esporte\" foi aprovado para o dia 18/07/2025 no primeiro período.', 'agendamento_aprovado', 6, 1, '2025-07-17 14:20:00'),
(6, 13, 'Agendamento Cancelado', 'Seu agendamento \"Treino Cancelado (Chuva)\" foi cancelado devido a problemas na rede elétrica.', 'agendamento_cancelado', 22, 0, '2025-09-11 18:30:00'),
(7, 17, 'Agendamento Rejeitado', 'Seu agendamento \"Uso da quadra para Lazer\" foi rejeitado. A quadra é destinada para atividades oficiais das atléticas.', 'agendamento_rejeitado', 33, 0, '2025-10-12 13:45:00'),
(8, 11, 'Presença Confirmada', 'Sua presença foi confirmada no evento \"Treino de Férias - Futsal\" do dia 02/07/2025.', 'presenca_confirmada', 1, 1, '2025-07-02 22:00:00'),
(9, 12, 'Presença Confirmada', 'Sua presença foi confirmada no evento \"Jogo Amistoso Vôlei\" do dia 05/07/2025.', 'presenca_confirmada', 2, 1, '2025-07-06 00:00:00'),
(10, 14, 'Presença Confirmada', 'Sua presença foi confirmada no evento \"Ação Social SANGUINÁRIA\" do dia 26/07/2025.', 'presenca_confirmada', 8, 1, '2025-07-26 22:15:00'),
(11, 11, 'Lembrete de Evento', 'Lembrete: Você tem o evento \"Treino Futsal Masculino - FURIOSA\" amanhã às 19:15.', 'lembrete_evento', 26, 0, '2025-10-05 21:00:00'),
(12, 12, 'Lembrete de Evento', 'Lembrete: Você tem o evento \"Treino Vôlei Feminino - PREDADORA\" amanhã às 21:10.', 'lembrete_evento', 27, 0, '2025-10-05 23:00:00'),
(13, 13, 'Lembrete de Evento', 'Lembrete: Você tem o evento \"Treino League of Legends - ALFA\" hoje às 19:15.', 'lembrete_evento', 28, 0, '2025-10-07 20:00:00'),
(14, 14, 'Lembrete de Evento', 'Lembrete: Você tem o evento \"Treino Basquete - SANGUINÁRIA\" hoje às 21:10.', 'lembrete_evento', 30, 0, '2025-10-08 22:00:00'),
(15, 8, 'Lembrete de Evento', 'Lembrete: Você tem o evento \"Workshop de Python para iniciantes\" amanhã às 19:15.', 'lembrete_evento', 31, 0, '2025-10-08 21:30:00'),
(16, 1, 'Nova Funcionalidade', 'O sistema de notificações foi implementado! Agora você receberá atualizações sobre seus agendamentos.', 'info', NULL, 0, '2025-10-01 11:00:00'),
(17, 2, 'Manutenção da Quadra', 'A quadra passará por manutenção no dia 15/10/2025. Não haverá atividades neste dia.', 'info', 35, 1, '2025-10-01 12:00:00'),
(18, 3, 'Sistema de Presenças', 'Lembre-se de confirmar sua presença nos eventos através do sistema.', 'info', NULL, 1, '2025-09-28 17:00:00'),
(19, 11, 'Intercursos 2025', 'As inscrições para o Intercursos 2025 começam na próxima semana. Fique atento!', 'aviso', NULL, 0, '2025-10-01 13:00:00'),
(20, 12, 'Intercursos 2025', 'As inscrições para o Intercursos 2025 começam na próxima semana. Fique atento!', 'aviso', NULL, 0, '2025-10-01 13:00:00'),
(21, 13, 'Intercursos 2025', 'As inscrições para o Intercursos 2025 começam na próxima semana. Fique atento!', 'aviso', NULL, 0, '2025-10-01 13:00:00'),
(22, 14, 'Intercursos 2025', 'As inscrições para o Intercursos 2025 começam na próxima semana. Fique atento!', 'aviso', NULL, 0, '2025-10-01 13:00:00'),
(23, 15, 'Intercursos 2025', 'As inscrições para o Intercursos 2025 começam na próxima semana. Fique atento!', 'aviso', NULL, 0, '2025-10-01 13:00:00'),
(24, 16, 'Intercursos 2025', 'As inscrições para o Intercursos 2025 começam na próxima semana. Fique atento!', 'aviso', NULL, 0, '2025-10-01 13:00:00'),
(25, 4, 'Reunião de Admins', 'Reunião mensal dos administradores das atléticas marcada para 20/10/2025.', 'info', NULL, 0, '2025-10-01 14:00:00'),
(26, 6, 'Atualização do Sistema', 'O sistema será atualizado durante a madrugada de 10/10/2025. Pode haver instabilidade.', 'aviso', NULL, 1, '2025-10-10 01:00:00'),
(27, 7, 'Evento Acadêmico', 'Seu evento \"Aula Magna Engenharia Civil\" teve grande participação. Parabéns!', 'info', 11, 1, '2025-08-06 13:00:00'),
(28, 9, 'Feedback do Evento', 'O \"Simpósio de Direito Penal\" foi muito bem avaliado pelos participantes.', 'info', 14, 1, '2025-08-16 12:30:00'),
(29, 18, 'Inscrição Aprovada', 'Sua inscrição na modalidade de Vôlei foi aprovada pela atlética PREDADORA.', 'info', NULL, 0, '2025-09-15 19:20:00'),
(30, 17, 'Inscrição Pendente', 'Sua inscrição na modalidade de Futsal está pendente de aprovação pela atlética FURIOSA.', 'info', NULL, 0, '2025-09-20 14:45:00'),
(31, 20, 'Status do Agendamento', 'Seu agendamento \"Palestra sobre Mercado de Trabalho\" ainda está pendente de aprovação.', 'info', 29, 0, '2025-10-07 17:00:00'),
(32, 15, 'Confirmação de Evento', 'Lembrete: Reunião da Atlética MAGNA confirmada para 13/10/2025 às 19:15.', 'lembrete_evento', 33, 0, '2025-10-12 20:00:00'),
(33, 16, 'Treino Confirmado', 'Seu treino de Handebol foi confirmado para 14/10/2025. Compareça com antecedência!', 'lembrete_evento', 34, 0, '2025-10-13 22:00:00'),
(34, 11, 'Materiais Disponíveis', 'Os materiais para o treino de futsal estão disponíveis na secretaria.', 'info', 26, 0, '2025-10-05 18:00:00'),
(35, 13, 'Equipamentos E-sports', 'Os computadores da sala de e-sports foram atualizados. Aproveitem!', 'info', 24, 1, '2025-09-24 11:00:00'),
(36, 19, 'Bem-vindo ao SGE', 'Bem-vindo ao Sistema de Gestão Esportiva! Explore as funcionalidades disponíveis.', 'info', NULL, 1, '2025-09-01 12:00:00'),
(37, 21, 'Como Agendar Eventos', 'Acesse o menu \"Agendar Evento\" para solicitar o uso da quadra poliesportiva.', 'info', NULL, 1, '2025-09-15 13:30:00');

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
(1, 11, 1, '2025-10-02 20:12:20'),
(2, 12, 1, '2025-10-02 20:12:20'),
(3, 13, 1, '2025-10-02 20:12:20'),
(4, 14, 1, '2025-10-02 20:12:20'),
(5, 15, 1, '2025-10-02 20:12:20'),
(6, 16, 1, '2025-10-02 20:12:20'),
(7, 17, 1, '2025-10-02 20:12:20'),
(8, 2, 1, '2025-10-02 20:12:20'),
(9, 3, 1, '2025-10-02 20:12:20'),
(10, 4, 1, '2025-10-02 20:12:20'),
(11, 20, 1, '2025-10-02 20:12:20'),
(12, 12, 2, '2025-10-02 20:12:20'),
(13, 13, 2, '2025-10-02 20:12:20'),
(14, 14, 2, '2025-10-02 20:12:20'),
(15, 16, 2, '2025-10-02 20:12:20'),
(16, 18, 2, '2025-10-02 20:12:20'),
(17, 19, 2, '2025-10-02 20:12:20'),
(18, 3, 2, '2025-10-02 20:12:20'),
(19, 4, 2, '2025-10-02 20:12:20'),
(20, 8, 3, '2025-10-02 20:12:20'),
(21, 11, 3, '2025-10-02 20:12:20'),
(22, 13, 3, '2025-10-02 20:12:20'),
(23, 15, 3, '2025-10-02 20:12:20'),
(24, 17, 3, '2025-10-02 20:12:20'),
(25, 18, 3, '2025-10-02 20:12:20'),
(26, 19, 3, '2025-10-02 20:12:20'),
(27, 2, 3, '2025-10-02 20:12:20'),
(28, 3, 3, '2025-10-02 20:12:20'),
(29, 4, 3, '2025-10-02 20:12:20'),
(30, 20, 3, '2025-10-02 20:12:20'),
(31, 21, 3, '2025-10-02 20:12:20'),
(32, 7, 3, '2025-10-02 20:12:20'),
(33, 9, 3, '2025-10-02 20:12:20'),
(34, 10, 3, '2025-10-02 20:12:20'),
(35, 15, 4, '2025-10-02 20:12:20'),
(36, 11, 4, '2025-10-02 20:12:20'),
(37, 13, 4, '2025-10-02 20:12:20'),
(38, 16, 4, '2025-10-02 20:12:20'),
(39, 18, 4, '2025-10-02 20:12:20'),
(40, 11, 5, '2025-10-02 20:12:20'),
(41, 12, 5, '2025-10-02 20:12:20'),
(42, 13, 5, '2025-10-02 20:12:20'),
(43, 14, 5, '2025-10-02 20:12:20'),
(44, 16, 5, '2025-10-02 20:12:20'),
(45, 17, 5, '2025-10-02 20:12:20'),
(46, 18, 5, '2025-10-02 20:12:20'),
(47, 2, 5, '2025-10-02 20:12:20'),
(48, 3, 5, '2025-10-02 20:12:20'),
(49, 4, 5, '2025-10-02 20:12:20'),
(50, 19, 5, '2025-10-02 20:12:20'),
(51, 20, 5, '2025-10-02 20:12:20'),
(52, 20, 6, '2025-10-02 20:12:20'),
(53, 11, 6, '2025-10-02 20:12:20'),
(54, 12, 6, '2025-10-02 20:12:20'),
(55, 13, 6, '2025-10-02 20:12:20'),
(56, 14, 6, '2025-10-02 20:12:20'),
(57, 15, 6, '2025-10-02 20:12:20'),
(58, 16, 6, '2025-10-02 20:12:20'),
(59, 17, 6, '2025-10-02 20:12:20'),
(60, 18, 6, '2025-10-02 20:12:20'),
(61, 19, 6, '2025-10-02 20:12:20'),
(62, 2, 6, '2025-10-02 20:12:20'),
(63, 3, 6, '2025-10-02 20:12:20'),
(64, 4, 6, '2025-10-02 20:12:20'),
(65, 7, 6, '2025-10-02 20:12:20'),
(66, 8, 6, '2025-10-02 20:12:20'),
(67, 9, 6, '2025-10-02 20:12:20'),
(68, 10, 6, '2025-10-02 20:12:20'),
(69, 21, 6, '2025-10-02 20:12:20'),
(70, 13, 7, '2025-10-02 20:12:20'),
(71, 11, 7, '2025-10-02 20:12:20'),
(72, 17, 7, '2025-10-02 20:12:20'),
(73, 18, 7, '2025-10-02 20:12:20'),
(74, 19, 7, '2025-10-02 20:12:20'),
(75, 2, 7, '2025-10-02 20:12:20'),
(76, 3, 7, '2025-10-02 20:12:20'),
(77, 4, 7, '2025-10-02 20:12:20'),
(78, 15, 7, '2025-10-02 20:12:20'),
(79, 14, 8, '2025-10-02 20:12:20'),
(80, 11, 8, '2025-10-02 20:12:20'),
(81, 12, 8, '2025-10-02 20:12:20'),
(82, 13, 8, '2025-10-02 20:12:20'),
(83, 15, 8, '2025-10-02 20:12:20'),
(84, 16, 8, '2025-10-02 20:12:20'),
(85, 17, 8, '2025-10-02 20:12:20'),
(86, 18, 8, '2025-10-02 20:12:20'),
(87, 19, 8, '2025-10-02 20:12:20'),
(88, 20, 8, '2025-10-02 20:12:20'),
(89, 21, 8, '2025-10-02 20:12:20'),
(90, 2, 8, '2025-10-02 20:12:20'),
(91, 3, 8, '2025-10-02 20:12:20'),
(92, 4, 8, '2025-10-02 20:12:20'),
(93, 7, 8, '2025-10-02 20:12:20'),
(94, 8, 8, '2025-10-02 20:12:20'),
(95, 9, 8, '2025-10-02 20:12:20'),
(96, 10, 8, '2025-10-02 20:12:20'),
(97, 6, 8, '2025-10-02 20:12:20'),
(98, 5, 8, '2025-10-02 20:12:20'),
(99, 1, 8, '2025-10-02 20:12:20'),
(100, 11, 18, '2025-10-02 20:12:20'),
(101, 13, 18, '2025-10-02 20:12:20'),
(102, 17, 18, '2025-10-02 20:12:20'),
(103, 2, 18, '2025-10-02 20:12:20'),
(104, 3, 18, '2025-10-02 20:12:20'),
(105, 15, 18, '2025-10-02 20:12:20'),
(106, 12, 19, '2025-10-02 20:12:20'),
(107, 14, 19, '2025-10-02 20:12:20'),
(108, 16, 19, '2025-10-02 20:12:20'),
(109, 18, 19, '2025-10-02 20:12:20'),
(110, 13, 20, '2025-10-02 20:12:20'),
(111, 11, 20, '2025-10-02 20:12:20'),
(112, 17, 20, '2025-10-02 20:12:20'),
(113, 19, 20, '2025-10-02 20:12:20'),
(114, 2, 20, '2025-10-02 20:12:20'),
(115, 3, 20, '2025-10-02 20:12:20'),
(116, 4, 20, '2025-10-02 20:12:20'),
(117, 14, 22, '2025-10-02 20:12:20'),
(118, 11, 22, '2025-10-02 20:12:20'),
(119, 16, 22, '2025-10-02 20:12:20'),
(120, 18, 22, '2025-10-02 20:12:20'),
(121, 20, 22, '2025-10-02 20:12:20'),
(122, 8, 23, '2025-10-02 20:12:20'),
(123, 11, 23, '2025-10-02 20:12:20'),
(124, 13, 23, '2025-10-02 20:12:20'),
(125, 17, 23, '2025-10-02 20:12:20'),
(126, 19, 23, '2025-10-02 20:12:20'),
(127, 2, 23, '2025-10-02 20:12:20'),
(128, 3, 23, '2025-10-02 20:12:20'),
(129, 4, 23, '2025-10-02 20:12:20'),
(130, 15, 23, '2025-10-02 20:12:20'),
(131, 18, 23, '2025-10-02 20:12:20'),
(132, 20, 23, '2025-10-02 20:12:20'),
(133, 21, 23, '2025-10-02 20:12:20'),
(134, 11, 24, '2025-10-02 20:12:20'),
(135, 13, 24, '2025-10-02 20:12:20'),
(136, 17, 24, '2025-10-02 20:12:20'),
(137, 12, 24, '2025-10-02 20:12:20'),
(138, 14, 24, '2025-10-02 20:12:20'),
(139, 16, 24, '2025-10-02 20:12:20'),
(140, 2, 24, '2025-10-02 20:12:20'),
(141, 3, 24, '2025-10-02 20:12:20'),
(142, 15, 25, '2025-10-02 20:12:20'),
(143, 18, 25, '2025-10-02 20:12:20'),
(144, 20, 25, '2025-10-02 20:12:20'),
(145, 16, 27, '2025-10-02 20:12:20'),
(146, 14, 27, '2025-10-02 20:12:20'),
(147, 12, 27, '2025-10-02 20:12:20'),
(148, 18, 27, '2025-10-02 20:12:20'),
(149, 19, 27, '2025-10-02 20:12:20'),
(150, 20, 27, '2025-10-02 20:12:20');

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

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ra`, `data_nascimento`, `telefone`, `curso_id`, `role`, `atletica_id`, `tipo_usuario_detalhado`, `is_coordenador`, `atletica_join_status`, `login_code`, `login_code_expires`, `reset_token`, `reset_token_expires`) VALUES
(1, 'Super Admin', 'sadmin', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, NULL, NULL, NULL, 'superadmin', NULL, NULL, 0, 'none', NULL, NULL, NULL, NULL),
(2, 'Aluno Teste', 'aluno@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '123456', '2004-08-15', '(14) 99123-4567', 1, 'usuario', NULL, 'Aluno', 0, 'none', NULL, NULL, NULL, NULL),
(3, 'Membro Atletica Teste', 'membro@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '789012', '2003-05-20', '(14) 99765-4321', 2, 'usuario', 1, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(4, 'Admin Atletica Teste', 'admin.atletica@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '345678', '2002-02-10', '(14) 98888-7777', 3, 'admin', 1, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(5, 'Comunidade Externa Teste', 'comunidade@email.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1990-11-30', '(11) 97777-8888', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none', NULL, NULL, NULL, NULL),
(6, 'Admin Esportes', 'admin@sge.com', '$2y$10$hashficticio2', NULL, '1992-05-10', '11987654322', NULL, 'admin', NULL, NULL, 0, 'none', NULL, NULL, NULL, NULL),
(7, 'Prof. Carlos Andrade', 'carlos.andrade@prof.sge.com', '$2y$10$hashficticio3', NULL, '1975-03-15', '14991234567', 1, 'usuario', NULL, 'Professor', 1, 'none', NULL, NULL, NULL, NULL),
(8, 'Profa. Beatriz Lima', 'beatriz.lima@prof.sge.com', '$2y$10$hashficticio4', NULL, '1980-11-20', '14991234568', 7, 'usuario', NULL, 'Professor', 1, 'none', NULL, NULL, NULL, NULL),
(9, 'Prof. Ricardo Souza', 'ricardo.souza@prof.sge.com', '$2y$10$hashficticio5', NULL, '1968-07-08', '14991234569', 3, 'usuario', NULL, 'Professor', 1, 'none', NULL, NULL, NULL, NULL),
(10, 'Profa. Helena Costa', 'helena.costa@prof.sge.com', '$2y$10$hashficticio6', NULL, '1985-02-25', '14991234570', 4, 'usuario', NULL, 'Professor', 0, 'none', NULL, NULL, NULL, NULL),
(11, 'Lucas Mendes', 'lucas.mendes@aluno.sge.com', '$2y$10$hashficticio7', '111222', '2004-06-30', '14981112233', 1, 'usuario', 1, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(12, 'Julia Alves', 'julia.alves@aluno.sge.com', '$2y$10$hashficticio8', '222333', '2003-09-12', '14981112234', 3, 'usuario', 2, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(13, 'Pedro Martins', 'pedro.martins@aluno.sge.com', '$2y$10$hashficticio9', '333444', '2002-12-01', '14981112235', 7, 'usuario', 6, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(14, 'Fernanda Oliveira', 'fernanda.oliveira@aluno.sge.com', '$2y$10$hashficticio10', '444555', '2004-04-18', '14981112236', 4, 'usuario', 3, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(15, 'Gabriel Pereira', 'gabriel.pereira@aluno.sge.com', '$2y$10$hashficticio11', '555666', '2003-01-22', '14981112237', 6, 'usuario', 5, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(16, 'Mariana Ferreira', 'mariana.ferreira@aluno.sge.com', '$2y$10$hashficticio12', '666777', '2005-08-05', '14981112238', 9, 'usuario', 8, 'Membro das Atléticas', 0, 'aprovado', NULL, NULL, NULL, NULL),
(17, 'Bruno Rodrigues', 'bruno.rodrigues@aluno.sge.com', '$2y$10$hashficticio13', '777888', '2004-02-14', '14982223344', 2, 'usuario', NULL, 'Aluno', 0, 'none', NULL, NULL, NULL, NULL),
(18, 'Larissa Gonçalves', 'larissa.goncalves@aluno.sge.com', '$2y$10$hashficticio14', '888999', '2003-07-29', '14982223345', 5, 'usuario', NULL, 'Aluno', 0, 'none', NULL, NULL, NULL, NULL),
(19, 'Rafael Almeida', 'rafael.almeida@aluno.sge.com', '$2y$10$hashficticio15', '999000', '2002-11-03', '14982223346', 8, 'usuario', NULL, 'Aluno', 0, 'none', NULL, NULL, NULL, NULL),
(20, 'Sr. Jorge Santos', 'jorge.santos@email.com', '$2y$10$hashficticio16', NULL, '1988-10-10', '11976543210', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none', NULL, NULL, NULL, NULL),
(21, 'Sra. Ana Paula', 'ana.paula@email.com', '$2y$10$hashficticio17', NULL, '1995-05-20', '11976543211', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none', NULL, NULL, NULL, NULL);

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
-- Índices de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `agendamento_id` (`agendamento_id`);

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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `inscricoes_modalidade`
--
ALTER TABLE `inscricoes_modalidade`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `modalidades`
--
ALTER TABLE `modalidades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de tabela `presencas`
--
ALTER TABLE `presencas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
-- Restrições para tabelas `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD CONSTRAINT `notificacoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notificacoes_ibfk_2` FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos` (`id`) ON DELETE SET NULL;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
