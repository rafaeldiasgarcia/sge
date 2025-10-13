<?php
/**
 * Repositório de Cursos (CursoRepository)
 * 
 * Camada de acesso a dados para a tabela 'cursos'.
 * Cursos são as graduações oferecidas pela instituição e podem estar
 * vinculados a uma atlética.
 * 
 * Responsabilidades:
 * - CRUD completo de cursos
 * - Busca de ID de atlética associada ao curso
 * - Desvinculação de cursos de uma atlética (quando atlética é excluída)
 * 
 * Relacionamentos:
 * - Um curso pertence a no máximo uma atlética (relação N:1)
 * - Um curso pode ter vários usuários matriculados
 * - O campo atletica_id pode ser NULL (cursos sem atlética)
 * 
 * @package Application\Repository
 */
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class CursoRepository
{
    /** @var PDO Instância da conexão PDO */
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function findAll(): array
    {
        $sql = "SELECT c.id, c.nome, a.nome as atletica_nome 
                FROM cursos c 
                LEFT JOIN atleticas a ON c.atletica_id = a.id 
                ORDER BY c.nome ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM cursos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create(string $nome, ?int $atleticaId): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO cursos (nome, atletica_id) VALUES (:nome, :atletica_id)");
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function update(int $id, string $nome, ?int $atleticaId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE cursos SET nome = :nome, atletica_id = :atletica_id WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM cursos WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findAtleticaIdByCursoId(int $cursoId): ?int
    {
        $sql = "SELECT atletica_id FROM cursos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $cursoId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchColumn();
        return $result ? (int)$result : null;
    }

    public function unlinkAtletica(int $atleticaId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE cursos SET atletica_id = NULL WHERE atletica_id = :atletica_id");
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}