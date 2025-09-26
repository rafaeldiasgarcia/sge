<?php
#
# Repositório para a tabela 'atleticas'.
# Contém todas as operações de CRUD (Create, Read, Update, Delete) para as atléticas,
# além de métodos específicos como encontrar atléticas sem vínculo com cursos.
#
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class AtleticaRepository
{
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
}