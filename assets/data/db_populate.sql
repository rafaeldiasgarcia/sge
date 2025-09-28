-- SGE-DB POPULATE SCRIPT (ESTRUTURA ANTIGA + USUÁRIOS DE TESTE NO INÍCIO)
-- Desativa a verificação de chaves estrangeiras para permitir a inserção de dados.
SET FOREIGN_KEY_CHECKS=0;
-- Inicia uma transação.
START TRANSACTION;

-- Limpando dados existentes para evitar duplicatas e garantir um ambiente limpo
DELETE FROM `presencas`;
DELETE FROM `inscricoes_eventos`;
DELETE FROM `inscricoes_modalidade`;
DELETE FROM `agendamentos`;
DELETE FROM `usuarios`;
DELETE FROM `cursos`;
DELETE FROM `atleticas`;
DELETE FROM `modalidades`;

-- Resetando AUTO_INCREMENT para todas as tabelas para começar do 1
ALTER TABLE `atleticas` AUTO_INCREMENT = 1;
ALTER TABLE `modalidades` AUTO_INCREMENT = 1;
ALTER TABLE `cursos` AUTO_INCREMENT = 1;
ALTER TABLE `usuarios` AUTO_INCREMENT = 1;
ALTER TABLE `agendamentos` AUTO_INCREMENT = 1;
ALTER TABLE `inscricoes_modalidade` AUTO_INCREMENT = 1;
ALTER TABLE `inscricoes_eventos` AUTO_INCREMENT = 1;
ALTER TABLE `presencas` AUTO_INCREMENT = 1;


--
-- Inserindo dados na tabela `atleticas`
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

--
-- Inserindo dados na tabela `modalidades`
--
INSERT INTO `modalidades` (`id`, `nome`) VALUES
(1, 'Futsal'),(2, 'Voleibol'),(3, 'Basquetebol'),(4, 'Handebol'),(5, 'Natação'),(6, 'Atletismo'),(7, 'Judô'),(8, 'Karatê'),(9, 'Tênis de Mesa'),(10, 'Tênis de Campo'),(11, 'Xadrez'),(12, 'League of Legends'),(13, 'CS:GO'),(14, 'Vôlei de Praia'),(15, 'Queimada');

--
-- Inserindo dados na tabela `cursos`
--
INSERT INTO `cursos` (`id`, `nome`, `atletica_id`, `coordenador_id`) VALUES
(1, 'Engenharia Civil', 1, NULL),(2, 'Engenharia de Software', 6, NULL),(3, 'Direito', 2, NULL),(4, 'Medicina', 3, NULL),(5, 'Psicologia', 4, NULL),(6, 'Administração', 5, NULL),(7, 'Ciência da Computação', 6, NULL),(8, 'Publicidade e Propaganda', 7, NULL),(9, 'Farmácia', 8, NULL),(10, 'Ciências Biológicas', 9, NULL);

