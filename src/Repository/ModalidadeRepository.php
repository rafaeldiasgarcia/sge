<?php
/**
 * Repositório de Modalidades (ModalidadeRepository)
 * 
 * Camada de acesso a dados para a tabela 'modalidades'.
 * Modalidades são os esportes disponíveis para inscrição no sistema,
 * como Futsal, Vôlei, Basquete, Handebol, etc.
 * 
 * Responsabilidades:
 * - CRUD completo de modalidades esportivas
 * 
 * Relacionamentos:
 * - Uma modalidade pode ter várias inscrições de alunos
 * - Modalidades são usadas para classificar agendamentos esportivos
 * 
 * Exemplos de modalidades:
 * - Futsal
 * - Vôlei
 * - Basquete
 * - Handebol
 * - Futevôlei
 * 
 * @package Application\Repository
 */
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class ModalidadeRepository
{
    /** @var PDO Instância da conexão PDO */
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