-- ============================================================================
-- SCRIPT DE POPULAÇÃO DE DADOS - SGE UNIFIO
-- Sistema de Gerenciamento de Eventos da Quadra Poliesportiva
-- ============================================================================
--
-- Este script insere dados iniciais (seed data) no banco de dados para
-- desenvolvimento e demonstração do sistema.
--
-- IMPORTANTE: Execute o arquivo 0-schema.sql ANTES deste arquivo!
-- Ordem de execução: 1º schema.sql, 2º db_populate.sql
--
-- Dados incluídos:
-- - 1 Super Administrador (acesso completo ao sistema)
-- - 3 Atléticas (Engenharia, Administração, Direito)
-- - 10 Cursos vinculados às atléticas
-- - 6 Modalidades esportivas (Futsal, Vôlei, Basquete, Handebol, Tênis de Mesa, Futevôlei)
-- - 15 Usuários de teste (alunos, professores, coordenadores, admins)
-- - Agendamentos de exemplo (pendentes, aprovados, finalizados)
-- - Inscrições em modalidades
-- - Notificações de exemplo
--
-- Credenciais de Teste:
-- Super Admin: superadmin@unifio.br / senha123
-- Admin Engenharia: admin@unifio.br / senha123
-- Usuário Comum: joao.silva@unifio.br / senha123
--
-- Nota: Todas as senhas são "senha123" para facilitar testes.
-- EM PRODUÇÃO, use senhas fortes e únicas!
--
-- ============================================================================

USE `application`;

-- Configura o charset para UTF8MB4 na conexão atual
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET CHARACTER SET utf8mb4;
SET character_set_client = utf8mb4;
SET character_set_connection = utf8mb4;
SET character_set_results = utf8mb4;
SET collation_connection = utf8mb4_unicode_ci;

-- Desativa a verificação de chaves estrangeiras para permitir a inserção de dados
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
-- Inserindo dados na tabela `atleticas` (nomes corretos conforme especificação)
--
INSERT INTO `atleticas` (`id`, `nome`) VALUES
(1, 'A.A.A. TOURADA'),           -- Medicina Veterinária
(2, 'A.A.A. ÁGUIAS'),            -- Engenharia de Software
(3, 'A.A.A. SOBERANOS'),         -- Biomedicina
(4, 'A.A.A. DEVORADORES'),       -- Nutrição
(5, 'A.A.A. CASTORES'),          -- Arquitetura
(6, 'A.A.A. SERPENTES'),         -- Enfermagem
(7, 'A.A.A. RAPOSADA'),          -- Direito
(8, 'A.A.A. FORASTEIROS'),       -- Agronomia
(9, 'A.A.A. GORILADA'),          -- Administração
(10, 'A.A.A. RATOLOUCO'),        -- Psicologia
(11, 'A.A.A. OLIMPO'),           -- Fisioterapia
(12, 'A.A.A. JAVALOUCOS'),       -- Engenharia
(13, 'A.A.A. LEÕES'),            -- Contábeis
(14, 'A.A.A. EDUCALOUCOS'),      -- Educação Física
(15, 'A.A.A. ZANGADOS'),         -- Biologia
(16, 'A.A.A. OCTORMENTA');       -- Terapia Ocupacional

--
-- Inserindo dados na tabela `modalidades`
--
INSERT INTO `modalidades` (`id`, `nome`) VALUES
(1, 'Futsal'),(2, 'Voleibol'),(3, 'Basquetebol'),(4, 'Handebol'),(5, 'Natação'),(6, 'Atletismo'),(7, 'Judô'),(8, 'Karatê'),(9, 'Tênis de Mesa'),(10, 'Tênis de Campo'),(11, 'Xadrez'),(12, 'League of Legends'),(13, 'CS:GO'),(14, 'Vôlei de Praia'),(15, 'Queimada');

--
-- Inserindo dados na tabela `cursos` (associações corretas com as atléticas)
--
INSERT INTO `cursos` (`id`, `nome`, `atletica_id`, `coordenador_id`) VALUES
(1, 'Medicina Veterinária', 1, NULL),      -- TOURADA
(2, 'Engenharia de Software', 2, NULL),    -- ÁGUIAS
(3, 'Biomedicina', 3, NULL),               -- SOBERANOS
(4, 'Nutrição', 4, NULL),                  -- DEVORADORES
(5, 'Arquitetura', 5, NULL),               -- CASTORES
(6, 'Enfermagem', 6, NULL),                -- SERPENTES
(7, 'Direito', 7, NULL),                   -- RAPOSADA
(8, 'Agronomia', 8, NULL),                 -- FORASTEIROS
(9, 'Administração', 9, NULL),             -- GORILADA
(10, 'Psicologia', 10, NULL),              -- RATOLOUCO
(11, 'Fisioterapia', 11, NULL),            -- OLIMPO
(12, 'Engenharia Civil', 12, NULL),        -- JAVALOUCOS
(13, 'Ciências Contábeis', 13, NULL),      -- LEÕES
(14, 'Educação Física', 14, NULL),         -- EDUCALOUCOS
(15, 'Biologia', 15, NULL),                -- ZANGADOS
(16, 'Terapia Ocupacional', 16, NULL),     -- OCTORMENTA
-- Cursos sem atlética ainda
(17, 'Farmácia', NULL, NULL),
(18, 'Ciência da Computação', NULL, NULL),
(19, 'Publicidade e Propaganda', NULL, NULL),
(20, 'Gastronomia', NULL, NULL);

