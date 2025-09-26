<?php
#
# Repositório para a tabela 'modalidades'.
# Contém todas as operações de CRUD para as modalidades esportivas
# disponíveis no sistema.
#
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class ModalidadeRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, nome FROM modalidades ORDER BY nome ASC");
        return $stmt->fetchAll();
    }

    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM modalidades WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create(string $nome): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO modalidades (nome) VALUES (:nome)");
        $stmt->bindValue(':nome', $nome);
        return $stmt->execute();
    }

    public function update(int $id, string $nome): bool
    {
        $stmt = $this->pdo->prepare("UPDATE modalidades SET nome = :nome WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nome', $nome);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM modalidades WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}