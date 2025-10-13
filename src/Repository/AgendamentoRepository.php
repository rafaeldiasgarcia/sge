<?php
/**
 * Repositório de Agendamentos (AgendamentoRepository)
 * 
 * Camada de acesso a dados para a tabela 'agendamentos'.
 * Gerencia todos os aspectos relacionados a agendamentos de eventos esportivos
 * e não esportivos na quadra poliesportiva.
 * 
 * Responsabilidades principais:
 * - CRUD de agendamentos (criar, buscar, atualizar, deletar)
 * - Verificação de disponibilidade de horários
 * - Aprovação e rejeição de agendamentos
 * - Cancelamento de eventos
 * - Gerenciamento de presenças em eventos
 * - Validação de regras de negócio (limite de treinos semanais, horários ocupados)
 * - Busca de eventos por diversos filtros (data, período, status, usuário)
 * - Cálculo de ocupação da quadra por período
 * - Atualização automática de status (eventos passados para 'finalizado')
 * 
 * Regras de Negócio:
 * - Cada horário/período pode ter apenas 1 agendamento aprovado
 * - Atléticas podem ter no máximo 1 treino por modalidade por semana
 * - Usuários podem agendar no máximo 1 evento esportivo por modalidade por semana
 * - Eventos com status 'aprovado' aparecem no calendário para todos
 * - Presenças só podem ser marcadas em eventos aprovados
 * 
 * Períodos disponíveis:
 * - 'primeiro': 19:15 - 20:55
 * - 'segundo': 21:10 - 22:50
 * 
 * @package Application\Repository
 */
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class AgendamentoRepository
{
    /**
     * Instância da conexão PDO
     * @var PDO
     */
    private $pdo;

    /**
     * Construtor - Obtém a instância única da conexão com o banco
     */
    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function findAgendaEvents(int $usuarioId): array
    {
        $sql = "SELECT a.id, a.titulo, a.tipo_agendamento, a.esporte_tipo, a.data_agendamento, a.periodo, 
                       u.nome as responsavel, p.id as presenca_id, a.atletica_confirmada, 
                       a.atletica_id_confirmada, a.quantidade_atletica, at.nome as atletica_nome,
                       (SELECT COUNT(*) FROM presencas p2 WHERE p2.agendamento_id = a.id) as total_presencas,
                       CASE 
                           WHEN a.periodo = 'primeiro' THEN '19:15 - 20:55'
                           WHEN a.periodo = 'segundo' THEN '21:10 - 22:50'
                           ELSE a.periodo
                       END as horario_periodo
                FROM agendamentos a
                JOIN usuarios u ON a.usuario_id = u.id
                LEFT JOIN presencas p ON a.id = p.agendamento_id AND p.usuario_id = :usuario_id
                LEFT JOIN atleticas at ON a.atletica_id_confirmada = at.id
                WHERE a.status IN ('aprovado', 'finalizado')
                ORDER BY a.data_agendamento ASC, a.periodo ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function marcarPresenca(int $usuarioId, int $agendamentoId): bool
    {
        $sql = "INSERT INTO presencas (usuario_id, agendamento_id) VALUES (:usuario_id, :agendamento_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':agendamento_id', $agendamentoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function desmarcarPresenca(int $usuarioId, int $agendamentoId): bool
    {
        $sql = "DELETE FROM presencas WHERE usuario_id = :usuario_id AND agendamento_id = :agendamento_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':agendamento_id', $agendamentoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function isSlotOccupied(string $data, string $periodo, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(id) FROM agendamentos WHERE data_agendamento = :data AND periodo = :periodo AND status = 'aprovado'";

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':data', $data);
        $stmt->bindValue(':periodo', $periodo);

        if ($excludeId !== null) {
            $stmt->bindValue(':exclude_id', $excludeId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function createAgendamento(array $data): bool
    {
        $sql = "INSERT INTO agendamentos (
            usuario_id, titulo, tipo_agendamento, subtipo_evento, esporte_tipo, 
            data_agendamento, periodo, descricao, quantidade_pessoas, responsavel_evento,
            possui_materiais, materiais_necessarios, responsabiliza_devolucao, 
            lista_participantes, arquivo_participantes, arbitro_partida,
            estimativa_participantes, evento_aberto_publico, descricao_publico_alvo, 
            infraestrutura_adicional, observacoes, status
        ) VALUES (
            :usuario_id, :titulo, :tipo_agendamento, :subtipo_evento, :esporte_tipo,
            :data_agendamento, :periodo, :descricao, :quantidade_pessoas, :responsavel_evento,
            :possui_materiais, :materiais_necessarios, :responsabiliza_devolucao,
            :lista_participantes, :arquivo_participantes, :arbitro_partida,
            :estimativa_participantes, :evento_aberto_publico, :descricao_publico_alvo,
            :infraestrutura_adicional, :observacoes, 'pendente'
        )";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':usuario_id', $data['usuario_id'], PDO::PARAM_INT);
        $stmt->bindValue(':titulo', $data['titulo']);
        $stmt->bindValue(':tipo_agendamento', $data['tipo_agendamento']);
        $stmt->bindValue(':subtipo_evento', $data['subtipo_evento'] ?? null);
        $stmt->bindValue(':esporte_tipo', $data['esporte_tipo'] ?? null);
        $stmt->bindValue(':data_agendamento', $data['data_agendamento']);
        $stmt->bindValue(':periodo', $data['periodo']);
        $stmt->bindValue(':descricao', $data['descricao'] ?? null);
        $stmt->bindValue(':quantidade_pessoas', $data['quantidade_pessoas'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':responsavel_evento', $data['responsavel_evento']);
        $stmt->bindValue(':possui_materiais', isset($data['possui_materiais']) ? (int)$data['possui_materiais'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':materiais_necessarios', $data['materiais_necessarios'] ?? null);
        $stmt->bindValue(':responsabiliza_devolucao', isset($data['responsabiliza_devolucao']) ? (int)$data['responsabiliza_devolucao'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':lista_participantes', $data['lista_participantes'] ?? null);
        $stmt->bindValue(':arquivo_participantes', $data['arquivo_participantes'] ?? null);
        $stmt->bindValue(':arbitro_partida', $data['arbitro_partida'] ?? null);
        $stmt->bindValue(':estimativa_participantes', $data['estimativa_participantes'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':evento_aberto_publico', isset($data['evento_aberto_publico']) ? (int)$data['evento_aberto_publico'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':descricao_publico_alvo', $data['descricao_publico_alvo'] ?? null);
        $stmt->bindValue(':infraestrutura_adicional', $data['infraestrutura_adicional'] ?? null);
        $stmt->bindValue(':observacoes', $data['observacoes'] ?? null);

        return $stmt->execute();
    }

    public function verificaTreinoSemanal(int $atleticaId, string $esporte, string $dataEvento): bool
    {
        $dataEventoObj = new \DateTime($dataEvento);
        $inicioSemana = clone $dataEventoObj;
        $inicioSemana->modify('monday this week');
        $fimSemana = clone $dataEventoObj;
        $fimSemana->modify('sunday this week');

        $sql = "SELECT COUNT(*) FROM agendamentos a
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE u.atletica_id = :atletica_id 
                AND a.tipo_agendamento = 'esportivo'
                AND a.subtipo_evento = 'treino'
                AND a.esporte_tipo = :esporte
                AND a.data_agendamento BETWEEN :inicio_semana AND :fim_semana
                AND a.status IN ('aprovado', 'pendente')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        $stmt->bindValue(':esporte', $esporte);
        $stmt->bindValue(':inicio_semana', $inicioSemana->format('Y-m-d'));
        $stmt->bindValue(':fim_semana', $fimSemana->format('Y-m-d'));
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function cancelAgendamento(int $id, int $userId): bool
    {
        $sql = "UPDATE agendamentos SET status = 'cancelado', motivo_rejeicao = 'Cancelado pelo solicitante.' WHERE id = :id AND usuario_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findByUserId(int $userId): array
    {
        $sql = "SELECT id, titulo, tipo_agendamento, subtipo_evento, esporte_tipo, 
                       data_agendamento, periodo, status, motivo_rejeicao, responsavel_evento
                FROM agendamentos 
                WHERE usuario_id = :user_id 
                ORDER BY data_solicitacao DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByIdAndUserId(int $id, int $userId)
    {
        $sql = "SELECT * FROM agendamentos WHERE id = :id AND usuario_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateAgendamento(int $id, int $userId, array $data): bool
    {
        $sql = "UPDATE agendamentos 
                SET titulo = :titulo, 
                    tipo_agendamento = :tipo_agendamento,
                    subtipo_evento = :subtipo_evento,
                    esporte_tipo = :esporte_tipo,
                    data_agendamento = :data_agendamento, 
                    periodo = :periodo,
                    possui_materiais = :possui_materiais,
                    materiais_necessarios = :materiais_necessarios,
                    responsabiliza_devolucao = :responsabiliza_devolucao,
                    lista_participantes = :lista_participantes,
                    arbitro_partida = :arbitro_partida,
                    estimativa_participantes = :estimativa_participantes,
                    evento_aberto_publico = :evento_aberto_publico,
                    descricao_publico_alvo = :descricao_publico_alvo,
                    infraestrutura_adicional = :infraestrutura_adicional,
                    observacoes = :observacoes,
                    foi_editado = :foi_editado,
                    data_edicao = :data_edicao,
                    status = 'pendente'
                WHERE id = :id AND usuario_id = :user_id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':titulo', $data['titulo']);
        $stmt->bindValue(':tipo_agendamento', $data['tipo_agendamento']);
        $stmt->bindValue(':subtipo_evento', $data['subtipo_evento'] ?? null);
        $stmt->bindValue(':esporte_tipo', $data['esporte_tipo'] ?? null);
        $stmt->bindValue(':data_agendamento', $data['data_agendamento']);
        $stmt->bindValue(':periodo', $data['periodo']);
        $stmt->bindValue(':possui_materiais', $data['possui_materiais'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':materiais_necessarios', $data['materiais_necessarios'] ?? null);
        $stmt->bindValue(':responsabiliza_devolucao', $data['responsabiliza_devolucao'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':lista_participantes', $data['lista_participantes'] ?? null);
        $stmt->bindValue(':arbitro_partida', $data['arbitro_partida'] ?? null);
        $stmt->bindValue(':estimativa_participantes', $data['estimativa_participantes'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':evento_aberto_publico', $data['evento_aberto_publico'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':descricao_publico_alvo', $data['descricao_publico_alvo'] ?? null);
        $stmt->bindValue(':infraestrutura_adicional', $data['infraestrutura_adicional'] ?? null);
        $stmt->bindValue(':observacoes', $data['observacoes'] ?? null);
        $stmt->bindValue(':foi_editado', $data['foi_editado'] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(':data_edicao', $data['data_edicao'] ?? null);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findPendingAgendamentos(): array
    {
        $sql = "SELECT a.id, a.titulo, a.data_agendamento, a.periodo, u.nome as solicitante,
                       a.foi_editado, a.data_edicao
                FROM agendamentos a 
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.status = 'pendente' 
                ORDER BY a.data_agendamento ASC, a.periodo ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function findSlotById(int $id)
    {
        $sql = "SELECT data_agendamento, periodo FROM agendamentos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function approveAgendamento(int $id): bool
    {
        $sql = "UPDATE agendamentos SET status = 'aprovado', motivo_rejeicao = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function rejectAgendamento(int $id, string $motivo): bool
    {
        $sql = "UPDATE agendamentos SET status = 'rejeitado', motivo_rejeicao = :motivo WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':motivo', $motivo);
        return $stmt->execute();
    }

    public function findFutureEsportivoEvents(): array
    {
        $sql = "SELECT id, titulo, data_agendamento, esporte_tipo, descricao 
                FROM agendamentos 
                WHERE tipo_agendamento = 'esportivo' 
                AND status = 'aprovado' 
                AND data_agendamento >= CURDATE()
                ORDER BY data_agendamento ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function findAllForSelect(): array
    {
        $sql = "SELECT id, titulo, data_agendamento FROM agendamentos ORDER BY data_agendamento DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function findEventosComPresenca(int $userId): array
    {
        // Buscar próximo evento esportivo
        $sqlEsportivos = "SELECT a.id, a.titulo, a.tipo_agendamento, a.esporte_tipo, a.data_agendamento, a.periodo, 
                                 a.status, u.nome as responsavel, a.quantidade_pessoas,
                                 CASE 
                                     WHEN a.periodo = 'primeiro' THEN '19:15 - 20:55'
                                     WHEN a.periodo = 'segundo' THEN '21:10 - 22:50'
                                     ELSE a.periodo
                                 END as horario_periodo
                          FROM agendamentos a
                          JOIN usuarios u ON a.usuario_id = u.id
                          JOIN presencas p ON a.id = p.agendamento_id
                          WHERE p.usuario_id = :usuario_id 
                          AND a.status = 'aprovado'
                          AND a.data_agendamento >= CURDATE()
                          AND a.tipo_agendamento = 'esportivo'
                          ORDER BY a.data_agendamento ASC, a.periodo ASC
                          LIMIT 1";

        $stmt = $this->pdo->prepare($sqlEsportivos);
        $stmt->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $eventosEsportivos = $stmt->fetchAll();

        // Buscar próximo evento não esportivo
        $sqlNaoEsportivos = "SELECT a.id, a.titulo, a.tipo_agendamento, a.esporte_tipo, a.data_agendamento, a.periodo, 
                                    a.status, u.nome as responsavel, a.quantidade_pessoas,
                                    CASE 
                                        WHEN a.periodo = 'primeiro' THEN '19:15 - 20:55'
                                        WHEN a.periodo = 'segundo' THEN '21:10 - 22:50'
                                        ELSE a.periodo
                                    END as horario_periodo
                             FROM agendamentos a
                             JOIN usuarios u ON a.usuario_id = u.id
                             JOIN presencas p ON a.id = p.agendamento_id
                             WHERE p.usuario_id = :usuario_id 
                             AND a.status = 'aprovado'
                             AND a.data_agendamento >= CURDATE()
                             AND a.tipo_agendamento = 'nao_esportivo'
                             ORDER BY a.data_agendamento ASC, a.periodo ASC
                             LIMIT 1";

        $stmt = $this->pdo->prepare($sqlNaoEsportivos);
        $stmt->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $eventosNaoEsportivos = $stmt->fetchAll();

        // Combinar os resultados mantendo a separação por tipo
        return array_merge($eventosEsportivos, $eventosNaoEsportivos);
    }

    public function findTodosEventosComPresencaFuturos(int $userId): array
    {
        // Buscar TODOS os eventos futuros com presença marcada (do mais próximo ao mais distante)
        $sql = "SELECT a.id, a.titulo, a.tipo_agendamento, a.esporte_tipo, a.data_agendamento, a.periodo, 
                       a.status, u.nome as responsavel, a.quantidade_pessoas,
                       CASE 
                           WHEN a.periodo = 'primeiro' THEN '19:15 - 20:55'
                           WHEN a.periodo = 'segundo' THEN '21:10 - 22:50'
                           ELSE a.periodo
                       END as horario_periodo
                FROM agendamentos a
                JOIN usuarios u ON a.usuario_id = u.id
                JOIN presencas p ON a.id = p.agendamento_id
                WHERE p.usuario_id = :usuario_id 
                AND a.status = 'aprovado'
                AND a.data_agendamento >= CURDATE()
                ORDER BY a.data_agendamento ASC, 
                         CASE 
                            WHEN a.periodo = 'primeiro' THEN 1
                            WHEN a.periodo = 'segundo' THEN 2
                            WHEN a.periodo = 'manha' THEN 3
                            WHEN a.periodo = 'tarde' THEN 4
                            WHEN a.periodo = 'noite' THEN 5
                            ELSE 6
                         END ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function findOcupacaoPorMes(string $inicioMes, string $fimMes): array
    {
        $sql = "SELECT id, data_agendamento, periodo, status 
                FROM agendamentos 
                WHERE data_agendamento BETWEEN :ini AND :fim
                AND status IN ('aprovado', 'pendente')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['ini' => $inicioMes, 'fim' => $fimMes]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $sql = "SELECT a.*, 
                       CASE 
                           WHEN a.periodo = 'primeiro' THEN '19:15-20:55'
                           WHEN a.periodo = 'segundo' THEN '21:10-22:50'
                           ELSE a.periodo
                       END as horario_periodo,
                       u.nome as responsavel
                FROM agendamentos a
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function updatePastEventsToFinalized(): bool
    {
        $sql = "UPDATE agendamentos 
                SET status = 'finalizado' 
                WHERE status = 'aprovado' 
                AND data_agendamento < CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute();
    }

    public function getPresencasByAgendamento(int $agendamentoId): array
    {
        $sql = "SELECT p.usuario_id, u.nome, u.email, u.ra 
                FROM presencas p 
                JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.agendamento_id = :agendamento_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':agendamento_id', $agendamentoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findByIdWithDetails(int $id)
    {
        try {
            $sql = "SELECT a.*, 
                           u.nome as criador_nome, 
                           u.email as criador_email, 
                           u.telefone as criador_telefone,
                           u.ra as criador_ra,
                           u.tipo_usuario_detalhado as criador_tipo,
                           at.nome as atletica_nome,
                           at_conf.nome as atletica_confirmada_nome,
                           (SELECT COUNT(*) FROM presencas p WHERE p.agendamento_id = a.id) as total_presencas
                    FROM agendamentos a
                    JOIN usuarios u ON a.usuario_id = u.id
                    LEFT JOIN atleticas at ON u.atletica_id = at.id
                    LEFT JOIN atleticas at_conf ON a.atletica_id_confirmada = at_conf.id
                    WHERE a.id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch();
            return $result ?: null;

        } catch (\PDOException $e) {
            error_log('Erro SQL em findByIdWithDetails: ' . $e->getMessage());
            throw new \Exception('Erro ao buscar detalhes do agendamento: ' . $e->getMessage());
        }
    }

    public function hasUserSportEventInWeek(int $userId, string $date, string $esporteTipo): bool
    {
        // Converte a data do novo agendamento para objeto DateTime
        $dataNovoAgendamento = new \DateTime($date);

        // Encontra o início (segunda) e fim (domingo) da semana do novo agendamento
        $inicioSemana = (clone $dataNovoAgendamento)->modify('monday this week')->format('Y-m-d');
        $fimSemana = (clone $dataNovoAgendamento)->modify('sunday this week')->format('Y-m-d');

        $sql = "SELECT COUNT(*) FROM agendamentos 
                WHERE usuario_id = :usuario_id 
                AND tipo_agendamento = 'esportivo'
                AND esporte_tipo = :esporte_tipo
                AND DATE(data_agendamento) BETWEEN :inicio_semana AND :fim_semana
                AND status IN ('aprovado', 'pendente')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':esporte_tipo', $esporteTipo);
        $stmt->bindValue(':inicio_semana', $inicioSemana);
        $stmt->bindValue(':fim_semana', $fimSemana);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    public function findApprovedAgendamentos(): array
    {
        $sql = "SELECT a.id, a.titulo, a.tipo_agendamento, a.esporte_tipo, 
                       a.data_agendamento, a.periodo, u.nome as solicitante,
                       CASE 
                           WHEN a.periodo = 'primeiro' THEN '19:15 - 20:55'
                           WHEN a.periodo = 'segundo' THEN '21:10 - 22:50'
                           ELSE a.periodo
                       END as horario_periodo
                FROM agendamentos a 
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.status = 'aprovado' 
                AND a.data_agendamento >= CURDATE()
                ORDER BY a.data_agendamento ASC, a.periodo ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function cancelarAgendamentoAprovado(int $id, string $motivo): bool
    {
        $sql = "UPDATE agendamentos 
                SET status = 'cancelado', 
                    motivo_rejeicao = :motivo,
                    data_cancelamento = NOW(),
                    cancelado_por_admin = true
                WHERE id = :id AND status = 'aprovado'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':motivo', $motivo);
        return $stmt->execute();
    }

    public function updateAgendamentoAprovado(int $id, array $data): bool
    {
        $sql = "UPDATE agendamentos 
                SET data_agendamento = :data_agendamento,
                    periodo = :periodo,
                    observacoes_admin = :observacoes_admin,
                    data_ultima_alteracao = NOW(),
                    alterado_por_admin = true
                WHERE id = :id AND status = 'aprovado'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':data_agendamento', $data['data_agendamento']);
        $stmt->bindValue(':periodo', $data['periodo']);
        $stmt->bindValue(':observacoes_admin', $data['observacoes_admin']);
        return $stmt->execute();
    }

    public function findByDate(string $date): array
    {
        $sql = "SELECT * FROM agendamentos 
                WHERE data_agendamento = :date 
                AND status = 'aprovado'
                ORDER BY periodo ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
