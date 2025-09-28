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
-- Atualizando `cursos` com os IDs dos coordenadores (IDs CORRIGIDOS baseados na ordem de inserção)
--
UPDATE `cursos` SET `coordenador_id` = 7 WHERE `id` = 1; -- Prof. Carlos Andrade (ID 7)
UPDATE `cursos` SET `coordenador_id` = 8 WHERE `id` = 7; -- Profa. Beatriz Lima (ID 8)
UPDATE `cursos` SET `coordenador_id` = 9 WHERE `id` = 3; -- Prof. Ricardo Souza (ID 9)


--
-- Inserindo dados na tabela `agendamentos` (IDs baseados na nova ordem de inserção)
--
INSERT INTO `agendamentos` (`usuario_id`, `titulo`, `tipo_agendamento`, `esporte_tipo`, `data_agendamento`, `periodo`, `descricao`, `status`, `responsavel_evento`, `estimativa_participantes`) VALUES
(11, 'Treino de Férias - Futsal', 'esportivo', 'Futsal', '2025-07-02', 'primeiro', 'Treino leve de manutenção durante as férias.', 'aprovado', 'Lucas Mendes', 15),
(12, 'Jogo Amistoso Vôlei', 'esportivo', 'Voleibol', '2025-07-05', 'segundo', 'Amistoso contra time convidado.', 'aprovado', 'Julia Alves', 20),
(8, 'Curso de Extensão: Programação em R', 'nao_esportivo', NULL, '2025-07-08', 'primeiro', 'Curso de férias para a comunidade.', 'aprovado', 'Profa. Beatriz Lima', 40),
(15, 'Planejamento de Eventos MAGNA', 'nao_esportivo', NULL, '2025-07-10', 'primeiro', 'Reunião de diretoria para o próximo semestre.', 'aprovado', 'Gabriel Pereira', 12),
(11, 'Treino Físico Geral', 'esportivo', 'Atletismo', '2025-07-15', 'segundo', 'Preparação física geral para atletas.', 'aprovado', 'Lucas Mendes', 25),
(20, 'Palestra: Saúde Mental no Esporte', 'nao_esportivo', NULL, '2025-07-18', 'primeiro', 'Palestra com psicólogo convidado.', 'aprovado', 'Sr. Jorge Santos', 80),
(13, 'Campeonato Relâmpago de CS:GO', 'esportivo', 'CS:GO', '2025-07-22', 'segundo', 'Torneio de um dia entre os alunos.', 'aprovado', 'Pedro Martins', 16),
(14, 'Ação Social SANGUINÁRIA', 'nao_esportivo', NULL, '2025-07-26', 'primeiro', 'Campanha de doação de sangue.', 'aprovado', 'Fernanda Oliveira', 100),
(11, 'Volta aos Treinos - Futsal', 'esportivo', 'Futsal', '2025-08-01', 'primeiro', 'Início oficial dos treinos do semestre.', 'aprovado', 'Lucas Mendes', 18),
(12, 'Seletiva Vôlei PREDADORA', 'esportivo', 'Voleibol', '2025-08-04', 'segundo', 'Seleção de novas atletas.', 'aprovado', 'Julia Alves', 30),
(7, 'Aula Magna Engenharia Civil', 'nao_esportivo', NULL, '2025-08-05', 'primeiro', 'Evento de boas-vindas aos calouros.', 'aprovado', 'Prof. Carlos Andrade', 150),
(13, 'Treino Tático Valorant', 'esportivo', NULL, '2025-08-07', 'segundo', 'Análise de mapas e estratégias.', 'aprovado', 'Pedro Martins', 10),
(16, 'Treino Handebol VENENOSA', 'esportivo', 'Handebol', '2025-08-11', 'primeiro', 'Foco em jogadas ensaiadas.', 'aprovado', 'Mariana Ferreira', 14),
(9, 'Simpósio de Direito Penal', 'nao_esportivo', NULL, '2025-08-15', 'primeiro', 'Evento com palestras e debates.', 'aprovado', 'Prof. Ricardo Souza', 120),
(11, 'Treino de Rugby', 'esportivo', NULL, '2025-08-19', 'segundo', 'Treino de contato e táticas de jogo.', 'aprovado', 'Lucas Mendes', 22),
(17, 'Festival de Queimada', 'esportivo', 'Queimada', '2025-08-23', 'primeiro', 'Evento de integração para calouros.', 'aprovado', 'Bruno Rodrigues', 50),
(4, 'Reunião Geral - Admin Atlética', 'nao_esportivo', NULL, '2025-08-26', 'primeiro', 'Alinhamento com a diretoria de esportes.', 'aprovado', 'Admin Atletica Teste', 8),
(12, 'Treino de Polo Aquático', 'esportivo', NULL, '2025-08-28', 'segundo', 'Treino em piscina olímpica.', 'aprovado', 'Julia Alves', 12),
(7, 'Palestra: Engenharia e Inovação', 'nao_esportivo', NULL, '2025-09-02', 'primeiro', 'Evento do curso de Engenharia de Produção.', 'aprovado', 'Prof. Carlos Andrade', 90),
(11, 'Jogo-Treino Futsal vs SANGUINÁRIA', 'esportivo', 'Futsal', '2025-09-05', 'segundo', 'Jogo preparatório.', 'aprovado', 'Lucas Mendes', 35),
(8, 'Treino de Cobertura de Eventos', 'nao_esportivo', NULL, '2025-09-09', 'primeiro', 'Atividade prática para alunos de Jornalismo.', 'aprovado', 'Profa. Beatriz Lima', 25),
(13, 'Treino Cancelado (Chuva)', 'esportivo', 'League of Legends', '2025-09-11', 'segundo', 'Treino cancelado por problemas na rede elétrica.', 'cancelado', 'Pedro Martins', 8),
(15, 'Semana do Administrador', 'nao_esportivo', NULL, '2025-09-16', 'primeiro', 'Ciclo de palestras e workshops.', 'aprovado', 'Gabriel Pereira', 60),
(4, 'Manutenção do E-Sports', 'nao_esportivo', NULL, '2025-09-23', 'segundo', 'Atualização dos computadores da sala de e-sports.', 'aprovado', 'Admin Atletica Teste', 5),
(18, 'Cine Debate - Psicologia', 'nao_esportivo', NULL, '2025-09-26', 'primeiro', 'Exibição de filme seguida de debate.', 'aprovado', 'Larissa Gonçalves', 45),
(11, 'Treino Futsal Masculino - FURIOSA', 'esportivo', 'Futsal', '2025-10-06', 'primeiro', 'Treino preparatório para o Intercursos.', 'aprovado', 'Lucas Mendes', 20),
(12, 'Treino Vôlei Feminino - PREDADORA', 'esportivo', 'Voleibol', '2025-10-06', 'segundo', 'Treino tático e físico.', 'aprovado', 'Julia Alves', 16),
(13, 'Treino League of Legends - ALFA', 'esportivo', 'League of Legends', '2025-10-07', 'primeiro', 'Treino de estratégias e team play.', 'aprovado', 'Pedro Martins', 10),
(20, 'Palestra sobre Mercado de Trabalho', 'nao_esportivo', NULL, '2025-10-08', 'primeiro', 'Palestra com convidado externo para alunos.', 'pendente', 'Sr. Jorge Santos', 75),
(14, 'Treino Basquete - SANGUINÁRIA', 'esportivo', 'Basquetebol', '2025-10-08', 'segundo', 'Foco em arremessos e defesa.', 'aprovado', 'Fernanda Oliveira', 12),
(8, 'Workshop de Python para iniciantes', 'nao_esportivo', NULL, '2025-10-09', 'primeiro', 'Organizado pelo curso de Ciência da Computação.', 'aprovado', 'Profa. Beatriz Lima', 30),
(11, 'Amistoso Futsal FURIOSA x ALFA', 'esportivo', 'Futsal', '2025-10-10', 'segundo', 'Jogo amistoso entre as atléticas.', 'aprovado', 'Lucas Mendes', 40),
(15, 'Reunião da Atlética MAGNA', 'nao_esportivo', NULL, '2025-10-13', 'primeiro', 'Planejamento de eventos do semestre.', 'aprovado', 'Gabriel Pereira', 15),
(17, 'Uso da quadra para Lazer', 'esportivo', 'Futsal', '2025-10-13', 'segundo', 'Solicitação de aluno para jogo com amigos.', 'rejeitado', 'Bruno Rodrigues', 8),
(16, 'Treino de Handebol - VENENOSA', 'esportivo', 'Handebol', '2025-10-14', 'primeiro', 'Treino de ataque e contra-ataque.', 'aprovado', 'Mariana Ferreira', 18),
(6, 'Manutenção da Quadra', 'nao_esportivo', NULL, '2025-10-15', 'primeiro', 'Reserva para manutenção e pintura.', 'aprovado', 'Admin Esportes', 3);


