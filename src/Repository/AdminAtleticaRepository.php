<?php
#
# Repositório para as operações do Admin de Atlética.
# Contém todas as queries SQL relacionadas às ações que um admin de atlética
# pode realizar, como buscar membros pendentes, aprovar inscrições, etc.
#
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class AdminAtleticaRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function countMembrosPendentes(int $atleticaId): int
    {
        $sql = "SELECT COUNT(u.id) FROM usuarios u JOIN cursos c ON u.curso_id = c.id WHERE c.atletica_id = :id AND u.atletica_join_status = 'pendente'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $atleticaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countAtletasAprovados(int $atleticaId): int
    {
        $sql = "SELECT COUNT(DISTINCT aluno_id) FROM inscricoes_modalidade WHERE atletica_id = :id AND status = 'aprovado'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $atleticaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function findMembrosPendentes(int $atleticaId): array
    {
        $sql = "SELECT u.id, u.nome, 
                       COALESCE(c.nome, 'Curso não definido') as curso_nome
                FROM usuarios u 
                LEFT JOIN cursos c ON u.curso_id = c.id
                WHERE u.atletica_join_status = 'pendente' 
                AND (c.atletica_id = :id OR c.atletica_id IS NULL)
                ORDER BY u.nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $atleticaId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function aprovarMembro(int $alunoId, int $atleticaId): bool
    {
        $sql = "UPDATE usuarios SET tipo_usuario_detalhado = 'Membro das Atléticas', atletica_join_status = 'aprovado', atletica_id = :atletica_id WHERE id = :aluno_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        $stmt->bindValue(':aluno_id', $alunoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function recusarMembro(int $alunoId): bool
    {
        $sql = "UPDATE usuarios SET atletica_join_status = 'none' WHERE id = :aluno_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':aluno_id', $alunoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findInscricoesPendentes(int $atleticaId): array
    {
        $sql = "SELECT i.id, u.nome as aluno_nome, m.nome as modalidade_nome, i.data_inscricao
                FROM inscricoes_modalidade i
                JOIN usuarios u ON i.aluno_id = u.id
                JOIN modalidades m ON i.modalidade_id = m.id
                WHERE i.atletica_id = :id AND i.status = 'pendente'
                ORDER BY i.data_inscricao ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $atleticaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findInscricoesAprovadas(int $atleticaId): array
    {
        $sql = "SELECT i.id, u.nome as aluno_nome, m.nome as modalidade_nome
                FROM inscricoes_modalidade i
                JOIN usuarios u ON i.aluno_id = u.id
                JOIN modalidades m ON i.modalidade_id = m.id
                WHERE i.atletica_id = :id AND i.status = 'aprovado'
                ORDER BY m.nome ASC, u.nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $atleticaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatusInscricao(int $inscricaoId, string $status): bool
    {
        $sql = "UPDATE inscricoes_modalidade SET status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $inscricaoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findAlunosInscritosEmEvento(int $eventoId, int $atleticaId): array
    {
        $sql = "SELECT ie.id as inscricao_id, u.nome, u.ra 
                FROM inscricoes_eventos ie 
                JOIN usuarios u ON ie.aluno_id = u.id 
                WHERE ie.evento_id = :evento_id AND ie.atletica_id = :atletica_id
                ORDER BY u.nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':evento_id', $eventoId, PDO::PARAM_INT);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findMembrosDisponiveisParaEvento(int $eventoId, int $atleticaId): array
    {
        $sql = "SELECT u.id, u.nome, u.ra 
                FROM usuarios u 
                WHERE u.atletica_id = :atletica_id
                AND u.tipo_usuario_detalhado = 'Membro das Atléticas' 
                AND u.id NOT IN (SELECT aluno_id FROM inscricoes_eventos WHERE evento_id = :evento_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        $stmt->bindValue(':evento_id', $eventoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function inscreverAlunoEmEvento(int $alunoId, int $eventoId, int $atleticaId): bool
    {
        $sql = "INSERT INTO inscricoes_eventos (aluno_id, evento_id, atletica_id, status) VALUES (:aluno_id, :evento_id, :atletica_id, 'aprovado')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':aluno_id', $alunoId, PDO::PARAM_INT);
        $stmt->bindValue(':evento_id', $eventoId, PDO::PARAM_INT);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function removerInscricaoDeEvento(int $inscricaoId): bool
    {
        $sql = "DELETE FROM inscricoes_eventos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $inscricaoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findMembrosAtletica(int $atleticaId): array
    {
        $sql = "SELECT u.id, u.nome, u.email, u.ra, u.role, u.tipo_usuario_detalhado, 
                       u.atletica_join_status, c.nome as curso_nome
                FROM usuarios u 
                LEFT JOIN cursos c ON u.curso_id = c.id
                WHERE u.atletica_id = :atletica_id 
                AND u.atletica_join_status = 'aprovado'
                ORDER BY u.role DESC, u.nome ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function promoverMembroAAdmin(int $membroId, int $atleticaId): bool
    {
        $sql = "UPDATE usuarios 
                SET role = 'admin', tipo_usuario_detalhado = 'Membro das Atléticas'
                WHERE id = :id AND atletica_id = :atletica_id AND role = 'usuario'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $membroId, PDO::PARAM_INT);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function rebaixarAdmin(int $adminId): bool
    {
        $sql = "UPDATE usuarios 
                SET role = 'usuario' 
                WHERE id = :id AND role = 'admin'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $adminId, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function removerMembroAtletica(int $membroId): bool
    {
        $sql = "UPDATE usuarios 
                SET atletica_id = NULL, 
                    atletica_join_status = 'none', 
                    role = 'usuario',
                    tipo_usuario_detalhado = 'Aluno'
                WHERE id = :id AND role != 'superadmin'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $membroId, PDO::PARAM_INT);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    public function getAtleticaById(int $atleticaId): ?array
    {
        $sql = "SELECT id, nome FROM atleticas WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $atleticaId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getUsuarioById(int $usuarioId): ?array
    {
        $sql = "SELECT id, nome, email, atletica_id FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch();
        return $result ?: null;
    }
}