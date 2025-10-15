<?php
/**
 * Repositório de Solicitações de Troca de Curso (SolicitacaoTrocaCursoRepository)
 * 
 * Camada de acesso a dados para a tabela 'solicitacoes_troca_curso'.
 * Gerencia todas as operações relacionadas a solicitações de troca de curso.
 * 
 * Responsabilidades:
 * - Criar novas solicitações de troca de curso
 * - Listar solicitações pendentes, aprovadas e recusadas
 * - Aprovar ou recusar solicitações
 * - Buscar solicitações por usuário
 * - Contar solicitações pendentes
 * 
 * @package Application\Repository
 */
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class SolicitacaoTrocaCursoRepository
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

    /**
     * Cria uma nova solicitação de troca de curso
     * 
     * @param int $usuarioId ID do usuário solicitante
     * @param int|null $cursoAtualId ID do curso atual (pode ser null)
     * @param int $cursoNovoId ID do curso desejado
     * @param string $justificativa Justificativa do pedido
     * @return bool True se criado com sucesso
     */
    public function create(int $usuarioId, ?int $cursoAtualId, int $cursoNovoId, string $justificativa): bool
    {
        $sql = "INSERT INTO solicitacoes_troca_curso 
                (usuario_id, curso_atual_id, curso_novo_id, justificativa, status) 
                VALUES (:usuario_id, :curso_atual_id, :curso_novo_id, :justificativa, 'pendente')";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':curso_atual_id', $cursoAtualId, $cursoAtualId ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':curso_novo_id', $cursoNovoId, PDO::PARAM_INT);
        $stmt->bindValue(':justificativa', $justificativa, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Busca todas as solicitações pendentes com informações completas
     * Inclui nome do usuário, email, telefone, RA, cursos atual e novo
     * 
     * @return array Lista de solicitações pendentes
     */
    public function findPendentes(): array
    {
        $sql = "SELECT 
                    s.id,
                    s.usuario_id,
                    s.curso_atual_id,
                    s.curso_novo_id,
                    s.justificativa,
                    s.data_solicitacao,
                    u.nome as usuario_nome,
                    u.email as usuario_email,
                    u.telefone as usuario_telefone,
                    u.ra as usuario_ra,
                    ca.nome as curso_atual_nome,
                    cn.nome as curso_novo_nome
                FROM solicitacoes_troca_curso s
                INNER JOIN usuarios u ON s.usuario_id = u.id
                LEFT JOIN cursos ca ON s.curso_atual_id = ca.id
                INNER JOIN cursos cn ON s.curso_novo_id = cn.id
                WHERE s.status = 'pendente'
                ORDER BY s.data_solicitacao ASC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Conta quantas solicitações pendentes existem
     * Usado para mostrar notificação ao super admin
     * 
     * @return int Número de solicitações pendentes
     */
    public function countPendentes(): int
    {
        $sql = "SELECT COUNT(*) FROM solicitacoes_troca_curso WHERE status = 'pendente'";
        $stmt = $this->pdo->query($sql);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Busca uma solicitação específica por ID
     * 
     * @param int $id ID da solicitação
     * @return array|false Dados da solicitação ou false se não encontrada
     */
    public function findById(int $id)
    {
        $sql = "SELECT 
                    s.*,
                    u.nome as usuario_nome,
                    u.email as usuario_email,
                    u.ra as usuario_ra,
                    ca.nome as curso_atual_nome,
                    cn.nome as curso_novo_nome
                FROM solicitacoes_troca_curso s
                INNER JOIN usuarios u ON s.usuario_id = u.id
                LEFT JOIN cursos ca ON s.curso_atual_id = ca.id
                INNER JOIN cursos cn ON s.curso_novo_id = cn.id
                WHERE s.id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Busca solicitações de um usuário específico
     * 
     * @param int $usuarioId ID do usuário
     * @param string|null $status Filtrar por status (opcional)
     * @return array Lista de solicitações do usuário
     */
    public function findByUsuario(int $usuarioId, ?string $status = null): array
    {
        $sql = "SELECT 
                    s.*,
                    ca.nome as curso_atual_nome,
                    cn.nome as curso_novo_nome
                FROM solicitacoes_troca_curso s
                LEFT JOIN cursos ca ON s.curso_atual_id = ca.id
                INNER JOIN cursos cn ON s.curso_novo_id = cn.id
                WHERE s.usuario_id = :usuario_id";
        
        if ($status) {
            $sql .= " AND s.status = :status";
        }
        
        $sql .= " ORDER BY s.data_solicitacao DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        
        if ($status) {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Verifica se usuário já tem solicitação pendente
     * 
     * @param int $usuarioId ID do usuário
     * @return bool True se já tem solicitação pendente
     */
    public function hasSolicitacaoPendente(int $usuarioId): bool
    {
        $sql = "SELECT COUNT(*) FROM solicitacoes_troca_curso 
                WHERE usuario_id = :usuario_id AND status = 'pendente'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Aprova uma solicitação de troca de curso
     * 
     * @param int $solicitacaoId ID da solicitação
     * @param int $respondidoPor ID do super admin que aprovou
     * @return bool True se aprovado com sucesso
     */
    public function aprovar(int $solicitacaoId, int $respondidoPor): bool
    {
        $sql = "UPDATE solicitacoes_troca_curso 
                SET status = 'aprovada', 
                    data_resposta = NOW(), 
                    respondido_por = :respondido_por 
                WHERE id = :id AND status = 'pendente'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $solicitacaoId, PDO::PARAM_INT);
        $stmt->bindValue(':respondido_por', $respondidoPor, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    /**
     * Recusa uma solicitação de troca de curso
     * 
     * @param int $solicitacaoId ID da solicitação
     * @param int $respondidoPor ID do super admin que recusou
     * @param string|null $justificativaResposta Justificativa do super admin para a recusa
     * @return bool True se recusado com sucesso
     */
    public function recusar(int $solicitacaoId, int $respondidoPor, string $justificativaResposta = null): bool
    {
        $sql = "UPDATE solicitacoes_troca_curso 
                SET status = 'recusada', 
                    data_resposta = NOW(), 
                    respondido_por = :respondido_por,
                    justificativa_resposta = :justificativa_resposta
                WHERE id = :id AND status = 'pendente'";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $solicitacaoId, PDO::PARAM_INT);
        $stmt->bindValue(':respondido_por', $respondidoPor, PDO::PARAM_INT);
        $stmt->bindValue(':justificativa_resposta', $justificativaResposta, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * Busca todas as solicitações (histórico completo)
     * Usado para relatórios administrativos
     * 
     * @return array Lista completa de solicitações
     */
    public function findAll(): array
    {
        $sql = "SELECT 
                    s.*,
                    u.nome as usuario_nome,
                    u.email as usuario_email,
                    ca.nome as curso_atual_nome,
                    cn.nome as curso_novo_nome,
                    sa.nome as respondido_por_nome
                FROM solicitacoes_troca_curso s
                INNER JOIN usuarios u ON s.usuario_id = u.id
                LEFT JOIN cursos ca ON s.curso_atual_id = ca.id
                INNER JOIN cursos cn ON s.curso_novo_id = cn.id
                LEFT JOIN usuarios sa ON s.respondido_por = sa.id
                ORDER BY s.data_solicitacao DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Busca solicitações já processadas (aprovadas ou recusadas)
     * Inclui informações completas para histórico
     * 
     * @return array Lista de solicitações processadas
     */
    public function findProcessadas(): array
    {
        $sql = "SELECT 
                    s.id,
                    s.usuario_id,
                    s.curso_atual_id,
                    s.curso_novo_id,
                    s.justificativa,
                    s.status,
                    s.data_solicitacao,
                    s.data_resposta,
                    s.justificativa_resposta,
                    u.nome as usuario_nome,
                    u.email as usuario_email,
                    u.telefone as usuario_telefone,
                    u.ra as usuario_ra,
                    ca.nome as curso_atual_nome,
                    cn.nome as curso_novo_nome,
                    sa.nome as respondido_por_nome
                FROM solicitacoes_troca_curso s
                INNER JOIN usuarios u ON s.usuario_id = u.id
                LEFT JOIN cursos ca ON s.curso_atual_id = ca.id
                INNER JOIN cursos cn ON s.curso_novo_id = cn.id
                LEFT JOIN usuarios sa ON s.respondido_por = sa.id
                WHERE s.status IN ('aprovada', 'recusada')
                ORDER BY s.data_resposta DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }
}

