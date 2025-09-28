-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: sge-db
-- Tempo de geração: 28/09/2025 às 04:42
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
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restrições para tabelas despejadas
--

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
