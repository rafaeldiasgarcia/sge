-- SGE-DB POPULATE SCRIPT (ESTRUTURA NOVA + USUÁRIOS DE TESTE)
-- Desativa a verificação de chaves estrangeiras para permitir a inserção de dados.
SET FOREIGN_KEY_CHECKS=0;
-- Inicia uma transação.
START TRANSACTION;

-- Limpando dados existentes para evitar duplicatas e garantir um ambiente limpo
DELETE FROM `presencas`;
DELETE FROM `inscricoes_eventos`;
DELETE FROM `inscricoes_modalidade`;
DELETE FROM `notificacoes`;
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
ALTER TABLE `notificacoes` AUTO_INCREMENT = 1;


--
-- Inserindo dados na tabela `atleticas` (NOVA ESTRUTURA)
--
INSERT INTO `atleticas` (`id`, `nome`) VALUES
                                           (1, 'A.A.A. TOURADA'),
                                           (2, 'A.A.A. ÁGUIAS'),
                                           (3, 'A.A.A. SOBERANOS'),
                                           (4, 'A.A.A. DEVORADORES'),
                                           (5, 'A.A.A. CASTORES'),
                                           (6, 'A.A.A. SERPENTES'),
                                           (7, 'A.A.A. RAPOSADA'),
                                           (8, 'A.A.A. FORASTEIROS'),
                                           (9, 'A.A.A. GORILADA'),
                                           (10, 'A.A.A. RATOLOUCO'),
                                           (11, 'A.A.A. OLIMPO'),
                                           (12, 'A.A.A. JAVALOUCOS'),
                                           (13, 'A.A.A. LEÕES'),
                                           (14, 'A.A.A. EDUCALOUCOS'),
                                           (15, 'A.A.A. ZANGADOS'),
                                           (16, 'A.A.A. OCTORMENTA');

--
-- Inserindo dados na tabela `modalidades` (sem alteração)
--
INSERT INTO `modalidades` (`id`, `nome`) VALUES
                                             (1, 'Futsal'),(2, 'Voleibol'),(3, 'Basquetebol'),(4, 'Handebol'),(5, 'Natação'),(6, 'Atletismo'),(7, 'Judô'),(8, 'Karatê'),(9, 'Tênis de Mesa'),(10, 'Tênis de Campo'),(11, 'Xadrez'),(12, 'League of Legends'),(13, 'CS:GO'),(14, 'Vôlei de Praia'),(15, 'Queimada');

--
-- Inserindo dados na tabela `cursos` (NOVA ESTRUTURA)
--
INSERT INTO `cursos` (`id`, `nome`, `atletica_id`, `coordenador_id`) VALUES
                                                                         (1, 'Medicina Veterinária', 1, NULL),
                                                                         (2, 'Engenharia de Software', 2, NULL),
                                                                         (3, 'Biomedicina', 3, NULL),
                                                                         (4, 'Nutrição', 4, NULL),
                                                                         (5, 'Arquitetura', 5, NULL),
                                                                         (6, 'Enfermagem', 6, NULL),
                                                                         (7, 'Direito', 7, NULL),
                                                                         (8, 'Agronomia', 8, NULL),
                                                                         (9, 'Administração', 9, NULL),
                                                                         (10, 'Psicologia', 10, NULL),
                                                                         (11, 'Fisioterapia', 11, NULL),
                                                                         (12, 'Engenharia', 12, NULL),
                                                                         (13, 'Contábeis', 13, NULL),
                                                                         (14, 'Educação Física', 14, NULL),
                                                                         (15, 'Biologia', 15, NULL),
                                                                         (16, 'Terapia Ocupacional', 16, NULL);

