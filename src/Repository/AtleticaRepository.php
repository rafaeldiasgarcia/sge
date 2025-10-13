<?php
/**
 * Repositório de Atléticas (AtleticaRepository)
 * 
 * Camada de acesso a dados para a tabela 'atleticas'.
 * Atléticas são organizações estudantis associadas a um ou mais cursos.
 * 
 * Responsabilidades:
 * - CRUD completo de atléticas
 * - Busca de atléticas sem vínculos com cursos (findUnlinked)
 * - Busca de atlética por ID de curso
 * 
 * Relacionamentos:
 * - Uma atlética pode estar vinculada a vários cursos (relação 1:N)
 * - Uma atlética pode ter vários usuários como membros
 * - Uma atlética pode ter vários administradores
 * 
 * @package Application\Repository
 */
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class AtleticaRepository
{
    /** @var PDO Instância da conexão PDO */
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM atleticas ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM atleticas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create(string $nome): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO atleticas (nome) VALUES (:nome)");
        $stmt->bindValue(':nome', $nome);
        return $stmt->execute();
    }

    public function update(int $id, string $nome): bool
    {
        $stmt = $this->pdo->prepare("UPDATE atleticas SET nome = :nome WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM atleticas WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findUnlinked(): array
    {
        $sql = "SELECT a.id, a.nome 
                FROM atleticas a
                LEFT JOIN cursos c ON a.id = c.atletica_id
                WHERE c.atletica_id IS NULL
                ORDER BY a.nome ASC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function findAtleticaByCursoId(int $cursoId)
    {
        $sql = "SELECT a.* FROM atleticas a 
                JOIN cursos c ON a.id = c.atletica_id 
                WHERE c.id = :curso_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':curso_id', $cursoId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}