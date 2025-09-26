<?php
#
# Repositório para a geração de Relatórios.
# Contém queries complexas que agregam dados de múltiplas tabelas para
# fornecer estatísticas e listas detalhadas sobre eventos, usuários e períodos.
#
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class RelatorioRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function getRelatorioGeral(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT 
                    COUNT(a.id) as total_eventos,
                    COUNT(CASE WHEN a.status = 'aprovado' THEN 1 END) as eventos_aprovados,
                    COUNT(CASE WHEN a.status = 'pendente' THEN 1 END) as eventos_pendentes,
                    COUNT(CASE WHEN a.status = 'rejeitado' THEN 1 END) as eventos_rejeitados,
                    COUNT(CASE WHEN a.tipo_agendamento = 'esportivo' THEN 1 END) as eventos_esportivos,
                    COUNT(CASE WHEN a.tipo_agendamento = 'nao_esportivo' THEN 1 END) as eventos_nao_esportivos,
                    COUNT(CASE WHEN a.atletica_confirmada = 1 THEN 1 END) as eventos_com_atletica,
                    SUM(a.quantidade_pessoas) as total_pessoas_estimadas,
                    SUM(a.quantidade_atletica) as total_pessoas_atleticas,
                    (SELECT COUNT(p.id) FROM presencas p JOIN agendamentos ag ON p.agendamento_id = ag.id WHERE ag.data_agendamento BETWEEN :sub_inicio AND :sub_fim) as total_presencas
                FROM agendamentos a 
                WHERE a.data_agendamento BETWEEN :main_inicio AND :main_fim";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':sub_inicio', $dataInicio);
        $stmt->bindValue(':sub_fim', $dataFim);
        $stmt->bindValue(':main_inicio', $dataInicio);
        $stmt->bindValue(':main_fim', $dataFim);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getEventosNoPeriodo(string $dataInicio, string $dataFim): array
    {
        $sql = "SELECT 
                    a.*, 
                    u.nome as responsavel,
                    (SELECT COUNT(p.id) FROM presencas p WHERE p.agendamento_id = a.id) as total_presencas
                FROM agendamentos a
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.data_agendamento BETWEEN :inicio AND :fim
                ORDER BY a.data_agendamento DESC, a.periodo";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':inicio', $dataInicio);
        $stmt->bindValue(':fim', $dataFim);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getDadosEvento(int $eventoId)
    {
        $sql = "SELECT a.*, u.nome as responsavel,
                       (SELECT COUNT(id) FROM presencas WHERE agendamento_id = a.id) as total_presencas
                FROM agendamentos a 
                JOIN usuarios u ON a.usuario_id = u.id 
                WHERE a.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $eventoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getPresencasPorEvento(int $eventoId): array
    {
        $sql = "SELECT u.nome, u.email, u.ra, u.tipo_usuario_detalhado, c.nome as curso_nome
                FROM presencas p
                JOIN usuarios u ON p.usuario_id = u.id
                LEFT JOIN cursos c ON u.curso_id = c.id
                WHERE p.agendamento_id = :id ORDER BY u.nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $eventoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAgendamentosPorUsuario(int $usuarioId): array
    {
        $sql = "SELECT id, titulo, data_agendamento, status FROM agendamentos WHERE usuario_id = :id ORDER BY data_agendamento DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPresencasPorUsuario(int $usuarioId): array
    {
        $sql = "SELECT a.titulo, a.data_agendamento FROM presencas p JOIN agendamentos a ON p.agendamento_id = a.id WHERE p.usuario_id = :id ORDER BY a.data_agendamento DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}