--
-- Inserindo dados na tabela `inscricoes_modalidade` (IDs baseados na nova ordem de inserção)
--
INSERT INTO `inscricoes_modalidade` (`aluno_id`, `modalidade_id`, `atletica_id`, `status`) VALUES
(11, 1, 1, 'aprovado'),(12, 2, 2, 'aprovado'),(13, 12, 6, 'aprovado'),(14, 3, 3, 'aprovado'),(15, 11, 5, 'aprovado'),(16, 4, 8, 'aprovado'),(17, 1, 1, 'pendente'),(18, 2, 2, 'pendente');

--
-- Inserindo dados na tabela `inscricoes_eventos` (FK aponta para `agendamentos`)
--
INSERT INTO `inscricoes_eventos` (`aluno_id`, `evento_id`, `atletica_id`, `status`) VALUES
(11, 7, 1, 'aprovado'),
(13, 3, 6, 'aprovado'),
(17, 6, 6, 'aprovado'),
(18, 6, 6, 'aprovado'),
(12, 2, 2, 'aprovado');

--
-- Inserindo dados na tabela `presencas` (IDs baseados na nova ordem de inserção)
-- Usuários válidos: 1-21 (Super Admin até Sra. Ana Paula)
-- Eventos passados (julho-setembro) com várias presenças
--
INSERT INTO `presencas` (`usuario_id`, `agendamento_id`) VALUES
-- Evento 1: Treino de Férias - Futsal (11 pessoas confirmaram)
(11, 1),(12, 1),(13, 1),(14, 1),(15, 1),(16, 1),(17, 1),(2, 1),(3, 1),(4, 1),(20, 1),

