<?php
#
# Repositório para a tabela 'cursos'.
# Contém todas as operações de CRUD para os cursos, além de métodos para
# buscar o ID da atlética associada e desvincular cursos de uma atlética.
#
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class CursoRepository
{
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