--
-- Inserindo dados na tabela `usuarios` (Estrutura Antiga: id, nome, email, senha, ra, ...)
--
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ra`, `data_nascimento`, `telefone`, `curso_id`, `role`, `atletica_id`, `tipo_usuario_detalhado`, `is_coordenador`, `atletica_join_status`) VALUES

(NULL, 'Super Admin', 'sadmin', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, NULL, NULL, NULL, 'superadmin', NULL, NULL, 0, 'none'),
(NULL, 'Aluno Teste', 'aluno@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '123456', '2004-08-15', '(14) 99123-4567', 1, 'usuario', NULL, 'Aluno', 0, 'none'),
(NULL, 'Membro Atletica Teste', 'membro@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '789012', '2003-05-20', '(14) 99765-4321', 2, 'usuario', 1, 'Membro das Atléticas', 0, 'aprovado'),
(NULL, 'Admin Atletica Teste', 'admin.atletica@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '345678', '2002-02-10', '(14) 98888-7777', 3, 'admin', 1, 'Membro das Atléticas', 0, 'aprovado'),
(NULL, 'Comunidade Externa Teste', 'comunidade@email.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1990-11-30', '(11) 97777-8888', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none'),

(NULL, 'Admin Esportes', 'admin@sge.com', '$2y$10$hashficticio2', NULL, '1992-05-10', '11987654322', NULL, 'admin', NULL, NULL, 0, 'none'),

(NULL, 'Prof. Carlos Andrade', 'carlos.andrade@prof.sge.com', '$2y$10$hashficticio3', NULL, '1975-03-15', '14991234567', 1, 'usuario', NULL, 'Professor', 1, 'none'),
(NULL, 'Profa. Beatriz Lima', 'beatriz.lima@prof.sge.com', '$2y$10$hashficticio4', NULL, '1980-11-20', '14991234568', 7, 'usuario', NULL, 'Professor', 1, 'none'),
(NULL, 'Prof. Ricardo Souza', 'ricardo.souza@prof.sge.com', '$2y$10$hashficticio5', NULL, '1968-07-08', '14991234569', 3, 'usuario', NULL, 'Professor', 1, 'none'),
(NULL, 'Profa. Helena Costa', 'helena.costa@prof.sge.com', '$2y$10$hashficticio6', NULL, '1985-02-25', '14991234570', 4, 'usuario', NULL, 'Professor', 0, 'none'),

(NULL, 'Lucas Mendes', 'lucas.mendes@aluno.sge.com', '$2y$10$hashficticio7', '111222', '2004-06-30', '14981112233', 1, 'usuario', 1, 'Membro das Atléticas', 0, 'aprovado'),
(NULL, 'Julia Alves', 'julia.alves@aluno.sge.com', '$2y$10$hashficticio8', '222333', '2003-09-12', '14981112234', 3, 'usuario', 2, 'Membro das Atléticas', 0, 'aprovado'),
(NULL, 'Pedro Martins', 'pedro.martins@aluno.sge.com', '$2y$10$hashficticio9', '333444', '2002-12-01', '14981112235', 7, 'usuario', 6, 'Membro das Atléticas', 0, 'aprovado'),
(NULL, 'Fernanda Oliveira', 'fernanda.oliveira@aluno.sge.com', '$2y$10$hashficticio10', '444555', '2004-04-18', '14981112236', 4, 'usuario', 3, 'Membro das Atléticas', 0, 'aprovado'),
(NULL, 'Gabriel Pereira', 'gabriel.pereira@aluno.sge.com', '$2y$10$hashficticio11', '555666', '2003-01-22', '14981112237', 6, 'usuario', 5, 'Membro das Atléticas', 0, 'aprovado'),
(NULL, 'Mariana Ferreira', 'mariana.ferreira@aluno.sge.com', '$2y$10$hashficticio12', '666777', '2005-08-05', '14981112238', 9, 'usuario', 8, 'Membro das Atléticas', 0, 'aprovado'),

(NULL, 'Bruno Rodrigues', 'bruno.rodrigues@aluno.sge.com', '$2y$10$hashficticio13', '777888', '2004-02-14', '14982223344', 2, 'usuario', NULL, 'Aluno', 0, 'none'),
(NULL, 'Larissa Gonçalves', 'larissa.goncalves@aluno.sge.com', '$2y$10$hashficticio14', '888999', '2003-07-29', '14982223345', 5, 'usuario', NULL, 'Aluno', 0, 'none'),
(NULL, 'Rafael Almeida', 'rafael.almeida@aluno.sge.com', '$2y$10$hashficticio15', '999000', '2002-11-03', '14982223346', 8, 'usuario', NULL, 'Aluno', 0, 'none'),

(NULL, 'Sr. Jorge Santos', 'jorge.santos@email.com', '$2y$10$hashficticio16', NULL, '1988-10-10', '11976543210', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none'),
(NULL, 'Sra. Ana Paula', 'ana.paula@email.com', '$2y$10$hashficticio17', NULL, '1995-05-20', '11976543211', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none');


--
-- Atualizando `cursos` com os IDs dos coordenadores (IDs baseados na nova ordem de inserção)
--
UPDATE `cursos` SET `coordenador_id` = 8 WHERE `id` = 1; -- Prof. Carlos Andrade
UPDATE `cursos` SET `coordenador_id` = 9 WHERE `id` = 7; -- Profa. Beatriz Lima
UPDATE `cursos` SET `coordenador_id` = 10 WHERE `id` = 3; -- Prof. Ricardo Souza


--
-- Inserindo dados na tabela `agendamentos` (IDs baseados na nova ordem de inserção)
--
INSERT INTO `agendamentos` (`usuario_id`, `titulo`, `tipo_agendamento`, `esporte_tipo`, `data_agendamento`, `periodo`, `descricao`, `status`, `responsavel_evento`) VALUES
(12, 'Treino Futsal Masculino - FURIOSA', 'esportivo', 'Futsal', '2025-10-06', 'primeiro', 'Treino preparatório para o Intercursos.', 'aprovado', 'Lucas Mendes'),
(13, 'Treino Vôlei Feminino - PREDADORA', 'esportivo', 'Voleibol', '2025-10-06', 'segundo', 'Treino tático e físico.', 'aprovado', 'Julia Alves'),
(14, 'Treino League of Legends - ALFA', 'esportivo', 'League of Legends', '2025-10-07', 'primeiro', 'Treino de estratégias e team play.', 'aprovado', 'Pedro Martins'),
(21, 'Palestra sobre Mercado de Trabalho', 'nao_esportivo', NULL, '2025-10-08', 'primeiro', 'Palestra com convidado externo para alunos.', 'pendente', 'Sr. Jorge Santos'),
(15, 'Treino Basquete - SANGUINÁRIA', 'esportivo', 'Basquetebol', '2025-10-08', 'segundo', 'Foco em arremessos e defesa.', 'aprovado', 'Fernanda Oliveira'),
(9, 'Workshop de Python para iniciantes', 'nao_esportivo', NULL, '2025-10-09', 'primeiro', 'Organizado pelo curso de Ciência da Computação.', 'aprovado', 'Profa. Beatriz Lima'),
(12, 'Amistoso Futsal FURIOSA x ALFA', 'esportivo', 'Futsal', '2025-10-10', 'segundo', 'Jogo amistoso entre as atléticas.', 'aprovado', 'Lucas Mendes'),
(16, 'Reunião da Atlética MAGNA', 'nao_esportivo', NULL, '2025-10-13', 'primeiro', 'Planejamento de eventos do semestre.', 'aprovado', 'Gabriel Pereira'),
(18, 'Uso da quadra para Lazer', 'esportivo', 'Futsal', '2025-10-13', 'segundo', 'Solicitação de aluno para jogo com amigos.', 'rejeitado', 'Bruno Rodrigues'),
(17, 'Treino de Handebol - VENENOSA', 'esportivo', 'Handebol', '2025-10-14', 'primeiro', 'Treino de ataque e contra-ataque.', 'aprovado', 'Mariana Ferreira'),
(7, 'Manutenção da Quadra', 'nao_esportivo', NULL, '2025-10-15', 'primeiro', 'Reserva para manutenção e pintura.', 'aprovado', 'Admin Esportes');


--
-- Inserindo dados na tabela `inscricoes_modalidade` (IDs baseados na nova ordem de inserção)
--
INSERT INTO `inscricoes_modalidade` (`aluno_id`, `modalidade_id`, `atletica_id`, `status`) VALUES
(12, 1, 1, 'aprovado'),(13, 2, 2, 'aprovado'),(14, 12, 6, 'aprovado'),(15, 3, 3, 'aprovado'),(16, 11, 5, 'aprovado'),(17, 4, 8, 'aprovado'),(18, 1, 1, 'pendente'),(19, 2, 2, 'pendente');

--
-- Inserindo dados na tabela `inscricoes_eventos` (FK aponta para `agendamentos`)
--
INSERT INTO `inscricoes_eventos` (`aluno_id`, `evento_id`, `atletica_id`, `status`) VALUES
(12, 7, 1, 'aprovado'),(14, 3, 6, 'aprovado'),(18, 6, 6, 'aprovado'),(19, 6, 6, 'aprovado'),(13, 2, 2, 'aprovado');

--
-- Inserindo dados na tabela `presencas` (IDs baseados na nova ordem de inserção)
--
INSERT INTO `presencas` (`usuario_id`, `agendamento_id`) VALUES
(12, 1),(13, 2),(14, 3),(15, 5),(18, 6),(19, 6),(9, 6);

-- Reativa a verificação de chaves estrangeiras.
SET FOREIGN_KEY_CHECKS=1;
-- Confirma a transação.
COMMIT;