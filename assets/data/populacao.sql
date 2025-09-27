--
-- SCRIPT DE POPULAÇÃO DE DADOS FINAL (INCLUINDO CORREÇÕES E CRIAÇÃO DE LOOKUPS)
--

-- 1. Desativa temporariamente a checagem de chaves estrangeiras.
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Garante a existência das tabelas de LOOKUP (caso o seu banco esteja completamente vazio)
-- As tabelas de LOOKUP devem ser criadas antes de serem referenciadas em FKs.

-- Tabela `modalidades` (Se não existir)
CREATE TABLE IF NOT EXISTS `modalidades` (
                                             `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(100) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabela `eventos` (Se não existir)
CREATE TABLE IF NOT EXISTS `eventos` (
                                         `id` int(11) NOT NULL AUTO_INCREMENT,
    `nome` varchar(255) NOT NULL,
    `data_inicio` date NOT NULL,
    `data_fim` date NOT NULL,
    `ativo` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- 3. Limpa todas as tabelas na ordem inversa de dependência (COM ORDEM CORRETA DE TRUNCATE)
-- A ordem é crucial para evitar o erro #1701.
TRUNCATE TABLE `professores_cursos`;

-- Tabelas que dependem de AGENDAMENTOS
TRUNCATE TABLE `inscricoes_eventos`;
TRUNCATE TABLE `presencas`;

-- Tabela AGENDAMENTOS (pode ser truncada agora)
TRUNCATE TABLE `agendamentos`;

-- Demais tabelas dependentes
TRUNCATE TABLE `equipe_membros`;
TRUNCATE TABLE `equipes`;
TRUNCATE TABLE `inscricoes_modalidade`;
TRUNCATE TABLE `usuarios`;
TRUNCATE TABLE `cursos`;
TRUNCATE TABLE `atleticas`;
TRUNCATE TABLE `eventos`;
TRUNCATE TABLE `modalidades`;


--
-- 4. População das tabelas
--

-- POPULAÇÃO: `atleticas` (10 Registros)
INSERT INTO `atleticas` (`id`, `nome`, `descricao`, `logo_url`) VALUES
                                                                    (1, 'Atlética de Engenharia Elétrica - Faísca', 'A atlética mais energizada do campus.', 'https://placeholder.com/1'),
                                                                    (2, 'Atlética de Ciência da Computação - A.T.I', 'Conectando mentes e promovendo o esporte.', 'https://placeholder.com/2'),
                                                                    (3, 'Atlética de Direito - Lex', 'Pela honra e pela glória do esporte e da justiça.', 'https://placeholder.com/3'),
                                                                    (4, 'Atlética de Medicina - Med', 'Saúde em primeiro lugar, dentro e fora das quadras.', 'https://placeholder.com/4'),
                                                                    (5, 'Atlética de Arquitetura e Urbanismo - Traço', 'Construindo vitórias e grandes amizades.', 'https://placeholder.com/5'),
                                                                    (6, 'Atlética de Psicologia - Psique', 'Mente sã, corpo são e muita garra no esporte.', 'https://placeholder.com/6'),
                                                                    (7, 'Atlética de Educação Física - Movimento', 'O corpo alcança o que a mente acredita.', 'https://placeholder.com/7'),
                                                                    (8, 'Atlética de Relações Internacionais - Diplomacia', 'Unindo nações através do esporte.', 'https://placeholder.com/8'),
                                                                    (9, 'Atlética de Engenharia Civil - Concreta', 'Fortes como concreto, unidos pela vitória.', 'https://placeholder.com/9'),
                                                                    (10, 'Atlética de Administração - Gestores', 'Planejando o sucesso, executando a vitória.', 'https://placeholder.com/10');
ALTER TABLE `atleticas` AUTO_INCREMENT = 11;


-- POPULAÇÃO: `cursos` (10 Registros - Ligados às Atléticas)
INSERT INTO `cursos` (`id`, `nome`, `atletica_id`, `coordenador_id`) VALUES
                                                                         (1, 'Engenharia Elétrica', 1, NULL),
                                                                         (2, 'Ciência da Computação', 2, NULL),
                                                                         (3, 'Direito', 3, NULL),
                                                                         (4, 'Medicina', 4, NULL),
                                                                         (5, 'Arquitetura e Urbanismo', 5, NULL),
                                                                         (6, 'Psicologia', 6, NULL),
                                                                         (7, 'Educação Física', 7, NULL),
                                                                         (8, 'Relações Internacionais', 8, NULL),
                                                                         (9, 'Engenharia Civil', 9, NULL),
                                                                         (10, 'Administração', 10, NULL);
ALTER TABLE `cursos` AUTO_INCREMENT = 11;


-- POPULAÇÃO: `usuarios` (15 Registros de Teste)
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ra`, `data_nascimento`, `curso_id`, `role`, `atletica_id`, `tipo_usuario_detalhado`, `is_coordenador`, `atletica_join_status`) VALUES
                                                                                                                                                                                            (1, 'Super Admin', 'sadmin@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, NULL, NULL, 'superadmin', NULL, NULL, 0, 'none'),
                                                                                                                                                                                            (2, 'Admin Geral', 'admin@sge.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1980-01-01', NULL, 'admin', NULL, NULL, 0, 'none'),
                                                                                                                                                                                            (3, 'Professor Coordenador 1', 'coord1@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1980-01-01', 1, 'usuario', NULL, 'Professor', 1, 'none'),
                                                                                                                                                                                            (4, 'Professor Coordenador 2', 'coord2@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1980-01-01', 2, 'usuario', NULL, 'Professor', 1, 'none'),
                                                                                                                                                                                            (5, 'Professor Comum 1', 'prof1@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1980-01-01', 3, 'usuario', NULL, 'Professor', 0, 'none'),
                                                                                                                                                                                            (6, 'Professor Comum 2', 'prof2@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1980-01-01', 4, 'usuario', NULL, 'Professor', 0, 'none'),
                                                                                                                                                                                            (7, 'Aluno Comum 1 (Eng. Eletrica)', 'aluno1@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '000010', '2000-01-01', 1, 'usuario', NULL, 'Aluno', 0, 'none'),
                                                                                                                                                                                            (8, 'Aluno Comum 2 (Comp.)', 'aluno2@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '000011', '2000-01-01', 2, 'usuario', NULL, 'Aluno', 0, 'none'),
                                                                                                                                                                                            (9, 'Aluno Comum 3 (Direito)', 'aluno3@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '000012', '2000-01-01', 3, 'usuario', NULL, 'Aluno', 0, 'none'),
                                                                                                                                                                                            (10, 'Aluno Comum 4 (Medicina)', 'aluno4@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '000013', '2000-01-01', 4, 'usuario', NULL, 'Aluno', 0, 'none'),
                                                                                                                                                                                            (11, 'Membro da Atlética Aprovado (Faísca)', 'membro1@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '000014', '2000-01-01', 1, 'usuario', 1, 'Membro das Atléticas', 0, 'aprovado'),
                                                                                                                                                                                            (12, 'Membro da Atlética Pendente (A.T.I)', 'membro2@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '000015', '2000-01-01', 2, 'usuario', 2, 'Membro das Atléticas', 0, 'pendente'),
                                                                                                                                                                                            (13, 'Admin da Atlética (Faísca)', 'adm_atletica1@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '000016', '2000-01-01', 1, 'admin', 1, 'Membro das Atléticas', 0, 'aprovado'),
                                                                                                                                                                                            (14, 'Admin da Atlética (Lex)', 'adm_atletica2@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', '000017', '2000-01-01', 3, 'admin', 3, 'Membro das Atléticas', 0, 'aprovado'),
                                                                                                                                                                                            (15, 'Comunidade Externa', 'externo1@teste.com', '$2y$10$IOB3SLdVtyDNNYxzatsPPuzI1OvyamWeACeryu6KuKpolRSKbqj5O', NULL, '1975-01-01', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none');
ALTER TABLE `usuarios` AUTO_INCREMENT = 16;


-- LIGAÇÃO: Atualização da tabela `cursos` (Ligando os Coordenadores)
UPDATE `cursos` SET `coordenador_id` = 3 WHERE `id` = 1;
UPDATE `cursos` SET `coordenador_id` = 4 WHERE `id` = 2;
UPDATE `cursos` SET `coordenador_id` = 5 WHERE `id` = 3;
UPDATE `cursos` SET `coordenador_id` = 6 WHERE `id` = 4;


-- POPULAÇÃO: `professores_cursos` (-- Desativa a verificação de chaves estrangeiras para evitar erros de ordem de inserção
-- SET FOREIGN_KEY_CHECKS=0;
-- START TRANSACTION;
--
-- --
-- -- Adicionando 10 novas ATLÉTICAS (começando do ID 2)
-- --
-- INSERT INTO `atleticas` (`id`, `nome`, `descricao`, `logo_url`) VALUES
-- (2, 'Tubarões da Engenharia', 'Atlética dos cursos de Engenharia.', 'https://example.com/logo_tubaroes.png'),
-- (3, 'Serpentes do Direito', 'Atlética do curso de Direito.', 'https://example.com/logo_serpentes.png'),
-- (4, 'Corujas da Medicina', 'Atlética do curso de Medicina.', 'https://example.com/logo_corujas.png'),
-- (5, 'Lobos da Computação', 'Atlética dos cursos de TI.', 'https://example.com/logo_lobos.png'),
-- (6, 'Águias da Administração', 'Atlética do curso de Administração.', 'https://example.com/logo_aguias.png'),
-- (7, 'Leões da Educação Física', 'Atlética do curso de Educação Física.', 'https://example.com/logo_leoes.png'),
-- (8, 'Tigres da Comunicação', 'Atlética dos cursos de Comunicação.', 'https://example.com/logo_tigres.png'),
-- (9, 'Panteras da Psicologia', 'Atlética do curso de Psicologia.', 'https://example.com/logo_panteras.png'),
-- (10, 'ursos da Arquitetura', 'Atlética do curso de Arquitetura.', 'https://example.com/logo_ursos.png'),
-- (11, 'Raposas da Biologia', 'Atlética dos cursos de Ciências Biológicas.', 'https://example.com/logo_raposas.png');
--
-- --
-- -- Adicionando 10 novas MODALIDADES
-- --
-- INSERT INTO `modalidades` (`id`, `nome`) VALUES
-- (1, 'Futebol de Campo'),
-- (2, 'Futsal'),
-- (3, 'Voleibol'),
-- (4, 'Basquetebol'),
-- (5, 'Handebol'),
-- (6, 'Natação'),
-- (7, 'Atletismo'),
-- (8, 'Tênis de Mesa'),
-- (9, 'Xadrez'),
-- (10, 'E-sports');
--
-- --
-- -- Adicionando 10 novos CURSOS (começando do ID 2)
-- --
-- INSERT INTO `cursos` (`id`, `nome`, `atletica_id`, `coordenador_id`) VALUES
-- (2, 'Direito', 3, NULL),
-- (3, 'Medicina', 4, NULL),
-- (4, 'Ciência da Computação', 5, NULL),
-- (5, 'Administração', 6, NULL),
-- (6, 'Educação Física', 7, NULL),
-- (7, 'Jornalismo', 8, NULL),
-- (8, 'Psicologia', 9, NULL),
-- (9, 'Arquitetura e Urbanismo', 10, NULL),
-- (10, 'Ciências Biológicas', 11, NULL),
-- (11, 'Engenharia Civil', 2, NULL);
--
-- --
-- -- Adicionando 12 novos USUÁRIOS (começando do ID 8)
-- --
-- INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `ra`, `data_nascimento`, `curso_id`, `role`, `atletica_id`, `tipo_usuario_detalhado`, `is_coordenador`, `atletica_join_status`) VALUES
-- (8, 'Ana Silva', '000004@unifio.edu.br', '$2y$10$...placeholder...', '000004', '2002-05-15', 4, 'usuario', 5, 'Membro das Atléticas', 0, 'aprovado'),
-- (9, 'Bruno Costa', '000005@unifio.edu.br', '$2y$10$...placeholder...', '000005', '2001-08-20', 2, 'usuario', 3, 'Membro das Atléticas', 0, 'aprovado'),
-- (10, 'Carla Dias', '000006@unifio.edu.br', '$2y$10$...placeholder...', '000006', '2003-01-30', 3, 'usuario', NULL, 'Aluno', 0, 'none'),
-- (11, 'Prof. Douglas Lima', 'professor.douglas@unifio.edu.br', '$2y$10$...placeholder...', NULL, '1980-11-10', 4, 'usuario', NULL, 'Professor', 0, 'none'),
-- (12, 'Eduarda Martins', '000007@unifio.edu.br', '$2y$10$...placeholder...', '000007', '2000-03-25', 11, 'admin', 2, 'Membro das Atléticas', 0, 'aprovado'),
-- (13, 'Felipe Souza', '000008@unifio.edu.br', '$2y$10$...placeholder...', '000008', '2002-07-12', 6, 'usuario', 7, 'Membro das Atléticas', 0, 'aprovado'),
-- (14, 'Gabriela Pereira', '000009@unifio.edu.br', '$2y$10$...placeholder...', '000009', '2004-09-05', 8, 'usuario', NULL, 'Aluno', 0, 'pendente'),
-- (15, 'Prof. Helena Santos', 'professora.helena@unifio.edu.br', '$2y$10$...placeholder...', NULL, '1985-04-18', 2, 'usuario', NULL, 'Professor', 1, 'none'),
-- (16, 'Igor Almeida', 'visitante.igor@email.com', '$2y$10$...placeholder...', NULL, '1995-02-22', NULL, 'usuario', NULL, 'Comunidade Externa', 0, 'none'),
-- (17, 'Juliana Rocha', '000010@unifio.edu.br', '$2y$10$...placeholder...', '000010', '2001-12-01', 5, 'admin', 6, 'Membro das Atléticas', 0, 'aprovado'),
-- (18, 'Prof. Coordenador Direito', 'coordenador.direito@unifio.edu.br', '$2y$10$...placeholder...', NULL, '1975-06-09', 2, 'admin', NULL, 'Professor', 1, 'none'),
-- (19, 'Lucas Andrade', '000011@unifio.edu.br', '$2y$10$...placeholder...', '000011', '2002-10-18', 1, 'usuario', 1, 'Membro das Atléticas', 0, 'aprovado');
-- -- Atualiza o curso de Direito para ter o coordenador correto
-- UPDATE `cursos` SET `coordenador_id` = 18 WHERE `id` = 2;
--
-- --
-- -- Adicionando 10 relações PROFESSORES <-> CURSOS
-- --
-- INSERT INTO `professores_cursos` (`professor_id`, `curso_id`) VALUES
-- (11, 4), -- Prof. Douglas -> Ciência da Computação
-- (11, 1), -- Prof. Douglas -> Engenharia de Software
-- (15, 2), -- Profa. Helena -> Direito
-- (18, 2), -- Prof. Coordenador -> Direito
-- (11, 11), -- Prof. Douglas -> Engenharia Civil
-- (15, 5), -- Profa. Helena -> Administração
-- (18, 5), -- Prof. Coordenador -> Administração
-- (11, 9), -- Prof. Douglas -> Arquitetura
-- (15, 8), -- Profa. Helena -> Psicologia
-- (18, 7); -- Prof. Coordenador -> Jornalismo
--
-- --
-- -- Adicionando 10 EQUIPES
-- --
-- INSERT INTO `equipes` (`nome`, `modalidade_id`, `atletica_id`) VALUES
-- ('Lobos Futsal Masculino', 2, 5),
-- ('Serpentes Vôlei Feminino', 3, 3),
-- ('Tubarões Basquete', 4, 2),
-- ('Corujas Handebol', 5, 4),
-- ('Galinhas Cyber E-sports', 10, 1),
-- ('Águias Tênis de Mesa', 8, 6),
-- ('Leões Atletismo', 7, 7),
-- ('Tigres Xadrez', 9, 8),
-- ('Panteras Natação', 6, 9),
-- ('Lobos Vôlei Masculino', 3, 5);
--
-- --
-- -- Adicionando 10+ MEMBROS às equipes
-- --
-- INSERT INTO `equipe_membros` (`equipe_id`, `aluno_id`) VALUES
-- (1, 8),   -- Ana Silva no Lobos Futsal
-- (1, 4),   -- MEMBRO DAS ATLETICAS (TESTE) no Lobos Futsal
-- (2, 9),   -- Bruno Costa no Serpentes Vôlei
-- (3, 12),  -- Eduarda Martins no Tubarões Basquete
-- (4, 3),   -- ALUNO (TESTE) no Corujas Handebol (aluno pode participar)
-- (5, 19),  -- Lucas Andrade no Galinhas Cyber E-sports
-- (5, 6),   -- ADMIN DA ATLETICA no Galinhas Cyber E-sports
-- (6, 17),  -- Juliana Rocha no Águias Tênis de Mesa
-- (7, 13),  -- Felipe Souza no Leões Atletismo
-- (10, 8),  -- Ana Silva no Lobos Vôlei
-- (2, 14);  -- Gabriela Pereira no Serpentes Vôlei
--
-- --
-- -- Adicionando 10 AGENDAMENTOS
-- --
-- INSERT INTO `agendamentos` (`usuario_id`, `titulo`, `tipo_agendamento`, `esporte_tipo`, `data_agendamento`, `periodo`, `descricao`, `status`, `responsavel_evento`) VALUES
-- (8, 'Treino Aberto de Futsal', 'esportivo', 'Futsal', '2025-10-03', 'primeiro', 'Treino aberto para seleção de novos atletas para a equipe dos Lobos.', 'aprovado', 'Ana Silva'),
-- (9, 'Campeonato Interno de Vôlei', 'esportivo', 'Voleibol', '2025-10-10', 'tarde', 'Fase de grupos do campeonato interno de vôlei das Serpentes.', 'aprovado', 'Bruno Costa'),
-- (12, 'Palestra: IA e o Futuro da Engenharia', 'nao_esportivo', NULL, '2025-10-05', 'noite', 'Palestra com especialista da área sobre o impacto da Inteligência Artificial.', 'pendente', 'Eduarda Martins'),
-- (11, 'Reunião de Planejamento da Atlética', 'nao_esportivo', NULL, '2025-09-30', 'segundo', 'Reunião para definir o calendário de eventos do próximo semestre.', 'rejeitado', 'Prof. Douglas Lima'),
-- (17, 'Workshop de Finanças para Universitários', 'nao_esportivo', 'Workshop', '2025-10-15', 'manha', 'Workshop sobre investimentos e planejamento financeiro.', 'aprovado', 'Juliana Rocha'),
-- (13, 'Seletiva de Atletismo', 'esportivo', 'Atletismo', '2025-10-01', 'tarde', 'Seletiva para todas as modalidades de atletismo.', 'aprovado', 'Felipe Souza'),
-- (19, 'Torneio de E-Sports (LoL)', 'esportivo', 'E-sports', '2025-10-18', 'tarde', 'Torneio de League of Legends entre as atléticas.', 'pendente', 'Lucas Andrade'),
-- (16, 'Evento Beneficente Comunitário', 'nao_esportivo', 'Evento Social', '2025-11-01', 'manha', 'Arrecadação de alimentos e agasalhos para a comunidade local.', 'aprovado', 'Igor Almeida'),
-- (6, 'Final do Campeonato de Basquete', 'esportivo', 'Basquetebol', '2025-11-15', 'primeiro', 'Grande final do campeonato inter-atléticas de basquete.', 'aprovado', 'ADMIN DA ATLETICA'),
-- (7, 'Aula de Yoga aberta ao público', 'nao_esportivo', 'Bem-estar', '2025-10-08', 'manha', 'Aula de yoga gratuita para alunos e comunidade externa.', 'pendente', 'COMUNIDADE EXTERNA (TESTE)');
--
-- --
-- -- Adicionando 10 EVENTOS (tabela 'eventos' parece desconectada, mas populando conforme solicitado)
-- --
-- INSERT INTO `eventos` (`nome`, `data_inicio`, `data_fim`, `ativo`) VALUES
-- ('Jogos Intercursos 2025', '2025-10-20', '2025-10-25', 1),
-- ('Semana Acadêmica de Tecnologia', '2025-11-03', '2025-11-07', 1),
-- ('Feira de Profissões', '2025-09-29', '2025-09-29', 1),
-- ('Festa de Halloween das Atléticas', '2025-10-31', '2025-10-31', 1),
-- ('Campanha de Doação de Sangue', '2025-11-10', '2025-11-12', 1),
-- ('Congresso de Direito Constitucional', '2025-11-18', '2025-11-20', 0),
-- ('Maratona de Programação (Hackathon)', '2025-12-05', '2025-12-06', 1),
-- ('Apresentação Cultural de Fim de Ano', '2025-12-10', '2025-12-10', 1),
-- ('Copa UNIFIO de Futsal', '2026-03-15', '2026-03-22', 1),
-- ('Simpósio de Saúde e Bem-estar', '2026-04-01', '2026-04-03', 1);
--
-- --
-- -- Adicionando 10+ registros de PRESENÇAS em agendamentos aprovados
-- --
-- INSERT INTO `presencas` (`usuario_id`, `agendamento_id`) VALUES
-- (8, 1), -- Ana Silva no Treino de Futsal
-- (4, 1), -- MEMBRO DAS ATLETICAS (TESTE) no Treino de Futsal
-- (9, 2), -- Bruno Costa no Campeonato de Vôlei
-- (14, 2), -- Gabriela Pereira no Campeonato de Vôlei
-- (17, 5), -- Juliana Rocha no Workshop de Finanças
-- (10, 5), -- Carla Dias no Workshop de Finanças
-- (16, 5), -- Igor Almeida no Workshop de Finanças
-- (13, 6), -- Felipe Souza na Seletiva de Atletismo
-- (3, 6), -- ALUNO (TESTE) na Seletiva de Atletismo
-- (16, 8), -- Igor Almeida no Evento Beneficente
-- (7, 8); -- COMUNIDADE EXTERNA (TESTE) no Evento Beneficente
--
-- --
-- -- Adicionando 10 INSCRIÇÕES em modalidades
-- --
-- INSERT INTO `inscricoes_modalidade` (`aluno_id`, `modalidade_id`, `atletica_id`, `status`) VALUES
-- (8, 2, 5, 'aprovado'), -- Ana Silva, Futsal, Lobos
-- (9, 3, 3, 'aprovado'), -- Bruno Costa, Voleibol, Serpentes
-- (10, 4, 4, 'pendente'), -- Carla Dias, Basquetebol, Corujas
-- (12, 4, 2, 'aprovado'), -- Eduarda Martins, Basquetebol, Tubarões
-- (13, 7, 7, 'aprovado'), -- Felipe Souza, Atletismo, Leões
-- (14, 3, 3, 'recusado'), -- Gabriela Pereira, Voleibol, Serpentes
-- (19, 10, 1, 'aprovado'), -- Lucas Andrade, E-sports, Galinhas
-- (3, 5, 4, 'aprovado'), -- ALUNO (TESTE), Handebol, Corujas
-- (4, 2, 5, 'pendente'), -- MEMBRO (TESTE), Futsal, Lobos
-- (6, 10, 1, 'aprovado'); -- ADMIN DA ATLETICA, E-sports, Galinhas
--
-- --
-- -- Adicionando 10 INSCRIÇÕES em eventos/agendamentos
-- -- OBS: A FK `evento_id` desta tabela aponta para a tabela `agendamentos`
-- --
-- INSERT INTO `inscricoes_eventos` (`aluno_id`, `evento_id`, `atletica_id`, `status`) VALUES
-- (8, 1, 5, 'aprovado'),  -- Ana Silva no Treino Aberto de Futsal
-- (9, 2, 3, 'aprovado'),  -- Bruno Costa no Campeonato Interno de Vôlei
-- (10, 2, 4, 'pendente'), -- Carla Dias no Campeonato Interno de Vôlei (outra atlética)
-- (12, 9, 2, 'aprovado'), -- Eduarda Martins na Final do Campeonato de Basquete
-- (13, 6, 7, 'aprovado'), -- Felipe Souza na Seletiva de Atletismo
-- (19, 7, 1, 'aprovado'), -- Lucas Andrade no Torneio de E-Sports
-- (17, 5, 6, 'aprovado'), -- Juliana Rocha no Workshop de Finanças
-- (16, 8, 2, 'aprovado'), -- Igor Almeida (Com. Externa) no Evento Beneficente (associado a uma atletica)
-- (3, 6, 4, 'aprovado'),  -- ALUNO (TESTE) na Seletiva de Atletismo
-- (4, 1, 5, 'recusado'); -- MEMBRO (TESTE) no Treino Aberto de Futsal
--
-- -- Ativa a verificação de chaves estrangeiras novamente
-- COMMIT;
-- SET FOREIGN_KEY_CHECKS=1;10 Registros)
INSERT INTO `professores_cursos` (`professor_id`, `curso_id`) VALUES
                                                                  (3, 1), (3, 3), (3, 5), (4, 2), (4, 4), (4, 6), (5, 7), (5, 8), (6, 9), (6, 10);
ALTER TABLE `professores_cursos` AUTO_INCREMENT = 11;


-- POPULAÇÃO MÍNIMA: `modalidades` e `eventos` (Para uso futuro)
INSERT INTO `modalidades` (`id`, `nome`) VALUES (1, 'Futsal'), (2, 'Vôlei'), (3, 'Basquete') ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);
ALTER TABLE `modalidades` AUTO_INCREMENT = 4;

INSERT INTO `eventos` (`id`, `nome`, `data_inicio`, `data_fim`, `ativo`) VALUES
                                                                             (1, 'Jogos Universitários de Outono', '2025-11-01', '2025-11-10', 1),
                                                                             (2, 'Copa Interatléticas', '2025-12-05', '2025-12-08', 1) ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`);
ALTER TABLE `eventos` AUTO_INCREMENT = 3;


-- 5. Reativa a checagem de chaves estrangeiras.
SET FOREIGN_KEY_CHECKS = 1;