--
-- Inserindo dados na tabela `usuarios` (CURSOS E ATLÉTICAS ATUALIZADOS)
--
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ra`, `data_nascimento`, `telefone`, `curso_id`, `role`, `atletica_id`, `tipo_usuario_detalhado`, `is_coordenador`, `atletica_join_status`) VALUES
                                                                                                                                                                                                        (NULL, 'Super Admin', 'sadmin', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, NULL, NULL, NULL, 'superadmin', NULL, NULL, 0, 'none'),
                                                                                                                                                                                                        (NULL, 'Aluno Teste', 'aluno@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '123456', '2004-08-15', '(14) 99123-4567', 12, 'usuario', NULL, 'Aluno', 0, 'none'), -- Engenharia
                                                                                                                                                                                                        (NULL, 'Membro Atletica Teste', 'membro@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '789012', '2003-05-20', '(14) 99765-4321', 2, 'usuario', 2, 'Membro das Atleticas', 0, 'aprovado'), -- Eng. Software / ÁGUIAS
                                                                                                                                                                                                        (NULL, 'Admin Atletica Teste', 'admin.atletica@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '345678', '2002-02-10', '(14) 98888-7777', 7, 'admin', 7, 'Membro das Atleticas', 0, 'aprovado'), -- Direito / RAPOSADA
                                                                                                                                                                                                        (NULL, 'Comunidade Externa Teste', 'comunidade@email.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1990-11-30', '(11) 97777-8888', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none'),
                                                                                                                                                                                                        (NULL, 'Admin Esportes', 'admin@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1992-05-10', '11987654322', NULL, 'admin', NULL, NULL, 0, 'none'),
                                                                                                                                                                                                        (NULL, 'Prof. Carlos Andrade', 'carlos.andrade@prof.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1975-03-15', '14991234567', 12, 'usuario', NULL, 'Professor', 1, 'none'), -- Engenharia
                                                                                                                                                                                                        (NULL, 'Profa. Beatriz Lima', 'beatriz.lima@prof.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1980-11-20', '14991234568', 2, 'usuario', NULL, 'Professor', 1, 'none'), -- Eng. Software
                                                                                                                                                                                                        (NULL, 'Prof. Ricardo Souza', 'ricardo.souza@prof.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1968-07-08', '14991234569', 7, 'usuario', NULL, 'Professor', 1, 'none'), -- Direito
                                                                                                                                                                                                        (NULL, 'Profa. Helena Costa', 'helena.costa@prof.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1985-02-25', '14991234570', 1, 'usuario', NULL, 'Professor', 0, 'none'), -- Medicina Veterinária
                                                                                                                                                                                                        (NULL, 'Lucas Mendes', 'lucas.mendes@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '111222', '2004-06-30', '14981112233', 12, 'usuario', 12, 'Membro das Atleticas', 0, 'aprovado'), -- Engenharia / JAVALOUCOS
                                                                                                                                                                                                        (NULL, 'Julia Alves', 'julia.alves@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '222333', '2003-09-12', '14981112234', 7, 'usuario', 7, 'Membro das Atleticas', 0, 'aprovado'), -- Direito / RAPOSADA
                                                                                                                                                                                                        (NULL, 'Pedro Martins', 'pedro.martins@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '333444', '2002-12-01', '14981112235', 2, 'usuario', 2, 'Membro das Atleticas', 0, 'aprovado'), -- Eng. Software / ÁGUIAS
                                                                                                                                                                                                        (NULL, 'Fernanda Oliveira', 'fernanda.oliveira@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '444555', '2004-04-18', '14981112236', 1, 'usuario', 1, 'Membro das Atleticas', 0, 'aprovado'), -- Med. Veterinária / TOURADA
                                                                                                                                                                                                        (NULL, 'Gabriel Pereira', 'gabriel.pereira@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '555666', '2003-01-22', '14981112237', 9, 'usuario', 9, 'Membro das Atleticas', 0, 'aprovado'), -- Administração / GORILADA
                                                                                                                                                                                                        (NULL, 'Mariana Ferreira', 'mariana.ferreira@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '666777', '2005-08-05', '14981112238', 3, 'usuario', 3, 'Membro das Atleticas', 0, 'aprovado'), -- Biomedicina / SOBERANOS
                                                                                                                                                                                                        (NULL, 'Bruno Rodrigues', 'bruno.rodrigues@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '777888', '2004-02-14', '14982223344', 2, 'usuario', NULL, 'Aluno', 0, 'none'), -- Eng. Software
                                                                                                                                                                                                        (NULL, 'Larissa Gonçalves', 'larissa.goncalves@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '888999', '2003-07-29', '14982223345', 10, 'usuario', NULL, 'Aluno', 0, 'none'), -- Psicologia
                                                                                                                                                                                                        (NULL, 'Rafael Almeida', 'rafael.almeida@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '999000', '2002-11-03', '14982223346', 5, 'usuario', NULL, 'Aluno', 0, 'none'), -- Arquitetura
                                                                                                                                                                                                        (NULL, 'Sr. Jorge Santos', 'jorge.santos@email.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1988-10-10', '11976543210', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none'),
                                                                                                                                                                                                        (NULL, 'Sra. Ana Paula', 'ana.paula@email.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1995-05-20', '11976543211', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none');

--
-- Atualizando `cursos` com os IDs dos coordenadores (IDs CORRIGIDOS baseados na ordem de inserção)
--
UPDATE `cursos` SET `coordenador_id` = 7 WHERE `id` = 12; -- Prof. Carlos Andrade (ID 7) para Engenharia (ID 12)
UPDATE `cursos` SET `coordenador_id` = 8 WHERE `id` = 2;  -- Profa. Beatriz Lima (ID 8) para Eng. Software (ID 2)
UPDATE `cursos` SET `coordenador_id` = 9 WHERE `id` = 7;  -- Prof. Ricardo Souza (ID 9) para Direito (ID 7)


--
-- Inserindo dados na tabela `agendamentos` (Nomes das atléticas atualizados)
--
INSERT INTO `agendamentos` (`usuario_id`, `titulo`, `tipo_agendamento`, `esporte_tipo`, `data_agendamento`, `periodo`, `descricao`, `status`, `responsavel_evento`, `estimativa_participantes`) VALUES
-- JULHO 2025 - Período de Férias
(11, 'Treino de Férias - Futsal', 'esportivo', 'Futsal', '2025-07-02', 'primeiro', 'Treino leve de manutenção durante as férias.', 'finalizado', 'Lucas Mendes', 15),
(12, 'Jogo Amistoso Vôlei', 'esportivo', 'Voleibol', '2025-07-05', 'segundo', 'Amistoso contra time convidado.', 'finalizado', 'Julia Alves', 20),
(8, 'Curso de Extensão: Programação em R', 'nao_esportivo', NULL, '2025-07-08', 'primeiro', 'Curso de férias para a comunidade.', 'finalizado', 'Profa. Beatriz Lima', 40),
(15, 'Planejamento de Eventos GORILADA', 'nao_esportivo', NULL, '2025-07-10', 'primeiro', 'Reunião de diretoria para o próximo semestre.', 'finalizado', 'Gabriel Pereira', 12),
(11, 'Treino Físico Geral', 'esportivo', 'Atletismo', '2025-07-15', 'segundo', 'Preparação física geral para atletas.', 'finalizado', 'Lucas Mendes', 25),
(20, 'Palestra: Saúde Mental no Esporte', 'nao_esportivo', NULL, '2025-07-18', 'primeiro', 'Palestra com psicólogo convidado.', 'finalizado', 'Sr. Jorge Santos', 80),
(13, 'Campeonato Relâmpago de CS:GO', 'esportivo', 'CS:GO', '2025-07-22', 'segundo', 'Torneio de um dia entre os alunos.', 'finalizado', 'Pedro Martins', 16),
(14, 'Ação Social TOURADA', 'nao_esportivo', NULL, '2025-07-26', 'primeiro', 'Campanha de doação de sangue.', 'finalizado', 'Fernanda Oliveira', 100),
(16, 'Treino de Handebol Feminino', 'esportivo', 'Handebol', '2025-07-29', 'segundo', 'Treino técnico e tático.', 'finalizado', 'Mariana Ferreira', 16),

-- AGOSTO 2025 - Início do Semestre
(11, 'Volta aos Treinos - Futsal', 'esportivo', 'Futsal', '2025-08-01', 'primeiro', 'Início oficial dos treinos do semestre.', 'finalizado', 'Lucas Mendes', 18),
(12, 'Seletiva Vôlei RAPOSADA', 'esportivo', 'Voleibol', '2025-08-04', 'segundo', 'Seleção de novas atletas.', 'finalizado', 'Julia Alves', 30),
(7, 'Aula Magna Engenharia', 'nao_esportivo', NULL, '2025-08-05', 'primeiro', 'Evento de boas-vindas aos calouros.', 'finalizado', 'Prof. Carlos Andrade', 150),
(13, 'Treino Tático Valorant', 'esportivo', NULL, '2025-08-07', 'segundo', 'Análise de mapas e estratégias.', 'finalizado', 'Pedro Martins', 10),
(16, 'Treino Handebol SOBERANOS', 'esportivo', 'Handebol', '2025-08-11', 'primeiro', 'Foco em jogadas ensaiadas.', 'finalizado', 'Mariana Ferreira', 14),
(9, 'Simpósio de Direito Penal', 'nao_esportivo', NULL, '2025-08-15', 'primeiro', 'Evento com palestras e debates.', 'finalizado', 'Prof. Ricardo Souza', 120),
(11, 'Treino de Rugby', 'esportivo', NULL, '2025-08-19', 'segundo', 'Treino de contato e táticas de jogo.', 'finalizado', 'Lucas Mendes', 22),
(17, 'Festival de Queimada', 'esportivo', 'Queimada', '2025-08-23', 'primeiro', 'Evento de integração para calouros.', 'finalizado', 'Bruno Rodrigues', 50),
(4, 'Reunião Geral - Admin Atlética', 'nao_esportivo', NULL, '2025-08-26', 'primeiro', 'Alinhamento com a diretoria de esportes.', 'finalizado', 'Admin Atletica Teste', 8),
(12, 'Treino de Polo Aquático', 'esportivo', NULL, '2025-08-28', 'segundo', 'Treino em piscina olímpica.', 'finalizado', 'Julia Alves', 12),
(14, 'Treino Basquete TOURADA', 'esportivo', 'Basquetebol', '2025-08-30', 'primeiro', 'Fundamentos e jogadas.', 'finalizado', 'Fernanda Oliveira', 15),

-- SETEMBRO 2025
(7, 'Palestra: Engenharia e Inovação', 'nao_esportivo', NULL, '2025-09-02', 'primeiro', 'Evento do curso de Engenharia.', 'finalizado', 'Prof. Carlos Andrade', 90),
(11, 'Jogo-Treino Futsal vs TOURADA', 'esportivo', 'Futsal', '2025-09-05', 'segundo', 'Jogo preparatório.', 'finalizado', 'Lucas Mendes', 35),
(8, 'Treino de Cobertura de Eventos', 'nao_esportivo', NULL, '2025-09-09', 'primeiro', 'Atividade prática para alunos de Jornalismo.', 'finalizado', 'Profa. Beatriz Lima', 25),
(13, 'Treino Cancelado (Chuva)', 'esportivo', 'League of Legends', '2025-09-11', 'segundo', 'Treino cancelado por problemas na rede elétrica.', 'cancelado', 'Pedro Martins', 8),
(15, 'Semana do Administrador', 'nao_esportivo', NULL, '2025-09-16', 'primeiro', 'Ciclo de palestras e workshops.', 'finalizado', 'Gabriel Pereira', 60),
(12, 'Amistoso Vôlei vs OLIMPO', 'esportivo', 'Voleibol', '2025-09-18', 'segundo', 'Jogo amistoso entre atléticas.', 'finalizado', 'Julia Alves', 28),
(16, 'Torneio de Handebol Feminino', 'esportivo', 'Handebol', '2025-09-20', 'primeiro', 'Primeira rodada do torneio interno.', 'finalizado', 'Mariana Ferreira', 32),
(4, 'Manutenção do E-Sports', 'nao_esportivo', NULL, '2025-09-23', 'segundo', 'Atualização dos computadores da sala de e-sports.', 'finalizado', 'Admin Atletica Teste', 5),
(18, 'Cine Debate - Psicologia', 'nao_esportivo', NULL, '2025-09-26', 'primeiro', 'Exibição de filme seguida de debate.', 'finalizado', 'Larissa Gonçalves', 45),
(11, 'Treino Intensivo Futsal', 'esportivo', 'Futsal', '2025-09-29', 'segundo', 'Preparação para o Intercursos.', 'finalizado', 'Lucas Mendes', 20),

-- OUTUBRO 2025 - Mês Atual
(11, 'Treino Futsal Masculino - JAVALOUCOS', 'esportivo', 'Futsal', '2025-10-01', 'primeiro', 'Treino preparatório para o Intercursos.', 'finalizado', 'Lucas Mendes', 20),
(12, 'Treino Vôlei Feminino - RAPOSADA', 'esportivo', 'Voleibol', '2025-10-02', 'segundo', 'Treino tático e físico.', 'finalizado', 'Julia Alves', 16),
(13, 'Treino League of Legends - ÁGUIAS', 'esportivo', 'League of Legends', '2025-10-03', 'primeiro', 'Treino de estratégias e team play.', 'aprovado', 'Pedro Martins', 10),
(20, 'Palestra sobre Mercado de Trabalho', 'nao_esportivo', NULL, '2025-10-04', 'primeiro', 'Palestra com convidado externo para alunos.', 'aprovado', 'Sr. Jorge Santos', 75),
(14, 'Treino Basquete - TOURADA', 'esportivo', 'Basquetebol', '2025-10-04', 'segundo', 'Foco em arremessos e defesa.', 'aprovado', 'Fernanda Oliveira', 12),
(8, 'Workshop de Python para iniciantes', 'nao_esportivo', NULL, '2025-10-05', 'primeiro', 'Organizado pelo curso de Engenharia de Software.', 'aprovado', 'Profa. Beatriz Lima', 30),
(11, 'Amistoso Futsal JAVALOUCOS x ÁGUIAS', 'esportivo', 'Futsal', '2025-10-06', 'segundo', 'Jogo amistoso entre as atléticas.', 'aprovado', 'Lucas Mendes', 40),
(15, 'Reunião da Atlética GORILADA', 'nao_esportivo', NULL, '2025-10-07', 'primeiro', 'Planejamento de eventos do semestre.', 'aprovado', 'Gabriel Pereira', 15),
(17, 'Uso da quadra para Lazer', 'esportivo', 'Futsal', '2025-10-08', 'segundo', 'Solicitação de aluno para jogo com amigos.', 'rejeitado', 'Bruno Rodrigues', 8),
(16, 'Treino de Handebol - SOBERANOS', 'esportivo', 'Handebol', '2025-10-09', 'primeiro', 'Treino de ataque e contra-ataque.', 'aprovado', 'Mariana Ferreira', 18),
(6, 'Manutenção da Quadra', 'nao_esportivo', NULL, '2025-10-10', 'primeiro', 'Reserva para manutenção e pintura.', 'aprovado', 'Admin Esportes', 3),
(13, 'Campeonato CS:GO - Semifinal', 'esportivo', 'CS:GO', '2025-10-11', 'segundo', 'Semifinal do campeonato interno.', 'aprovado', 'Pedro Martins', 20),
(12, 'Treino Vôlei de Praia', 'esportivo', 'Vôlei de Praia', '2025-10-12', 'primeiro', 'Treino na quadra externa.', 'aprovado', 'Julia Alves', 12),
(7, 'Palestra: Gestão de Projetos', 'nao_esportivo', NULL, '2025-10-13', 'segundo', 'Palestra para alunos de Engenharia.', 'aprovado', 'Prof. Carlos Andrade', 65),
(11, 'Jogo-Treino Futsal vs SERPENTES', 'esportivo', 'Futsal', '2025-10-14', 'primeiro', 'Preparação tática para o campeonato.', 'aprovado', 'Lucas Mendes', 30),
(14, 'Workshop de Primeiros Socorros', 'nao_esportivo', NULL, '2025-10-15', 'segundo', 'Capacitação para membros das atléticas.', 'aprovado', 'Fernanda Oliveira', 40),
(18, 'Palestra: Ansiedade e Desempenho', 'nao_esportivo', NULL, '2025-10-16', 'primeiro', 'Evento do curso de Psicologia.', 'aprovado', 'Larissa Gonçalves', 55),
(16, 'Torneio de Handebol - Quartas', 'esportivo', 'Handebol', '2025-10-17', 'segundo', 'Quartas de final do torneio.', 'aprovado', 'Mariana Ferreira', 35),
(9, 'Júri Simulado - Direito', 'nao_esportivo', NULL, '2025-10-18', 'primeiro', 'Atividade prática do curso de Direito.', 'aprovado', 'Prof. Ricardo Souza', 80),
(13, 'Treino League of Legends - ÁGUIAS', 'esportivo', 'League of Legends', '2025-10-19', 'segundo', 'Análise de partidas anteriores.', 'aprovado', 'Pedro Martins', 10),
(11, 'Treino Futsal - JAVALOUCOS', 'esportivo', 'Futsal', '2025-10-20', 'primeiro', 'Treino de finalizações.', 'aprovado', 'Lucas Mendes', 18),
(21, 'Aula de Yoga para Atletas', 'esportivo', NULL, '2025-10-21', 'segundo', 'Atividade de relaxamento e alongamento.', 'aprovado', 'Sra. Ana Paula', 25),
(12, 'Seletiva Final Vôlei', 'esportivo', 'Voleibol', '2025-10-22', 'primeiro', 'Definição do time principal.', 'aprovado', 'Julia Alves', 24),
(8, 'Hackathon Universitário', 'nao_esportivo', NULL, '2025-10-23', 'segundo', 'Maratona de programação.', 'aprovado', 'Profa. Beatriz Lima', 50),
(15, 'Evento de Networking', 'nao_esportivo', NULL, '2025-10-24', 'primeiro', 'Conexões para futuros profissionais.', 'pendente', 'Gabriel Pereira', 70),
(14, 'Amistoso Basquete vs LEÕES', 'esportivo', 'Basquetebol', '2025-10-25', 'segundo', 'Jogo preparatório.', 'pendente', 'Fernanda Oliveira', 22),
(4, 'Reunião de Coordenadores', 'nao_esportivo', NULL, '2025-10-26', 'primeiro', 'Alinhamento mensal.', 'pendente', 'Admin Atletica Teste', 10),
(11, 'Treino Tático Futsal', 'esportivo', 'Futsal', '2025-10-27', 'segundo', 'Estratégias de jogo.', 'pendente', 'Lucas Mendes', 20),
(16, 'Treino Handebol - SOBERANOS', 'esportivo', 'Handebol', '2025-10-28', 'primeiro', 'Treino de defesa.', 'pendente', 'Mariana Ferreira', 16),
(13, 'Final CS:GO', 'esportivo', 'CS:GO', '2025-10-29', 'segundo', 'Grande final do campeonato.', 'pendente', 'Pedro Martins', 30),
(7, 'Semana da Engenharia', 'nao_esportivo', NULL, '2025-10-30', 'primeiro', 'Abertura da semana temática.', 'pendente', 'Prof. Carlos Andrade', 100),
(12, 'Treino Vôlei Feminino', 'esportivo', 'Voleibol', '2025-10-31', 'segundo', 'Preparação para jogos de novembro.', 'pendente', 'Julia Alves', 18),

-- NOVEMBRO 2025 - Eventos Futuros
(11, 'Intercursos - Abertura Futsal', 'esportivo', 'Futsal', '2025-11-01', 'primeiro', 'Jogo de abertura do Intercursos.', 'pendente', 'Lucas Mendes', 50),
(14, 'Intercursos - Basquete Fase 1', 'esportivo', 'Basquetebol', '2025-11-02', 'segundo', 'Primeira fase do torneio.', 'pendente', 'Fernanda Oliveira', 40),
(12, 'Intercursos - Vôlei Fase 1', 'esportivo', 'Voleibol', '2025-11-03', 'primeiro', 'Jogos da primeira fase.', 'pendente', 'Julia Alves', 45),
(16, 'Intercursos - Handebol Fase 1', 'esportivo', 'Handebol', '2025-11-04', 'segundo', 'Início do torneio de handebol.', 'pendente', 'Mariana Ferreira', 38),
(13, 'Intercursos - E-sports LoL', 'esportivo', 'League of Legends', '2025-11-05', 'primeiro', 'Torneio de League of Legends.', 'pendente', 'Pedro Martins', 25),
(11, 'Intercursos - Futsal Quartas', 'esportivo', 'Futsal', '2025-11-06', 'segundo', 'Quartas de final de futsal.', 'pendente', 'Lucas Mendes', 55),
(17, 'Intercursos - Queimada', 'esportivo', 'Queimada', '2025-11-07', 'primeiro', 'Torneio de queimada misto.', 'pendente', 'Bruno Rodrigues', 60),
(12, 'Intercursos - Vôlei Semifinal', 'esportivo', 'Voleibol', '2025-11-08', 'segundo', 'Semifinais de vôlei.', 'pendente', 'Julia Alves', 50),
(20, 'Palestra: Empreendedorismo', 'nao_esportivo', NULL, '2025-11-09', 'primeiro', 'Palestra com empresário local.', 'pendente', 'Sr. Jorge Santos', 85),
(14, 'Intercursos - Basquete Semifinal', 'esportivo', 'Basquetebol', '2025-11-10', 'segundo', 'Semifinais de basquete.', 'pendente', 'Fernanda Oliveira', 48),
(11, 'Intercursos - Futsal Semifinal', 'esportivo', 'Futsal', '2025-11-11', 'primeiro', 'Semifinais de futsal.', 'pendente', 'Lucas Mendes', 60),
(16, 'Intercursos - Handebol Semifinal', 'esportivo', 'Handebol', '2025-11-12', 'segundo', 'Semifinais de handebol.', 'pendente', 'Mariana Ferreira', 42),
(8, 'Workshop: Inteligência Artificial', 'nao_esportivo', NULL, '2025-11-13', 'primeiro', 'Introdução a IA e Machine Learning.', 'pendente', 'Profa. Beatriz Lima', 45),
(12, 'Intercursos - Final Vôlei', 'esportivo', 'Voleibol', '2025-11-14', 'segundo', 'Grande final de vôlei feminino.', 'pendente', 'Julia Alves', 70),
(14, 'Intercursos - Final Basquete', 'esportivo', 'Basquetebol', '2025-11-15', 'primeiro', 'Grande final de basquete.', 'pendente', 'Fernanda Oliveira', 65),
(11, 'Intercursos - Final Futsal', 'esportivo', 'Futsal', '2025-11-16', 'segundo', 'Grande final de futsal masculino.', 'pendente', 'Lucas Mendes', 80),
(16, 'Intercursos - Final Handebol', 'esportivo', 'Handebol', '2025-11-17', 'primeiro', 'Grande final de handebol.', 'pendente', 'Mariana Ferreira', 55),
(6, 'Intercursos - Cerimônia de Encerramento', 'nao_esportivo', NULL, '2025-11-18', 'segundo', 'Premiação e encerramento do Intercursos.', 'pendente', 'Admin Esportes', 150),
(15, 'Feira de Profissões', 'nao_esportivo', NULL, '2025-11-20', 'primeiro', 'Evento para calouros e comunidade.', 'pendente', 'Gabriel Pereira', 120),
(9, 'Seminário de Direito Constitucional', 'nao_esportivo', NULL, '2025-11-21', 'segundo', 'Evento acadêmico do curso.', 'pendente', 'Prof. Ricardo Souza', 75),
(18, 'Workshop: Saúde Mental na Universidade', 'nao_esportivo', NULL, '2025-11-22', 'primeiro', 'Atividade do curso de Psicologia.', 'pendente', 'Larissa Gonçalves', 50),
(11, 'Confraternização JAVALOUCOS', 'nao_esportivo', NULL, '2025-11-24', 'primeiro', 'Evento de confraternização da atlética.', 'pendente', 'Lucas Mendes', 35),
(12, 'Confraternização RAPOSADA', 'nao_esportivo', NULL, '2025-11-25', 'segundo', 'Festa de encerramento do semestre.', 'pendente', 'Julia Alves', 40),
(7, 'Última Aula - Formandos Engenharia', 'nao_esportivo', NULL, '2025-11-27', 'primeiro', 'Despedida dos formandos.', 'pendente', 'Prof. Carlos Andrade', 80),
(13, 'Torneio de Xadrez', 'esportivo', 'Xadrez', '2025-11-28', 'segundo', 'Campeonato interno de xadrez.', 'pendente', 'Pedro Martins', 20),
(21, 'Aula Aberta de Meditação', 'nao_esportivo', NULL, '2025-11-29', 'primeiro', 'Técnicas de mindfulness para estudantes.', 'pendente', 'Sra. Ana Paula', 30);

--
-- Inserindo dados na tabela `inscricoes_modalidade` (IDs das atléticas atualizados)
--
INSERT INTO `inscricoes_modalidade` (`aluno_id`, `modalidade_id`, `atletica_id`, `status`) VALUES
                                                                                               (11, 1, 12, 'aprovado'), -- Lucas Mendes / JAVALOUCOS
                                                                                               (12, 2, 7, 'aprovado'),  -- Julia Alves / RAPOSADA
                                                                                               (13, 12, 2, 'aprovado'), -- Pedro Martins / ÁGUIAS
                                                                                               (14, 3, 1, 'aprovado'),  -- Fernanda Oliveira / TOURADA
                                                                                               (15, 11, 9, 'aprovado'), -- Gabriel Pereira / GORILADA
                                                                                               (16, 4, 3, 'aprovado'),  -- Mariana Ferreira / SOBERANOS
                                                                                               (17, 1, 12, 'pendente'), -- Bruno Rodrigues / JAVALOUCOS (atribuído para consistência)
                                                                                               (18, 2, 7, 'pendente');  -- Larissa Gonçalves / RAPOSADA (atribuído para consistência)

--
-- Inserindo dados na tabela `inscricoes_eventos` (IDs das atléticas atualizados)
--
INSERT INTO `inscricoes_eventos` (`aluno_id`, `evento_id`, `atletica_id`, `status`) VALUES
-- Inscrições em eventos de JULHO
(11, 1, 12, 'aprovado'), (12, 1, 7, 'aprovado'), (13, 1, 2, 'aprovado'), (14, 1, 1, 'aprovado'),
(12, 2, 7, 'aprovado'), (14, 2, 1, 'aprovado'), (16, 2, 3, 'aprovado'), (18, 2, 10, 'aprovado'),
(11, 3, 12, 'aprovado'), (13, 3, 2, 'aprovado'), (17, 3, 2, 'aprovado'), (18, 3, 10, 'aprovado'), (19, 3, 5, 'aprovado'),
(15, 4, 9, 'aprovado'), (18, 4, 10, 'aprovado'),
(11, 5, 12, 'aprovado'), (12, 5, 7, 'aprovado'), (13, 5, 2, 'aprovado'), (14, 5, 1, 'aprovado'), (16, 5, 3, 'aprovado'),
(11, 6, 12, 'aprovado'), (12, 6, 7, 'aprovado'), (13, 6, 2, 'aprovado'), (14, 6, 1, 'aprovado'), (15, 6, 9, 'aprovado'), (16, 6, 3, 'aprovado'), (17, 6, 2, 'aprovado'), (18, 6, 10, 'aprovado'), (19, 6, 5, 'aprovado'),
(13, 7, 2, 'aprovado'), (11, 7, 12, 'aprovado'), (17, 7, 2, 'aprovado'), (19, 7, 5, 'aprovado'),
(11, 8, 12, 'aprovado'), (12, 8, 7, 'aprovado'), (13, 8, 2, 'aprovado'), (14, 8, 1, 'aprovado'), (15, 8, 9, 'aprovado'), (16, 8, 3, 'aprovado'), (17, 8, 2, 'aprovado'), (18, 8, 10, 'aprovado'), (19, 8, 5, 'aprovado'),
(16, 9, 3, 'aprovado'),

-- Inscrições em eventos de AGOSTO
(11, 10, 12, 'aprovado'), (13, 10, 2, 'aprovado'), (17, 10, 2, 'aprovado'),
(12, 11, 7, 'aprovado'), (14, 11, 1, 'aprovado'), (16, 11, 3, 'aprovado'), (18, 11, 10, 'aprovado'),
(11, 12, 12, 'aprovado'), (17, 12, 2, 'aprovado'),
(13, 13, 2, 'aprovado'), (11, 13, 12, 'aprovado'), (17, 13, 2, 'aprovado'),
(16, 14, 3, 'aprovado'), (14, 14, 1, 'aprovado'),
(12, 15, 7, 'aprovado'), (18, 15, 10, 'aprovado'),
(11, 16, 12, 'aprovado'), (13, 16, 2, 'aprovado'),
(17, 17, 2, 'aprovado'), (18, 17, 10, 'aprovado'), (11, 17, 12, 'aprovado'), (12, 17, 7, 'aprovado'), (13, 17, 2, 'aprovado'), (14, 17, 1, 'aprovado'), (15, 17, 9, 'aprovado'), (16, 17, 3, 'aprovado'), (19, 17, 5, 'aprovado'),
(12, 19, 7, 'aprovado'), (14, 19, 1, 'aprovado'),
(14, 20, 1, 'aprovado'),

-- Inscrições em eventos de SETEMBRO
(11, 22, 12, 'aprovado'), (13, 22, 2, 'aprovado'), (17, 22, 2, 'aprovado'),
(13, 23, 2, 'aprovado'), (17, 23, 2, 'aprovado'), (18, 23, 10, 'aprovado'),
(15, 25, 9, 'aprovado'), (18, 25, 10, 'aprovado'),
(12, 26, 7, 'aprovado'), (14, 26, 1, 'aprovado'), (16, 26, 3, 'aprovado'),
(16, 27, 3, 'aprovado'), (14, 27, 1, 'aprovado'), (12, 27, 7, 'aprovado'),
(18, 29, 10, 'aprovado'), (19, 29, 5, 'aprovado'),
(11, 30, 12, 'aprovado'), (13, 30, 2, 'aprovado'),

-- Inscrições em eventos de OUTUBRO
(11, 31, 12, 'aprovado'), (13, 31, 2, 'aprovado'), (17, 31, 2, 'aprovado'),
(12, 32, 7, 'aprovado'), (14, 32, 1, 'aprovado'), (16, 32, 3, 'aprovado'),
(13, 33, 2, 'aprovado'), (11, 33, 12, 'aprovado'), (17, 33, 2, 'aprovado'), (19, 33, 5, 'aprovado'),
(11, 34, 12, 'aprovado'), (12, 34, 7, 'aprovado'), (13, 34, 2, 'aprovado'), (15, 34, 9, 'aprovado'), (17, 34, 2, 'aprovado'), (18, 34, 10, 'aprovado'), (19, 34, 5, 'aprovado'),
(14, 35, 1, 'aprovado'), (11, 35, 12, 'aprovado'), (16, 35, 3, 'aprovado'),
(11, 36, 12, 'aprovado'), (13, 36, 2, 'aprovado'), (17, 36, 2, 'aprovado'), (18, 36, 10, 'aprovado'), (19, 36, 5, 'aprovado'),
(11, 37, 12, 'aprovado'), (13, 37, 2, 'aprovado'), (17, 37, 2, 'aprovado'), (12, 37, 7, 'aprovado'), (14, 37, 1, 'aprovado'),
(15, 38, 9, 'aprovado'), (18, 38, 10, 'aprovado'),
(17, 39, 2, 'recusado'),
(16, 40, 3, 'aprovado'), (14, 40, 1, 'aprovado'),
(13, 42, 2, 'aprovado'), (11, 42, 12, 'aprovado'), (17, 42, 2, 'aprovado'), (19, 42, 5, 'aprovado'),
(12, 43, 7, 'aprovado'), (16, 43, 3, 'aprovado'),
(11, 44, 12, 'aprovado'), (17, 44, 2, 'aprovado'),
(11, 45, 12, 'aprovado'), (13, 45, 2, 'aprovado'), (17, 45, 2, 'aprovado'),
(14, 46, 1, 'aprovado'), (16, 46, 3, 'aprovado'), (11, 46, 12, 'aprovado'), (12, 46, 7, 'aprovado'),
(18, 47, 10, 'aprovado'), (19, 47, 5, 'aprovado'), (15, 47, 9, 'aprovado'),
(16, 48, 3, 'aprovado'), (14, 48, 1, 'aprovado'), (12, 48, 7, 'aprovado'),
(12, 49, 7, 'aprovado'), (18, 49, 10, 'aprovado'),
(13, 50, 2, 'aprovado'), (11, 50, 12, 'aprovado'), (17, 50, 2, 'aprovado'),
(11, 51, 12, 'aprovado'), (13, 51, 2, 'aprovado'),
(11, 52, 12, 'pendente'), (12, 52, 7, 'pendente'), (14, 52, 1, 'pendente'), (16, 52, 3, 'pendente'),
(12, 53, 7, 'pendente'), (14, 53, 1, 'pendente'), (16, 53, 3, 'pendente'), (18, 53, 10, 'pendente'),
(11, 54, 12, 'pendente'), (13, 54, 2, 'pendente'), (17, 54, 2, 'pendente'), (19, 54, 5, 'pendente'),
(15, 55, 9, 'pendente'), (18, 55, 10, 'pendente'), (11, 55, 12, 'pendente'), (12, 55, 7, 'pendente'), (13, 55, 2, 'pendente'),
(14, 56, 1, 'pendente'), (11, 56, 12, 'pendente'), (16, 56, 3, 'pendente'),
(11, 58, 12, 'pendente'), (13, 58, 2, 'pendente'), (17, 58, 2, 'pendente'),
(16, 59, 3, 'pendente'), (14, 59, 1, 'pendente'),
(13, 60, 2, 'pendente'), (11, 60, 12, 'pendente'), (17, 60, 2, 'pendente'), (19, 60, 5, 'pendente'),
(11, 61, 12, 'pendente'), (17, 61, 2, 'pendente'),
(12, 62, 7, 'pendente'), (16, 62, 3, 'pendente'),

-- Inscrições em eventos de NOVEMBRO
(11, 63, 12, 'pendente'), (13, 63, 2, 'pendente'), (17, 63, 2, 'pendente'),
(14, 64, 1, 'pendente'), (11, 64, 12, 'pendente'), (16, 64, 3, 'pendente'),
(12, 65, 7, 'pendente'), (14, 65, 1, 'pendente'), (16, 65, 3, 'pendente'), (18, 65, 10, 'pendente'),
(16, 66, 3, 'pendente'), (14, 66, 1, 'pendente'), (12, 66, 7, 'pendente'),
(13, 67, 2, 'pendente'), (11, 67, 12, 'pendente'), (17, 67, 2, 'pendente'), (19, 67, 5, 'pendente'),
(11, 68, 12, 'pendente'), (13, 68, 2, 'pendente'), (17, 68, 2, 'pendente'),
(17, 69, 2, 'pendente'), (18, 69, 10, 'pendente'), (11, 69, 12, 'pendente'), (12, 69, 7, 'pendente'), (13, 69, 2, 'pendente'), (14, 69, 1, 'pendente'), (15, 69, 9, 'pendente'), (16, 69, 3, 'pendente'), (19, 69, 5, 'pendente'),
(12, 70, 7, 'pendente'), (14, 70, 1, 'pendente'), (16, 70, 3, 'pendente'), (18, 70, 10, 'pendente'),
(11, 71, 12, 'pendente'), (15, 71, 9, 'pendente'), (18, 71, 10, 'pendente'),
(14, 72, 1, 'pendente'), (11, 72, 12, 'pendente'), (16, 72, 3, 'pendente'),
(11, 73, 12, 'pendente'), (13, 73, 2, 'pendente'), (17, 73, 2, 'pendente'),
(16, 74, 3, 'pendente'), (14, 74, 1, 'pendente'), (12, 74, 7, 'pendente'),
(11, 75, 12, 'pendente'), (13, 75, 2, 'pendente'), (17, 75, 2, 'pendente'), (19, 75, 5, 'pendente'),
(12, 76, 7, 'pendente'), (14, 76, 1, 'pendente'), (16, 76, 3, 'pendente'), (18, 76, 10, 'pendente'),
(14, 77, 1, 'pendente'), (11, 77, 12, 'pendente'), (16, 77, 3, 'pendente'),
(11, 78, 12, 'pendente'), (13, 78, 2, 'pendente'), (17, 78, 2, 'pendente'),
(16, 79, 3, 'pendente'), (14, 79, 1, 'pendente'), (12, 79, 7, 'pendente'),
(11, 80, 12, 'pendente'), (12, 80, 7, 'pendente'), (13, 80, 2, 'pendente'), (14, 80, 1, 'pendente'), (15, 80, 9, 'pendente'), (16, 80, 3, 'pendente'), (17, 80, 2, 'pendente'), (18, 80, 10, 'pendente'), (19, 80, 5, 'pendente'),
(15, 81, 9, 'pendente'), (18, 81, 10, 'pendente'),
(12, 82, 7, 'pendente'), (18, 82, 10, 'pendente'),
(18, 83, 10, 'pendente'), (19, 83, 5, 'pendente'), (15, 83, 9, 'pendente'),
(11, 84, 12, 'pendente'), (13, 84, 2, 'pendente'), (17, 84, 2, 'pendente'),
(12, 85, 7, 'pendente'), (14, 85, 1, 'pendente'), (18, 85, 10, 'pendente'),
(11, 86, 12, 'pendente'), (17, 86, 2, 'pendente'),
(13, 87, 2, 'pendente'), (15, 87, 9, 'pendente'),
(11, 88, 12, 'pendente'), (12, 88, 7, 'pendente'), (18, 88, 10, 'pendente');

--
-- Inserindo dados na tabela `presencas` (sem alterações necessárias)
--
INSERT INTO `presencas` (`usuario_id`, `agendamento_id`, `data_presenca`) VALUES
-- Julho 2025 (IDs 1-9 são eventos finalizados)
(11, 1, '2025-07-02 19:30:00'),(12, 1, '2025-07-02 19:30:00'),(13, 1, '2025-07-02 19:30:00'),
(12, 2, '2025-07-05 21:15:00'),(14, 2, '2025-07-05 21:15:00'),(16, 2, '2025-07-05 21:15:00'),
-- Agosto 2025 (IDs 10-20 são eventos finalizados)
(11, 10, '2025-08-01 19:20:00'),(13, 10, '2025-08-01 19:20:00'),(17, 10, '2025-08-01 19:20:00'),
(12, 11, '2025-08-04 21:15:00'),(14, 11, '2025-08-04 21:15:00'),(16, 11, '2025-08-04 21:15:00'),
-- Setembro 2025 (IDs 21-30 são eventos finalizados, exceto o ID 24 que foi cancelado)
(11, 21, '2025-09-02 19:15:00'),(12, 21, '2025-09-02 19:15:00'),
(11, 22, '2025-09-05 21:20:00'),(13, 22, '2025-09-05 21:20:00'),(14, 22, '2025-09-05 21:20:00'),
(12, 26, '2025-09-18 21:15:00'),(14, 26, '2025-09-18 21:15:00'),
(11, 30, '2025-09-29 21:25:00'),(13, 30, '2025-09-29 21:25:00'),
-- Outubro 2025 (apenas eventos finalizados até dia 2)
(11, 31, '2025-10-01 19:20:00'),(13, 31, '2025-10-01 19:20:00'),(17, 31, '2025-10-01 19:20:00'),
(12, 32, '2025-10-02 21:15:00'),(14, 32, '2025-10-02 21:15:00'),(16, 32, '2025-10-02 21:15:00');

--
-- Inserindo dados na tabela `notificacoes` (Nomes das atléticas atualizados)
--
INSERT INTO `notificacoes` (`usuario_id`, `titulo`, `mensagem`, `tipo`, `agendamento_id`, `lida`, `data_criacao`) VALUES
-- Notificações mais antigas (Julho/Agosto)
(11, 'Agendamento Aprovado', 'Seu agendamento "Treino de Férias - Futsal" foi aprovado.', 'agendamento_aprovado', 1, 1, '2025-07-01 10:30:00'),
(12, 'Agendamento Aprovado', 'Seu agendamento "Jogo Amistoso Vôlei" foi aprovado.', 'agendamento_aprovado', 2, 1, '2025-07-04 14:15:00'),
(13, 'Presença Confirmada', 'Sua presença no evento "Treino de Férias - Futsal" foi registrada.', 'presenca_confirmada', 1, 1, '2025-07-02 20:00:00'),

-- Notificações de Setembro
(11, 'Lembrete de Evento', 'Não se esqueça do "Jogo-Treino Futsal vs TOURADA" amanhã às 21:10.', 'lembrete_evento', 22, 1, '2025-09-04 18:00:00'),
(12, 'Agendamento Cancelado', 'O evento "Treino Cancelado (Chuva)" foi cancelado devido a problemas técnicos.', 'agendamento_cancelado', 24, 1, '2025-09-11 15:45:00'),
(14, 'Presença Confirmada', 'Sua presença no evento "Amistoso Vôlei vs OLIMPO" foi registrada.', 'presenca_confirmada', 26, 1, '2025-09-18 22:55:00'),

-- Notificações Recentes (Outubro)
(11, 'Agendamento Aprovado', 'Seu agendamento "Treino Futsal Masculino - JAVALOUCOS" foi aprovado.', 'agendamento_aprovado', 31, 0, '2025-10-01 08:30:00'),
(13, 'Lembrete de Evento', 'Seu evento "Treino League of Legends - ÁGUIAS" começa em 1 hora.', 'lembrete_evento', 33, 0, '2025-10-03 18:15:00'),
(12, 'Presença Confirmada', 'Sua presença no evento "Treino Vôlei Feminino - RAPOSADA" foi registrada.', 'presenca_confirmada', 32, 0, '2025-10-02 22:55:00'),
(17, 'Agendamento Rejeitado', 'Seu agendamento "Uso da quadra para Lazer" foi rejeitado. Motivo: Horário reservado para treinos oficiais.', 'agendamento_rejeitado', 39, 0, '2025-10-01 16:20:00'),
(1, '⚠️ Agendamento Editado', 'O agendamento "Treino Futsal Masculino - JAVALOUCOS" (anteriormente aprovado) foi editado por Admin Atletica Teste e retornou para análise.', 'agendamento_editado', 31, 0, '2025-10-03 14:30:00'),
(1, '⚠️ Agendamento Editado', 'O agendamento "Amistoso Vôlei vs OLIMPO" (anteriormente pendente) foi editado por Professor Teste e retornou para análise.', 'agendamento_editado', 26, 0, '2025-10-02 16:45:00'),

-- Notificações Informativas Gerais
(11, 'Aviso Importante', 'Os treinos de futsal serão intensificados devido à proximidade do Intercursos.', 'aviso', NULL, 0, '2025-10-02 09:00:00'),
(12, 'Informação', 'Nova política de uso da quadra implementada. Confira as atualizações.', 'info', NULL, 0, '2025-10-01 14:30:00'),
(13, 'Aviso de Manutenção', 'A sala de e-sports ficará fechada para manutenção dia 23/10.', 'aviso', NULL, 0, '2025-10-02 11:45:00'),
(14, 'Lembrete de Documentação', 'Não se esqueça de atualizar seu atestado médico para participar dos treinos.', 'info', NULL, 0, '2025-10-01 10:15:00'),
(15, 'Aviso de Evento', 'As inscrições para o Intercursos 2025 serão abertas em breve.', 'info', NULL, 0, '2025-10-03 08:00:00');

-- Comitando as alterações
COMMIT;
SET FOREIGN_KEY_CHECKS=1;