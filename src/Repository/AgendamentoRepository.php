<?php
#
# Repositório para a tabela 'agendamentos'.
# Centraliza todas as interações com o banco de dados relacionadas a agendamentos,
# como criar, buscar, atualizar, aprovar, rejeitar e verificar a disponibilidade de horários.
#
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class AgendamentoRepository
{
    private $pdo;

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
                WHERE a.status = 'aprovado'
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

    public function isSlotOccupied(string $data, string $periodo): bool
    {
        $sql = "SELECT COUNT(id) FROM agendamentos WHERE data_agendamento = :data AND periodo = :periodo AND status = 'aprovado'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':data', $data);
        $stmt->bindValue(':periodo', $periodo);
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
                SET titulo = :titulo, data_agendamento = :data_agendamento, periodo = :periodo, descricao = :descricao, status = 'pendente' 
                WHERE id = :id AND usuario_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':titulo', $data['titulo']);
        $stmt->bindValue(':data_agendamento', $data['data_agendamento']);
        $stmt->bindValue(':periodo', $data['periodo']);
        $stmt->bindValue(':descricao', $data['descricao']);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findPendingAgendamentos(): array
    {
        $sql = "SELECT a.id, a.titulo, a.data_agendamento, a.periodo, u.nome as solicitante
                FROM agendamentos a 
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.status = 'pendente' 
                ORDER BY a.data_solicitacao ASC";
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
                                 u.nome as responsavel, a.quantidade_pessoas,
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
                                    u.nome as responsavel, a.quantidade_pessoas,
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

    public function findOcupacaoPorMes(string $inicioMes, string $fimMes): array
    {
        $sql = "SELECT data_agendamento, periodo, status 
                FROM agendamentos 
                WHERE data_agendamento BETWEEN :ini AND :fim
                AND status IN ('aprovado', 'pendente')";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['ini' => $inicioMes, 'fim' => $fimMes]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id)
    {
        $sql = "SELECT * FROM agendamentos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findByDate(string $date): array
    {
        $sql = "SELECT * FROM agendamentos WHERE data_agendamento = :date AND status = 'aprovado'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':date', $date);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPresencasByAgendamento(int $agendamentoId): array
    {
        $sql = "SELECT p.usuario_id, u.nome, u.email 
                FROM presencas p 
                JOIN usuarios u ON p.usuario_id = u.id 
                WHERE p.agendamento_id = :agendamento_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':agendamento_id', $agendamentoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