--
-- Inserindo dados na tabela `usuarios` (ajustado para cursos e atléticas corretas)
--
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ra`, `data_nascimento`, `telefone`, `curso_id`, `role`, `atletica_id`, `tipo_usuario_detalhado`, `is_coordenador`, `atletica_join_status`) VALUES
(NULL, 'Super Admin', 'sadmin', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, NULL, NULL, NULL, 'superadmin', NULL, NULL, 0, 'none'),
(NULL, 'Aluno Teste', 'aluno@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '123456', '2004-08-15', '(14) 99123-4567', 7, 'usuario', 7, 'Aluno', 0, 'none'),
(NULL, 'Membro Atletica Teste', 'membro@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '789012', '2003-05-20', '(14) 99765-4321', 2, 'usuario', 2, 'Membro das Atleticas', 0, 'aprovado'),
(NULL, 'Admin Atletica Teste', 'admin.atletica@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '345678', '2002-02-10', '(14) 98888-7777', 7, 'admin', 7, 'Membro das Atleticas', 0, 'aprovado'),
(NULL, 'Comunidade Externa Teste', 'comunidade@email.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1990-11-30', '(11) 97777-8888', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none'),
(NULL, 'Admin Esportes', 'admin@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1992-05-10', '11987654322', NULL, 'admin', NULL, NULL, 0, 'none'),
(NULL, 'Prof. Carlos Andrade', 'carlos.andrade@prof.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1975-03-15', '14991234567', 1, 'usuario', NULL, 'Professor', 1, 'none'),
(NULL, 'Profa. Beatriz Lima', 'beatriz.lima@prof.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1980-11-20', '14991234568', 18, 'usuario', NULL, 'Professor', 1, 'none'),
(NULL, 'Prof. Ricardo Souza', 'ricardo.souza@prof.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1968-07-08', '14991234569', 7, 'usuario', NULL, 'Professor', 1, 'none'),
(NULL, 'Profa. Helena Costa', 'helena.costa@prof.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1985-02-25', '14991234570', 3, 'usuario', NULL, 'Professor', 0, 'none'),
(NULL, 'Lucas Mendes', 'lucas.mendes@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '111222', '2004-06-30', '14981112233', 1, 'usuario', 1, 'Membro das Atleticas', 0, 'aprovado'),
(NULL, 'Julia Alves', 'julia.alves@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '222333', '2003-09-12', '14981112234', 7, 'usuario', 7, 'Membro das Atleticas', 0, 'aprovado'),
(NULL, 'Pedro Martins', 'pedro.martins@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '333444', '2002-12-01', '14981112235', 2, 'usuario', 2, 'Membro das Atleticas', 0, 'aprovado'),
(NULL, 'Fernanda Oliveira', 'fernanda.oliveira@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '444555', '2004-04-18', '14981112236', 3, 'usuario', 3, 'Membro das Atleticas', 0, 'aprovado'),
(NULL, 'Gabriel Pereira', 'gabriel.pereira@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '555666', '2003-01-22', '14981112237', 9, 'usuario', 9, 'Membro das Atleticas', 0, 'aprovado'),
(NULL, 'Mariana Ferreira', 'mariana.ferreira@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '666777', '2005-08-05', '14981112238', 6, 'usuario', 6, 'Membro das Atleticas', 0, 'aprovado'),
(NULL, 'Bruno Rodrigues', 'bruno.rodrigues@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '777888', '2004-02-14', '14982223344', 2, 'usuario', NULL, 'Aluno', 0, 'none'),
(NULL, 'Larissa Gonçalves', 'larissa.goncalves@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '888999', '2003-07-29', '14982223345', 10, 'usuario', NULL, 'Aluno', 0, 'none'),
(NULL, 'Rafael Almeida', 'rafael.almeida@aluno.sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '999000', '2002-11-03', '14982223346', 8, 'usuario', NULL, 'Aluno', 0, 'none'),
(NULL, 'Sr. Jorge Santos', 'jorge.santos@email.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1988-10-10', '11976543210', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none'),
(NULL, 'Sra. Ana Paula', 'ana.paula@email.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1995-05-20', '11976543211', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none');

--
-- Atualizando `cursos` com os IDs dos coordenadores
--
UPDATE `cursos` SET `coordenador_id` = 7 WHERE `id` = 1;  -- Prof. Carlos Andrade (Medicina Veterinária)
UPDATE `cursos` SET `coordenador_id` = 8 WHERE `id` = 18; -- Profa. Beatriz Lima (Ciência da Computação)
UPDATE `cursos` SET `coordenador_id` = 9 WHERE `id` = 7;  -- Prof. Ricardo Souza (Direito)


--
-- Inserindo dados na tabela `agendamentos` (IDs baseados na nova ordem de inserção)
--
INSERT INTO `agendamentos` (`usuario_id`, `titulo`, `tipo_agendamento`, `esporte_tipo`, `data_agendamento`, `periodo`, `descricao`, `status`, `responsavel_evento`, `estimativa_participantes`) VALUES
-- JULHO 2025 - Período de Férias
(11, 'Treino de Férias - Futsal', 'esportivo', 'Futsal', '2025-07-02', 'primeiro', 'Treino leve de manutenção durante as férias.', 'finalizado', 'Lucas Mendes', 15),
(12, 'Jogo Amistoso Vôlei', 'esportivo', 'Voleibol', '2025-07-05', 'segundo', 'Amistoso contra time convidado.', 'finalizado', 'Julia Alves', 20),
(8, 'Curso de Extensão: Programação em R', 'nao_esportivo', NULL, '2025-07-08', 'primeiro', 'Curso de férias para a comunidade.', 'finalizado', 'Profa. Beatriz Lima', 40),
(15, 'Planejamento de Eventos MAGNA', 'nao_esportivo', NULL, '2025-07-10', 'primeiro', 'Reunião de diretoria para o próximo semestre.', 'finalizado', 'Gabriel Pereira', 12),
(11, 'Treino Físico Geral', 'esportivo', 'Atletismo', '2025-07-15', 'segundo', 'Preparação física geral para atletas.', 'finalizado', 'Lucas Mendes', 25),
(20, 'Palestra: Saúde Mental no Esporte', 'nao_esportivo', NULL, '2025-07-18', 'primeiro', 'Palestra com psicólogo convidado.', 'finalizado', 'Sr. Jorge Santos', 80),
(13, 'Campeonato Relâmpago de CS:GO', 'esportivo', 'CS:GO', '2025-07-22', 'segundo', 'Torneio de um dia entre os alunos.', 'finalizado', 'Pedro Martins', 16),
(14, 'Ação Social SANGUINÁRIA', 'nao_esportivo', NULL, '2025-07-26', 'primeiro', 'Campanha de doação de sangue.', 'finalizado', 'Fernanda Oliveira', 100),
(16, 'Treino de Handebol Feminino', 'esportivo', 'Handebol', '2025-07-29', 'segundo', 'Treino técnico e tático.', 'finalizado', 'Mariana Ferreira', 16),

-- AGOSTO 2025 - Início do Semestre
(11, 'Volta aos Treinos - Futsal', 'esportivo', 'Futsal', '2025-08-01', 'primeiro', 'Início oficial dos treinos do semestre.', 'finalizado', 'Lucas Mendes', 18),
(12, 'Seletiva Vôlei PREDADORA', 'esportivo', 'Voleibol', '2025-08-04', 'segundo', 'Seleção de novas atletas.', 'finalizado', 'Julia Alves', 30),
(7, 'Aula Magna Engenharia Civil', 'nao_esportivo', NULL, '2025-08-05', 'primeiro', 'Evento de boas-vindas aos calouros.', 'finalizado', 'Prof. Carlos Andrade', 150),
(13, 'Treino Tático Valorant', 'esportivo', NULL, '2025-08-07', 'segundo', 'Análise de mapas e estratégias.', 'finalizado', 'Pedro Martins', 10),
(16, 'Treino Handebol VENENOSA', 'esportivo', 'Handebol', '2025-08-11', 'primeiro', 'Foco em jogadas ensaiadas.', 'finalizado', 'Mariana Ferreira', 14),
(9, 'Simpósio de Direito Penal', 'nao_esportivo', NULL, '2025-08-15', 'primeiro', 'Evento com palestras e debates.', 'finalizado', 'Prof. Ricardo Souza', 120),
(11, 'Treino de Rugby', 'esportivo', NULL, '2025-08-19', 'segundo', 'Treino de contato e táticas de jogo.', 'finalizado', 'Lucas Mendes', 22),
(17, 'Festival de Queimada', 'esportivo', 'Queimada', '2025-08-23', 'primeiro', 'Evento de integração para calouros.', 'finalizado', 'Bruno Rodrigues', 50),
(4, 'Reunião Geral - Admin Atlética', 'nao_esportivo', NULL, '2025-08-26', 'primeiro', 'Alinhamento com a diretoria de esportes.', 'finalizado', 'Admin Atletica Teste', 8),
(12, 'Treino de Polo Aquático', 'esportivo', NULL, '2025-08-28', 'segundo', 'Treino em piscina olímpica.', 'finalizado', 'Julia Alves', 12),
(14, 'Treino Basquete SANGUINÁRIA', 'esportivo', 'Basquetebol', '2025-08-30', 'primeiro', 'Fundamentos e jogadas.', 'finalizado', 'Fernanda Oliveira', 15),

-- SETEMBRO 2025
(7, 'Palestra: Engenharia e Inovação', 'nao_esportivo', NULL, '2025-09-02', 'primeiro', 'Evento do curso de Engenharia de Produção.', 'finalizado', 'Prof. Carlos Andrade', 90),
(11, 'Jogo-Treino Futsal vs SANGUINÁRIA', 'esportivo', 'Futsal', '2025-09-05', 'segundo', 'Jogo preparatório.', 'finalizado', 'Lucas Mendes', 35),
(8, 'Treino de Cobertura de Eventos', 'nao_esportivo', NULL, '2025-09-09', 'primeiro', 'Atividade prática para alunos de Jornalismo.', 'finalizado', 'Profa. Beatriz Lima', 25),
(13, 'Treino Cancelado (Chuva)', 'esportivo', 'League of Legends', '2025-09-11', 'segundo', 'Treino cancelado por problemas na rede elétrica.', 'cancelado', 'Pedro Martins', 8),
(15, 'Semana do Administrador', 'nao_esportivo', NULL, '2025-09-16', 'primeiro', 'Ciclo de palestras e workshops.', 'finalizado', 'Gabriel Pereira', 60),
(12, 'Amistoso Vôlei vs IMPÉRIO', 'esportivo', 'Voleibol', '2025-09-18', 'segundo', 'Jogo amistoso entre atléticas.', 'finalizado', 'Julia Alves', 28),
(16, 'Torneio de Handebol Feminino', 'esportivo', 'Handebol', '2025-09-20', 'primeiro', 'Primeira rodada do torneio interno.', 'finalizado', 'Mariana Ferreira', 32),
(4, 'Manutenção do E-Sports', 'nao_esportivo', NULL, '2025-09-23', 'segundo', 'Atualização dos computadores da sala de e-sports.', 'finalizado', 'Admin Atletica Teste', 5),
(18, 'Cine Debate - Psicologia', 'nao_esportivo', NULL, '2025-09-26', 'primeiro', 'Exibição de filme seguida de debate.', 'finalizado', 'Larissa Gonçalves', 45),
(11, 'Treino Intensivo Futsal', 'esportivo', 'Futsal', '2025-09-29', 'segundo', 'Preparação para o Intercursos.', 'finalizado', 'Lucas Mendes', 20),

-- OUTUBRO 2025 - Mês Atual
(11, 'Treino Futsal Masculino - FURIOSA', 'esportivo', 'Futsal', '2025-10-01', 'primeiro', 'Treino preparatório para o Intercursos.', 'finalizado', 'Lucas Mendes', 20),
(12, 'Treino Vôlei Feminino - PREDADORA', 'esportivo', 'Voleibol', '2025-10-02', 'segundo', 'Treino tático e físico.', 'finalizado', 'Julia Alves', 16),
(13, 'Treino League of Legends - ALFA', 'esportivo', 'League of Legends', '2025-10-03', 'primeiro', 'Treino de estratégias e team play.', 'aprovado', 'Pedro Martins', 10),
(20, 'Palestra sobre Mercado de Trabalho', 'nao_esportivo', NULL, '2025-10-04', 'primeiro', 'Palestra com convidado externo para alunos.', 'aprovado', 'Sr. Jorge Santos', 75),
(14, 'Treino Basquete - SANGUINÁRIA', 'esportivo', 'Basquetebol', '2025-10-04', 'segundo', 'Foco em arremessos e defesa.', 'aprovado', 'Fernanda Oliveira', 12),
(8, 'Workshop de Python para iniciantes', 'nao_esportivo', NULL, '2025-10-05', 'primeiro', 'Organizado pelo curso de Ciência da Computação.', 'aprovado', 'Profa. Beatriz Lima', 30),
(11, 'Amistoso Futsal FURIOSA x ALFA', 'esportivo', 'Futsal', '2025-10-06', 'segundo', 'Jogo amistoso entre as atléticas.', 'aprovado', 'Lucas Mendes', 40),
(15, 'Reunião da Atlética MAGNA', 'nao_esportivo', NULL, '2025-10-07', 'primeiro', 'Planejamento de eventos do semestre.', 'aprovado', 'Gabriel Pereira', 15),
(17, 'Uso da quadra para Lazer', 'esportivo', 'Futsal', '2025-10-08', 'segundo', 'Solicitação de aluno para jogo com amigos.', 'rejeitado', 'Bruno Rodrigues', 8),
(16, 'Treino de Handebol - VENENOSA', 'esportivo', 'Handebol', '2025-10-09', 'primeiro', 'Treino de ataque e contra-ataque.', 'aprovado', 'Mariana Ferreira', 18),
(6, 'Manutenção da Quadra', 'nao_esportivo', NULL, '2025-10-10', 'primeiro', 'Reserva para manutenção e pintura.', 'aprovado', 'Admin Esportes', 3),
(13, 'Campeonato CS:GO - Semifinal', 'esportivo', 'CS:GO', '2025-10-11', 'segundo', 'Semifinal do campeonato interno.', 'aprovado', 'Pedro Martins', 20),
(12, 'Treino Vôlei de Praia', 'esportivo', 'Vôlei de Praia', '2025-10-12', 'primeiro', 'Treino na quadra externa.', 'aprovado', 'Julia Alves', 12),
(7, 'Palestra: Gestão de Projetos', 'nao_esportivo', NULL, '2025-10-13', 'segundo', 'Palestra para alunos de Engenharia.', 'aprovado', 'Prof. Carlos Andrade', 65),
(11, 'Jogo-Treino Futsal vs INSANA', 'esportivo', 'Futsal', '2025-10-14', 'primeiro', 'Preparação tática para o campeonato.', 'aprovado', 'Lucas Mendes', 30),
(14, 'Workshop de Primeiros Socorros', 'nao_esportivo', NULL, '2025-10-15', 'segundo', 'Capacitação para membros das atléticas.', 'aprovado', 'Fernanda Oliveira', 40),
(18, 'Palestra: Ansiedade e Desempenho', 'nao_esportivo', NULL, '2025-10-16', 'primeiro', 'Evento do curso de Psicologia.', 'aprovado', 'Larissa Gonçalves', 55),
(16, 'Torneio de Handebol - Quartas', 'esportivo', 'Handebol', '2025-10-17', 'segundo', 'Quartas de final do torneio.', 'aprovado', 'Mariana Ferreira', 35),
(9, 'Júri Simulado - Direito', 'nao_esportivo', NULL, '2025-10-18', 'primeiro', 'Atividade prática do curso de Direito.', 'aprovado', 'Prof. Ricardo Souza', 80),
(13, 'Treino League of Legends - ALFA', 'esportivo', 'League of Legends', '2025-10-19', 'segundo', 'Análise de partidas anteriores.', 'aprovado', 'Pedro Martins', 10),
(11, 'Treino Futsal - FURIOSA', 'esportivo', 'Futsal', '2025-10-20', 'primeiro', 'Treino de finalizações.', 'aprovado', 'Lucas Mendes', 18),
(21, 'Aula de Yoga para Atletas', 'esportivo', NULL, '2025-10-21', 'segundo', 'Atividade de relaxamento e alongamento.', 'aprovado', 'Sra. Ana Paula', 25),
(12, 'Seletiva Final Vôlei', 'esportivo', 'Voleibol', '2025-10-22', 'primeiro', 'Definição do time principal.', 'aprovado', 'Julia Alves', 24),
(8, 'Hackathon Universitário', 'nao_esportivo', NULL, '2025-10-23', 'segundo', 'Maratona de programação.', 'aprovado', 'Profa. Beatriz Lima', 50),
(15, 'Evento de Networking', 'nao_esportivo', NULL, '2025-10-24', 'primeiro', 'Conexões para futuros profissionais.', 'pendente', 'Gabriel Pereira', 70),
(14, 'Amistoso Basquete vs LETAL', 'esportivo', 'Basquetebol', '2025-10-25', 'segundo', 'Jogo preparatório.', 'pendente', 'Fernanda Oliveira', 22),
(4, 'Reunião de Coordenadores', 'nao_esportivo', NULL, '2025-10-26', 'primeiro', 'Alinhamento mensal.', 'pendente', 'Admin Atletica Teste', 10),
(11, 'Treino Tático Futsal', 'esportivo', 'Futsal', '2025-10-27', 'segundo', 'Estratégias de jogo.', 'pendente', 'Lucas Mendes', 20),
(16, 'Treino Handebol - VENENOSA', 'esportivo', 'Handebol', '2025-10-28', 'primeiro', 'Treino de defesa.', 'pendente', 'Mariana Ferreira', 16),
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
(11, 'Confraternização FURIOSA', 'nao_esportivo', NULL, '2025-11-24', 'primeiro', 'Evento de confraternização da atlética.', 'pendente', 'Lucas Mendes', 35),
(12, 'Confraternização PREDADORA', 'nao_esportivo', NULL, '2025-11-25', 'segundo', 'Festa de encerramento do semestre.', 'pendente', 'Julia Alves', 40),
(7, 'Última Aula - Formandos Engenharia', 'nao_esportivo', NULL, '2025-11-27', 'primeiro', 'Despedida dos formandos.', 'pendente', 'Prof. Carlos Andrade', 80),
(13, 'Torneio de Xadrez', 'esportivo', 'Xadrez', '2025-11-28', 'segundo', 'Campeonato interno de xadrez.', 'pendente', 'Pedro Martins', 20),
(21, 'Aula Aberta de Meditação', 'nao_esportivo', NULL, '2025-11-29', 'primeiro', 'Técnicas de mindfulness para estudantes.', 'pendente', 'Sra. Ana Paula', 30),

-- EVENTOS REJEITADOS - Para popular a aba de eventos rejeitados
(17, 'Festa de Aniversário na Quadra', 'nao_esportivo', NULL, '2025-09-14', 'segundo', 'Comemoração de aniversário.', 'rejeitado', 'Bruno Rodrigues', 25),
(18, 'Treino Livre de Skate', 'esportivo', NULL, '2025-09-17', 'primeiro', 'Treino de manobras de skate.', 'rejeitado', 'Larissa Gonçalves', 12),
(19, 'Gravação de TikTok', 'nao_esportivo', NULL, '2025-09-21', 'segundo', 'Gravação de vídeos para redes sociais.', 'rejeitado', 'Rafael Almeida', 8),
(20, 'Churrasco da Turma', 'nao_esportivo', NULL, '2025-10-05', 'segundo', 'Confraternização com churrasco.', 'rejeitado', 'Sr. Jorge Santos', 30),
(21, 'Aula Particular de Tênis', 'esportivo', 'Tênis de Campo', '2025-10-06', 'primeiro', 'Professor particular.', 'rejeitado', 'Sra. Ana Paula', 2),
(17, 'Treino de Parkour', 'esportivo', NULL, '2025-10-10', 'segundo', 'Treino de saltos e acrobacias.', 'rejeitado', 'Bruno Rodrigues', 10),
(18, 'Show Acústico', 'nao_esportivo', NULL, '2025-10-11', 'primeiro', 'Apresentação musical.', 'rejeitado', 'Larissa Gonçalves', 40),
(19, 'Treino de Crossfit Externo', 'esportivo', NULL, '2025-10-13', 'primeiro', 'Box de crossfit externo.', 'rejeitado', 'Rafael Almeida', 20),
(11, 'Treino Extra Futsal', 'esportivo', 'Futsal', '2025-10-04', 'primeiro', 'Treino adicional solicitado.', 'rejeitado', 'Lucas Mendes', 18),
(12, 'Amistoso Vôlei Extra', 'esportivo', 'Voleibol', '2025-10-04', 'segundo', 'Jogo amistoso.', 'rejeitado', 'Julia Alves', 24),
(14, 'Campeonato Basquete 3x3', 'esportivo', 'Basquetebol', '2025-10-19', 'primeiro', 'Torneio aberto.', 'rejeitado', 'Fernanda Oliveira', 60),
(15, 'Palestra com Influencer', 'nao_esportivo', NULL, '2025-10-21', 'primeiro', 'Palestra motivacional.', 'rejeitado', 'Gabriel Pereira', 150);

-- Atualizar motivos de rejeição
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Horário reservado para treinos oficiais das atléticas.' WHERE `titulo` = 'Uso da quadra para Lazer';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Quadra destinada exclusivamente para atividades esportivas e acadêmicas.' WHERE `titulo` = 'Festa de Aniversário na Quadra';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Skate não permitido devido ao risco de danos ao piso.' WHERE `titulo` = 'Treino Livre de Skate';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Quadra não pode ser reservada para produção de conteúdo de redes sociais.' WHERE `titulo` = 'Gravação de TikTok';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Quadra não possui infraestrutura para eventos gastronômicos.' WHERE `titulo` = 'Churrasco da Turma';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Aulas particulares comerciais não são permitidas nas instalações.' WHERE `titulo` = 'Aula Particular de Tênis';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Parkour oferece riscos às instalações e não é modalidade reconhecida.' WHERE `titulo` = 'Treino de Parkour';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Eventos musicais devem ser no auditório. Quadra possui acústica inadequada.' WHERE `titulo` = 'Show Acústico';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Grupos externos comerciais não têm permissão para usar as instalações.' WHERE `titulo` = 'Treino de Crossfit Externo';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Horário já ocupado. Verifique disponibilidade em outros períodos.' WHERE `titulo` = 'Treino Extra Futsal';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Horário conflitante com evento já aprovado.' WHERE `titulo` = 'Amistoso Vôlei Extra';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Eventos abertos ao público externo requerem autorização prévia e seguro. Documentação incompleta.' WHERE `titulo` = 'Campeonato Basquete 3x3';
UPDATE `agendamentos` SET `motivo_rejeicao` = 'Palestrantes externos devem ser aprovados pela coordenação acadêmica.' WHERE `titulo` = 'Palestra com Influencer';

--
-- Configurando campo possui_materiais e materiais_necessarios para eventos esportivos
--
-- Eventos que POSSUEM materiais próprios (possui_materiais = 1)
UPDATE `agendamentos` SET `possui_materiais` = 1 WHERE `tipo_agendamento` = 'esportivo' AND `possui_materiais` IS NULL;

-- Eventos específicos que NÃO possuem materiais (possui_materiais = 0) com lista de materiais necessários
UPDATE `agendamentos` SET `possui_materiais` = 0, `materiais_necessarios` = '3 bolas de basquete\n2 conjuntos de coletes (cores diferentes)\n1 bomba de ar\nCronômetro' 
WHERE `titulo` IN ('Treino Basquete - SANGUINÁRIA', 'Treino Basquete SANGUINÁRIA', 'Amistoso Basquete vs LETAL');

UPDATE `agendamentos` SET `possui_materiais` = 0, `materiais_necessarios` = '2 bolas de handebol\n2 conjuntos de coletes\n1 apito\nCones para treino' 
WHERE `titulo` LIKE '%Treino Handebol%' AND `data_agendamento` >= '2025-10-14';

UPDATE `agendamentos` SET `possui_materiais` = 0, `materiais_necessarios` = '1 bola de vôlei\nRede oficial\nAntenas\nFita para marcação de quadra' 
WHERE `titulo` IN ('Treino Vôlei Feminino', 'Seletiva Final Vôlei', 'Amistoso Vôlei vs IMPÉRIO');

UPDATE `agendamentos` SET `possui_materiais` = 0, `materiais_necessarios` = '2 bolas de futsal\n4 conjuntos de coletes (2 cores)\nCones de marcação\n1 apito' 
WHERE `titulo` IN ('Jogo-Treino Futsal vs INSANA', 'Treino Tático Futsal', 'Amistoso Futsal FURIOSA x ALFA') AND `data_agendamento` >= '2025-10-14';

UPDATE `agendamentos` SET `possui_materiais` = 0, `materiais_necessarios` = 'Equipamentos de ginástica\nColchonetes\nBloco de yoga (10 unidades)' 
WHERE `titulo` = 'Aula de Yoga para Atletas';

UPDATE `agendamentos` SET `possui_materiais` = 0, `materiais_necessarios` = 'Mesa oficial de tênis de mesa\nRede e suportes\n6 raquetes\n12 bolinhas' 
WHERE `titulo` LIKE '%Tênis de Mesa%' OR `esporte_tipo` = 'Tênis de Mesa';

UPDATE `agendamentos` SET `possui_materiais` = 0, `materiais_necessarios` = 'Bolas de queimada (mínimo 3)\nColetes para identificação de times\nCronômetro\nApito' 
WHERE `esporte_tipo` = 'Queimada';

UPDATE `agendamentos` SET `possui_materiais` = 0, `materiais_necessarios` = '1 bola oficial de vôlei de praia\nRede para vôlei de praia\nMarcadores de quadra' 
WHERE `esporte_tipo` = 'Vôlei de Praia';

--
-- Inserindo dados na tabela `inscricoes_modalidade` (IDs baseados na nova ordem de inserção)
--
INSERT INTO `inscricoes_modalidade` (`aluno_id`, `modalidade_id`, `atletica_id`, `status`) VALUES
(11, 1, 1, 'aprovado'),(12, 2, 2, 'aprovado'),(13, 12, 6, 'aprovado'),(14, 3, 3, 'aprovado'),(15, 11, 5, 'aprovado'),(16, 4, 8, 'aprovado'),(17, 1, 1, 'pendente'),(18, 2, 2, 'pendente');

--
-- Inserindo dados na tabela `inscricoes_eventos` (FK aponta para `agendamentos`)
-- Expandido com muitas inscrições em diversos eventos
--
INSERT INTO `inscricoes_eventos` (`aluno_id`, `evento_id`, `atletica_id`, `status`) VALUES
-- Inscrições em eventos de JULHO
(11, 1, 1, 'aprovado'), -- Lucas no Treino de Férias Futsal
(12, 1, 2, 'aprovado'), -- Julia no Treino de Férias Futsal
(13, 1, 6, 'aprovado'), -- Pedro no Treino de Férias Futsal
(14, 1, 3, 'aprovado'), -- Fernanda no Treino de Férias Futsal
(12, 2, 2, 'aprovado'), -- Julia no Jogo Amistoso Vôlei
(14, 2, 3, 'aprovado'), -- Fernanda no Jogo Amistoso Vôlei
(16, 2, 8, 'aprovado'), -- Mariana no Jogo Amistoso Vôlei
(18, 2, 2, 'aprovado'), -- Larissa no Jogo Amistoso Vôlei
(11, 3, 1, 'aprovado'), -- Curso de Programação em R
(13, 3, 6, 'aprovado'),
(17, 3, 1, 'aprovado'),
(18, 3, 5, 'aprovado'),
(19, 3, 8, 'aprovado'),
(15, 4, 5, 'aprovado'), -- Planejamento MAGNA
(18, 4, 5, 'aprovado'),
(11, 5, 1, 'aprovado'), -- Treino Físico Geral
(12, 5, 2, 'aprovado'),
(13, 5, 6, 'aprovado'),
(14, 5, 3, 'aprovado'),
(16, 5, 8, 'aprovado'),
(11, 6, 1, 'aprovado'), -- Palestra Saúde Mental (evento grande)
(12, 6, 2, 'aprovado'),
(13, 6, 6, 'aprovado'),
(14, 6, 3, 'aprovado'),
(15, 6, 5, 'aprovado'),
(16, 6, 8, 'aprovado'),
(17, 6, 1, 'aprovado'),
(18, 6, 4, 'aprovado'),
(19, 6, 8, 'aprovado'),
(13, 7, 6, 'aprovado'), -- Campeonato CS:GO
(11, 7, 1, 'aprovado'),
(17, 7, 1, 'aprovado'),
(18, 7, 7, 'aprovado'),
(19, 7, 8, 'aprovado'),
(11, 8, 1, 'aprovado'), -- Ação Social SANGUINÁRIA (evento massivo)
(12, 8, 2, 'aprovado'),
(13, 8, 6, 'aprovado'),
(14, 8, 3, 'aprovado'),
(15, 8, 5, 'aprovado'),
(16, 8, 8, 'aprovado'),
(17, 8, 1, 'aprovado'),
(18, 8, 4, 'aprovado'),
(19, 8, 8, 'aprovado'),
(16, 9, 8, 'aprovado'), -- Treino Handebol Feminino

-- Inscrições em eventos de AGOSTO
(11, 10, 1, 'aprovado'), -- Volta aos Treinos Futsal
(13, 10, 6, 'aprovado'),
(17, 10, 1, 'aprovado'),
(12, 11, 2, 'aprovado'), -- Seletiva Vôlei
(14, 11, 3, 'aprovado'),
(16, 11, 8, 'aprovado'),
(18, 11, 2, 'aprovado'),
(11, 12, 1, 'aprovado'), -- Aula Magna Engenharia
(17, 12, 1, 'aprovado'),
(13, 13, 6, 'aprovado'), -- Treino Tático Valorant
(11, 13, 1, 'aprovado'),
(17, 13, 1, 'aprovado'),
(16, 14, 8, 'aprovado'), -- Treino Handebol VENENOSA
(14, 14, 3, 'aprovado'),
(12, 15, 2, 'aprovado'), -- Simpósio Direito Penal
(18, 15, 4, 'aprovado'),
(11, 16, 1, 'aprovado'), -- Treino Rugby
(13, 16, 6, 'aprovado'),
(17, 17, 1, 'aprovado'), -- Festival de Queimada
(18, 17, 4, 'aprovado'),
(11, 17, 1, 'aprovado'),
(12, 17, 2, 'aprovado'),
(13, 17, 6, 'aprovado'),
(14, 17, 3, 'aprovado'),
(15, 17, 5, 'aprovado'),
(16, 17, 8, 'aprovado'),
(19, 17, 8, 'aprovado'),
(12, 19, 2, 'aprovado'), -- Treino Polo Aquático
(14, 19, 3, 'aprovado'),
(14, 20, 3, 'aprovado'), -- Treino Basquete

-- Inscrições em eventos de SETEMBRO
(11, 22, 1, 'aprovado'), -- Jogo-Treino Futsal
(13, 22, 6, 'aprovado'),
(17, 22, 1, 'aprovado'),
(13, 23, 6, 'aprovado'), -- Cobertura de Eventos
(17, 23, 1, 'aprovado'),
(18, 23, 4, 'aprovado'),
(15, 25, 5, 'aprovado'), -- Semana do Administrador
(18, 25, 5, 'aprovado'),
(12, 26, 2, 'aprovado'), -- Amistoso Vôlei vs IMPÉRIO
(14, 26, 3, 'aprovado'),
(16, 26, 8, 'aprovado'),
(16, 27, 8, 'aprovado'), -- Torneio Handebol Feminino
(14, 27, 3, 'aprovado'),
(12, 27, 2, 'aprovado'),
(18, 29, 4, 'aprovado'), -- Cine Debate Psicologia
(19, 29, 8, 'aprovado'),
(11, 30, 1, 'aprovado'), -- Treino Intensivo Futsal
(13, 30, 6, 'aprovado'),

-- Inscrições em eventos de OUTUBRO (eventos atuais e futuros)
(11, 31, 1, 'aprovado'), -- Treino Futsal Masculino
(13, 31, 6, 'aprovado'),
(17, 31, 1, 'aprovado'),
(12, 32, 2, 'aprovado'), -- Treino Vôlei Feminino
(14, 32, 3, 'aprovado'),
(16, 32, 8, 'aprovado'),
(13, 33, 6, 'aprovado'), -- Treino LoL ALFA
(11, 33, 1, 'aprovado'),
(17, 33, 1, 'aprovado'),
(19, 33, 8, 'aprovado'),
(11, 34, 1, 'aprovado'), -- Palestra Mercado de Trabalho
(12, 34, 2, 'aprovado'),
(13, 34, 6, 'aprovado'),
(15, 34, 5, 'aprovado'),
(17, 34, 1, 'aprovado'),
(18, 34, 4, 'aprovado'),
(19, 34, 8, 'aprovado'),
(14, 35, 3, 'aprovado'), -- Treino Basquete
(11, 35, 1, 'aprovado'),
(16, 35, 8, 'aprovado'),
(11, 36, 1, 'aprovado'), -- Workshop Python
(13, 36, 6, 'aprovado'),
(17, 36, 1, 'aprovado'),
(18, 36, 4, 'aprovado'),
(19, 36, 8, 'aprovado'),
(11, 37, 1, 'aprovado'), -- Amistoso Futsal FURIOSA x ALFA
(13, 37, 6, 'aprovado'),
(17, 37, 1, 'aprovado'),
(12, 37, 2, 'aprovado'),
(14, 37, 3, 'aprovado'),
(15, 38, 5, 'aprovado'), -- Reunião MAGNA
(18, 38, 5, 'aprovado'),
(17, 39, 1, 'recusado'), -- Uso da quadra para Lazer (rejeitado)
(16, 40, 8, 'aprovado'), -- Treino Handebol
(14, 40, 3, 'aprovado'),
(13, 42, 6, 'aprovado'), -- Campeonato CS:GO Semifinal
(11, 42, 1, 'aprovado'),
(17, 42, 1, 'aprovado'),
(19, 42, 8, 'aprovado'),
(12, 43, 2, 'aprovado'), -- Treino Vôlei de Praia
(16, 43, 8, 'aprovado'),
(11, 44, 1, 'aprovado'), -- Palestra Gestão de Projetos
(17, 44, 1, 'aprovado'),
(11, 45, 1, 'aprovado'), -- Jogo-Treino vs INSANA
(13, 45, 6, 'aprovado'),
(17, 45, 1, 'aprovado'),
(14, 46, 3, 'aprovado'), -- Workshop Primeiros Socorros
(16, 46, 8, 'aprovado'),
(11, 46, 1, 'aprovado'),
(12, 46, 2, 'aprovado'),
(18, 47, 4, 'aprovado'), -- Palestra Ansiedade
(19, 47, 8, 'aprovado'),
(15, 47, 5, 'aprovado'),
(16, 48, 8, 'aprovado'), -- Torneio Handebol Quartas
(14, 48, 3, 'aprovado'),
(12, 48, 2, 'aprovado'),
(12, 49, 2, 'aprovado'), -- Júri Simulado Direito
(18, 49, 4, 'aprovado'),
(13, 50, 6, 'aprovado'), -- Treino LoL
(11, 50, 1, 'aprovado'),
(17, 50, 1, 'aprovado'),
(11, 51, 1, 'aprovado'), -- Treino Futsal
(13, 51, 6, 'aprovado'),
(11, 52, 1, 'pendente'), -- Yoga para Atletas
(12, 52, 2, 'pendente'),
(14, 52, 3, 'pendente'),
(16, 52, 8, 'pendente'),
(12, 53, 2, 'pendente'), -- Seletiva Final Vôlei
(14, 53, 3, 'pendente'),
(16, 53, 8, 'pendente'),
(18, 53, 2, 'pendente'),
(11, 54, 1, 'pendente'), -- Hackathon
(13, 54, 6, 'pendente'),
(17, 54, 1, 'pendente'),
(19, 54, 8, 'pendente'),
(15, 55, 5, 'pendente'), -- Evento Networking
(18, 55, 5, 'pendente'),
(11, 55, 1, 'pendente'),
(12, 55, 2, 'pendente'),
(13, 55, 6, 'pendente'),
(14, 56, 3, 'pendente'), -- Amistoso Basquete vs LETAL
(11, 56, 1, 'pendente'),
(16, 56, 8, 'pendente'),
(11, 58, 1, 'pendente'), -- Treino Tático Futsal
(13, 58, 6, 'pendente'),
(17, 58, 1, 'pendente'),
(16, 59, 8, 'pendente'), -- Treino Handebol
(14, 59, 3, 'pendente'),
(13, 60, 6, 'pendente'), -- Final CS:GO
(11, 60, 1, 'pendente'),
(17, 60, 1, 'pendente'),
(19, 60, 8, 'pendente'),
(11, 61, 1, 'pendente'), -- Semana da Engenharia
(17, 61, 1, 'pendente'),
(12, 62, 2, 'pendente'), -- Treino Vôlei
(16, 62, 8, 'pendente'),

-- Inscrições em eventos de NOVEMBRO (Intercursos e eventos futuros)
(11, 63, 1, 'pendente'), -- Intercursos Abertura Futsal
(13, 63, 6, 'pendente'),
(17, 63, 1, 'pendente'),
(14, 64, 3, 'pendente'), -- Intercursos Basquete Fase 1
(11, 64, 1, 'pendente'),
(16, 64, 8, 'pendente'),
(12, 65, 2, 'pendente'), -- Intercursos Vôlei Fase 1
(14, 65, 3, 'pendente'),
(16, 65, 8, 'pendente'),
(18, 65, 2, 'pendente'),
(16, 66, 8, 'pendente'), -- Intercursos Handebol Fase 1
(14, 66, 3, 'pendente'),
(12, 66, 2, 'pendente'),
(13, 67, 6, 'pendente'), -- Intercursos E-sports LoL
(11, 67, 1, 'pendente'),
(17, 67, 1, 'pendente'),
(19, 67, 8, 'pendente'),
(11, 68, 1, 'pendente'), -- Intercursos Futsal Quartas
(13, 68, 6, 'pendente'),
(17, 68, 1, 'pendente'),
(17, 69, 1, 'pendente'), -- Intercursos Queimada
(18, 69, 4, 'pendente'),
(11, 69, 1, 'pendente'),
(12, 69, 2, 'pendente'),
(13, 69, 6, 'pendente'),
(14, 69, 3, 'pendente'),
(15, 69, 5, 'pendente'),
(16, 69, 8, 'pendente'),
(19, 69, 8, 'pendente'),
(12, 70, 2, 'pendente'), -- Intercursos Vôlei Semifinal
(14, 70, 3, 'pendente'),
(16, 70, 8, 'pendente'),
(18, 70, 2, 'pendente'),
(11, 71, 1, 'pendente'), -- Palestra Empreendedorismo
(15, 71, 5, 'pendente'),
(18, 71, 5, 'pendente'),
(14, 72, 3, 'pendente'), -- Intercursos Basquete Semifinal
(11, 72, 1, 'pendente'),
(16, 72, 8, 'pendente'),
(11, 73, 1, 'pendente'), -- Intercursos Futsal Semifinal
(13, 73, 6, 'pendente'),
(17, 73, 1, 'pendente'),
(16, 74, 8, 'pendente'), -- Intercursos Handebol Semifinal
(14, 74, 3, 'pendente'),
(12, 74, 2, 'pendente'),
(11, 75, 1, 'pendente'), -- Workshop IA
(13, 75, 6, 'pendente'),
(17, 75, 1, 'pendente'),
(19, 75, 8, 'pendente'),
(12, 76, 2, 'pendente'), -- Intercursos Final Vôlei
(14, 76, 3, 'pendente'),
(16, 76, 8, 'pendente'),
(18, 76, 2, 'pendente'),
(14, 77, 3, 'pendente'), -- Intercursos Final Basquete
(11, 77, 1, 'pendente'),
(16, 77, 8, 'pendente'),
(11, 78, 1, 'pendente'), -- Intercursos Final Futsal
(13, 78, 6, 'pendente'),
(17, 78, 1, 'pendente'),
(16, 79, 8, 'pendente'), -- Intercursos Final Handebol
(14, 79, 3, 'pendente'),
(12, 79, 2, 'pendente'),
(11, 80, 1, 'pendente'), -- Cerimônia Encerramento Intercursos
(12, 80, 2, 'pendente'),
(13, 80, 6, 'pendente'),
(14, 80, 3, 'pendente'),
(15, 80, 5, 'pendente'),
(16, 80, 8, 'pendente'),
(17, 80, 1, 'pendente'),
(18, 80, 4, 'pendente'),
(19, 80, 8, 'pendente'),
(15, 81, 5, 'pendente'), -- Feira de Profissões
(18, 81, 5, 'pendente'),
(12, 82, 2, 'pendente'), -- Seminário Direito Constitucional
(18, 82, 4, 'pendente'),
(18, 83, 4, 'pendente'), -- Workshop Saúde Mental
(19, 83, 8, 'pendente'),
(15, 83, 5, 'pendente'),
(11, 84, 1, 'pendente'), -- Confraternização FURIOSA
(13, 84, 1, 'pendente'),
(17, 84, 1, 'pendente'),
(12, 85, 2, 'pendente'), -- Confraternização PREDADORA
(14, 85, 2, 'pendente'),
(18, 85, 2, 'pendente'),
(11, 86, 1, 'pendente'), -- Última Aula Formandos
(17, 86, 1, 'pendente'),
(13, 87, 6, 'pendente'), -- Torneio Xadrez
(15, 87, 5, 'pendente'),
(11, 88, 1, 'pendente'), -- Aula Meditação
(12, 88, 2, 'pendente'),
(18, 88, 4, 'pendente');

--
-- Inserindo dados na tabela `presencas`
--
INSERT INTO `presencas` (`usuario_id`, `agendamento_id`, `data_presenca`) VALUES
-- Julho 2025 (IDs 1-9 são eventos finalizados)
(11, 1, '2025-07-02 19:30:00'),
(12, 1, '2025-07-02 19:30:00'),
(13, 1, '2025-07-02 19:30:00'),
(12, 2, '2025-07-05 21:15:00'),
(14, 2, '2025-07-05 21:15:00'),
(16, 2, '2025-07-05 21:15:00'),

-- Agosto 2025 (IDs 10-20 são eventos finalizados)
(11, 10, '2025-08-01 19:20:00'),
(13, 10, '2025-08-01 19:20:00'),
(17, 10, '2025-08-01 19:20:00'),
(12, 11, '2025-08-04 21:15:00'),
(14, 11, '2025-08-04 21:15:00'),
(16, 11, '2025-08-04 21:15:00'),

-- Setembro 2025 (IDs 31-40 são eventos finalizados, exceto o ID 34 que foi cancelado)
(11, 31, '2025-09-02 19:15:00'),
(12, 31, '2025-09-02 19:15:00'),
(11, 32, '2025-09-05 21:20:00'),
(13, 32, '2025-09-05 21:20:00'),
(14, 32, '2025-09-05 21:20:00'),
(12, 36, '2025-09-18 21:15:00'),
(13, 36, '2025-09-18 21:15:00'),
(11, 40, '2025-09-29 21:25:00'),
(13, 40, '2025-09-29 21:25:00'),

-- Outubro 2025 (apenas eventos finalizados até dia 3)
(11, 41, '2025-10-01 19:20:00'),
(13, 41, '2025-10-01 19:20:00'),
(17, 41, '2025-10-01 19:20:00'),
(12, 42, '2025-10-02 21:15:00'),
(14, 42, '2025-10-02 21:15:00'),
(16, 42, '2025-10-02 21:15:00'),

-- Outubro 2025 - Eventos aprovados a partir de 14/10 (com presenças)
-- ID 45: Jogo-Treino Futsal vs INSANA (14/10)
(11, 45, '2025-10-14 19:20:00'),
(13, 45, '2025-10-14 19:25:00'),
(17, 45, '2025-10-14 19:30:00'),
(12, 45, '2025-10-14 19:35:00'),

-- ID 46: Workshop de Primeiros Socorros (15/10)
(14, 46, '2025-10-15 21:15:00'),
(16, 46, '2025-10-15 21:20:00'),
(11, 46, '2025-10-15 21:25:00'),
(12, 46, '2025-10-15 21:30:00'),
(15, 46, '2025-10-15 21:35:00'),

-- ID 47: Palestra Ansiedade e Desempenho (16/10)
(18, 47, '2025-10-16 19:15:00'),
(19, 47, '2025-10-16 19:20:00'),
(15, 47, '2025-10-16 19:25:00'),
(11, 47, '2025-10-16 19:30:00'),

-- ID 48: Torneio de Handebol - Quartas (17/10)
(16, 48, '2025-10-17 21:15:00'),
(14, 48, '2025-10-17 21:20:00'),
(12, 48, '2025-10-17 21:25:00'),
(11, 48, '2025-10-17 21:30:00'),

-- ID 49: Júri Simulado - Direito (18/10)
(12, 49, '2025-10-18 19:15:00'),
(18, 49, '2025-10-18 19:20:00'),
(9, 49, '2025-10-18 19:25:00'),
(15, 49, '2025-10-18 19:30:00'),

-- ID 50: Treino League of Legends - ALFA (19/10)
(13, 50, '2025-10-19 21:15:00'),
(11, 50, '2025-10-19 21:20:00'),
(17, 50, '2025-10-19 21:25:00'),
(19, 50, '2025-10-19 21:30:00'),

-- ID 51: Treino Futsal - FURIOSA (20/10)
(11, 51, '2025-10-20 19:15:00'),
(13, 51, '2025-10-20 19:20:00'),
(17, 51, '2025-10-20 19:25:00'),
(12, 51, '2025-10-20 19:30:00'),

-- ID 52: Aula de Yoga para Atletas (21/10)
(11, 52, '2025-10-21 21:15:00'),
(12, 52, '2025-10-21 21:20:00'),
(14, 52, '2025-10-21 21:25:00'),
(16, 52, '2025-10-21 21:30:00'),
(18, 52, '2025-10-21 21:35:00'),

-- ID 53: Seletiva Final Vôlei (22/10)
(12, 53, '2025-10-22 19:15:00'),
(14, 53, '2025-10-22 19:20:00'),
(16, 53, '2025-10-22 19:25:00'),
(18, 53, '2025-10-22 19:30:00'),

-- ID 54: Hackathon Universitário (23/10)
(11, 54, '2025-10-23 21:15:00'),
(13, 54, '2025-10-23 21:20:00'),
(17, 54, '2025-10-23 21:25:00'),
(19, 54, '2025-10-23 21:30:00'),
(8, 54, '2025-10-23 21:35:00');

--
-- Inserindo dados na tabela `notificacoes`
--
INSERT INTO `notificacoes` (`usuario_id`, `titulo`, `mensagem`, `tipo`, `agendamento_id`, `lida`, `data_criacao`) VALUES
-- Notificações mais antigas (Julho/Agosto)
(11, 'Agendamento Aprovado', 'Seu agendamento "Treino de Férias - Futsal" foi aprovado.', 'agendamento_aprovado', 1, 1, '2025-07-01 10:30:00'),
(12, 'Agendamento Aprovado', 'Seu agendamento "Jogo Amistoso Vôlei" foi aprovado.', 'agendamento_aprovado', 2, 1, '2025-07-04 14:15:00'),
(13, 'Presença Confirmada', 'Sua presença no evento "Treino de Férias - Futsal" foi registrada.', 'presenca_confirmada', 1, 1, '2025-07-02 20:00:00'),

-- Notificações de Setembro
(11, 'Lembrete de Evento', 'Não se esqueça do "Jogo-Treino Futsal vs SANGUINÁRIA" amanhã às 21:10.', 'lembrete_evento', 32, 1, '2025-09-04 18:00:00'),
(12, 'Agendamento Cancelado', 'O evento "Treino Cancelado (Chuva)" foi cancelado devido a problemas técnicos.', 'agendamento_cancelado', 34, 1, '2025-09-11 15:45:00'),
(14, 'Presença Confirmada', 'Sua presença no evento "Amistoso Vôlei vs IMPÉRIO" foi registrada.', 'presenca_confirmada', 36, 1, '2025-09-18 22:55:00'),

-- Notificações Recentes (Outubro)
(11, 'Agendamento Aprovado', 'Seu agendamento "Treino Futsal Masculino - FURIOSA" foi aprovado.', 'agendamento_aprovado', 41, 0, '2025-10-01 08:30:00'),
(13, 'Lembrete de Evento', 'Seu evento "Treino League of Legends - ALFA" começa em 1 hora.', 'lembrete_evento', 33, 0, '2025-10-03 18:15:00'),
(12, 'Presença Confirmada', 'Sua presença no evento "Treino Vôlei Feminino - PREDADORA" foi registrada.', 'presenca_confirmada', 42, 0, '2025-10-02 22:55:00'),
(17, 'Agendamento Rejeitado', 'Seu agendamento "Uso da quadra para Lazer" foi rejeitado. Motivo: Horário reservado para treinos oficiais.', 'agendamento_rejeitado', 39, 0, '2025-10-01 16:20:00'),
(1, '⚠️ Agendamento Editado', 'O agendamento "Treino Futsal Masculino - FURIOSA" (anteriormente aprovado) foi editado por Admin Atletica Teste e retornou para análise.', 'agendamento_editado', 41, 0, '2025-10-03 14:30:00'),
(1, '⚠️ Agendamento Editado', 'O agendamento "Amistoso Vôlei vs IMPÉRIO" (anteriormente pendente) foi editado por Professor Teste e retornou para análise.', 'agendamento_editado', 36, 0, '2025-10-02 16:45:00'),

-- Notificações Informativas Gerais
(11, 'Aviso Importante', 'Os treinos de futsal serão intensificados devido à proximidade do Intercursos.', 'aviso', NULL, 0, '2025-10-02 09:00:00'),
(12, 'Informação', 'Nova política de uso da quadra implementada. Confira as atualizações.', 'info', NULL, 0, '2025-10-01 14:30:00'),
(13, 'Aviso de Manutenção', 'A sala de e-sports ficará fechada para manutenção dia 23/10.', 'aviso', NULL, 0, '2025-10-02 11:45:00'),
(14, 'Lembrete de Documentação', 'Não se esqueça de atualizar seu atestado médico para participar dos treinos.', 'info', NULL, 0, '2025-10-01 10:15:00'),
(15, 'Aviso de Evento', 'As inscrições para o Intercursos 2025 serão abertas em breve.', 'info', NULL, 0, '2025-10-03 08:00:00'),

-- Notificações de Ações em Membros (Exemplos históricos)
(11, 'Bem-vindo à Atlética! 🎉', 'Parabéns! Sua solicitação para se juntar à A.A.A. TOURADA foi aprovada. Agora você é um membro oficial e pode participar das atividades e eventos!', 'info', NULL, 1, '2025-07-20 14:30:00'),
(12, 'Bem-vindo à Atlética! 🎉', 'Parabéns! Sua solicitação para se juntar à A.A.A. RAPOSADA foi aprovada. Agora você é um membro oficial e pode participar das atividades e eventos!', 'info', NULL, 1, '2025-07-22 16:45:00'),
(13, 'Bem-vindo à Atlética! 🎉', 'Parabéns! Sua solicitação para se juntar à A.A.A. ÁGUIAS foi aprovada. Agora você é um membro oficial e pode participar das atividades e eventos!', 'info', NULL, 1, '2025-07-25 10:20:00'),
(4, 'Promoção a Administrador! 🚀', 'Parabéns! Você foi promovido a Administrador da A.A.A. RAPOSADA. Agora você tem permissões especiais para gerenciar membros, eventos e inscrições.', 'info', NULL, 1, '2025-08-05 11:00:00'),
(17, 'Solicitação Não Aprovada 😔', 'Sua solicitação para se juntar à A.A.A. TOURADA não foi aprovada desta vez. Você pode fazer uma nova solicitação no futuro.', 'aviso', NULL, 1, '2025-09-10 15:30:00');

-- ===================================================================
-- SOLICITAÇÕES DE TROCA DE CURSO
-- ===================================================================
-- Tabela: solicitacoes_troca_curso
-- Descrição: Pedidos de alunos para trocar de curso
-- ===================================================================

INSERT INTO `solicitacoes_troca_curso` (`id`, `usuario_id`, `curso_atual_id`, `curso_novo_id`, `justificativa`, `status`, `data_solicitacao`, `data_resposta`, `respondido_por`, `justificativa_resposta`) VALUES
-- Solicitação 1: Pedro Silva (id: 6) quer trocar de Enfermagem para Fisioterapia
(1, 6, 3, 2, 'Descobri que tenho mais afinidade com a área de reabilitação física. Durante meu estágio no hospital, tive contato com fisioterapeutas e me identifiquei muito com o trabalho. Acredito que esta mudança me permitirá desenvolver melhor minhas habilidades na área que realmente quero atuar.', 'pendente', '2025-10-08 10:30:00', NULL, NULL, NULL),

-- Solicitação 2: Juliana Costa (id: 8) quer trocar de Farmácia para Biomedicina (RECUSADA)
(2, 8, 5, 4, 'Após cursar algumas disciplinas, percebi que meu interesse maior está na área de análises clínicas e pesquisa laboratorial, que são o foco da Biomedicina. Gostaria de poder trabalhar mais diretamente com diagnósticos e pesquisas científicas.', 'recusada', '2025-10-10 14:15:00', '2025-10-12 09:30:00', 1, 'A solicitação foi recusada pois o aluno está no 6º semestre e a mudança de curso neste momento prejudicaria significativamente seu progresso acadêmico. Recomendamos aguardar a conclusão do curso atual ou considerar a possibilidade de pós-graduação na área desejada.'),

-- Solicitação 3: Ana Santos (id: 9) quer trocar de Administração para Psicologia (APROVADA)
(3, 9, 1, 3, 'Tenho interesse em trabalhar na área de saúde mental e acredito que a Psicologia me dará uma base mais sólida para atuar com pacientes que precisam de apoio psicológico.', 'aprovada', '2025-10-15 16:45:00', '2025-10-16 10:20:00', 1, NULL);

-- Comitando as alterações
COMMIT;

-- Reativa a verificação de chaves estrangeiras
SET FOREIGN_KEY_CHECKS=1;

-- ===================================================================
-- ATUALIZAÇÕES DE SCHEMA
-- ===================================================================

-- Adicionar novo tipo de notificação para eventos cancelados por campeonato
-- (A coluna cancelado_por_campeonato já está no schema principal)
ALTER TABLE `notificacoes` 
MODIFY COLUMN `tipo` enum('agendamento_aprovado','agendamento_rejeitado','agendamento_cancelado','agendamento_cancelado_admin','agendamento_editado','agendamento_alterado','presenca_confirmada','lembrete_evento','evento_cancelado_campeonato','info','aviso','sistema') 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

-- ===================================================================
-- FIM DO SCRIPT DE POPULAÇÃO
-- 
-- ✅ Dados inseridos com sucesso!
-- ✅ UTF8MB4 está configurado e suporta:
--    - Todos os caracteres com acentos (José, Ação, Comunicação)
--    - Emojis (🎯, 🏆, ⚽, 🎉, 😀)
--    - Caracteres especiais de múltiplos idiomas
-- ===================================================================