-- Evento 2: Jogo Amistoso Vôlei (8 pessoas confirmaram)
(12, 2),(13, 2),(14, 2),(16, 2),(18, 2),(19, 2),(3, 2),(4, 2),

-- Evento 3: Curso de Extensão: Programação em R (15 pessoas confirmaram)
(8, 3),(11, 3),(13, 3),(15, 3),(17, 3),(18, 3),(19, 3),(2, 3),(3, 3),(4, 3),(20, 3),(21, 3),(7, 3),(9, 3),(10, 3),

-- Evento 4: Planejamento de Eventos MAGNA (5 pessoas confirmaram)
(15, 4),(11, 4),(13, 4),(16, 4),(18, 4),

-- Evento 5: Treino Físico Geral (12 pessoas confirmaram)
(11, 5),(12, 5),(13, 5),(14, 5),(16, 5),(17, 5),(18, 5),(2, 5),(3, 5),(4, 5),(19, 5),(20, 5),

-- Evento 6: Palestra: Saúde Mental no Esporte (18 pessoas confirmaram)
(20, 6),(11, 6),(12, 6),(13, 6),(14, 6),(15, 6),(16, 6),(17, 6),(18, 6),(19, 6),(2, 6),(3, 6),(4, 6),(7, 6),(8, 6),(9, 6),(10, 6),(21, 6),

-- Evento 7: Campeonato Relâmpago de CS:GO (9 pessoas confirmaram)
(13, 7),(11, 7),(17, 7),(18, 7),(19, 7),(2, 7),(3, 7),(4, 7),(15, 7),

-- Evento 8: Ação Social SANGUINÁRIA (21 pessoas confirmaram - quase todos os usuários!)
(14, 8),(11, 8),(12, 8),(13, 8),(15, 8),(16, 8),(17, 8),(18, 8),(19, 8),(20, 8),(21, 8),(2, 8),(3, 8),(4, 8),(7, 8),(8, 8),(9, 8),(10, 8),(6, 8),(5, 8),(1, 8),

-- Eventos futuros (outubro) - algumas pessoas já confirmaram presença
-- Evento 18: Treino Futsal Masculino - FURIOSA (6 pessoas já confirmaram)
(11, 18),(13, 18),(17, 18),(2, 18),(3, 18),(15, 18),

-- Evento 19: Treino Vôlei Feminino - PREDADORA (4 pessoas já confirmaram)
(12, 19),(14, 19),(16, 19),(18, 19),

-- Evento 20: Treino League of Legends - ALFA (7 pessoas já confirmaram)
(13, 20),(11, 20),(17, 20),(19, 20),(2, 20),(3, 20),(4, 20),

-- Evento 22: Treino Basquete - SANGUINÁRIA (5 pessoas já confirmaram)
(14, 22),(11, 22),(16, 22),(18, 22),(20, 22),

-- Evento 23: Workshop de Python para iniciantes (12 pessoas já confirmaram)
(8, 23),(11, 23),(13, 23),(17, 23),(19, 23),(2, 23),(3, 23),(4, 23),(15, 23),(18, 23),(20, 23),(21, 23),

-- Evento 24: Amistoso Futsal FURIOSA x ALFA (8 pessoas já confirmaram)
(11, 24),(13, 24),(17, 24),(12, 24),(14, 24),(16, 24),(2, 24),(3, 24),

-- Evento 25: Reunião da Atlética MAGNA (3 pessoas já confirmaram)
(15, 25),(18, 25),(20, 25),

-- Evento 27: Treino de Handebol - VENENOSA (6 pessoas já confirmaram)
(16, 27),(14, 27),(12, 27),(18, 27),(19, 27),(20, 27);

-- Reativa a verificação de chaves estrangeiras.
SET FOREIGN_KEY_CHECKS=1;
-- Confirma a transação.
COMMIT;