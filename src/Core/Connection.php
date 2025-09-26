<?php
#
# Classe de Conexão com o Banco de Dados.
# Utiliza o padrão Singleton para garantir uma única instância da conexão PDO.
# As credenciais são lidas das variáveis de ambiente definidas no docker-compose.yml,
# tornando o código mais seguro e portável.
#
namespace Application\Core;

use PDO;
use PDOException;

class Connection
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // Lê as configurações do banco de dados das variáveis de ambiente do Docker.
            $host = getenv('DB_HOST');
            $dbname = getenv('DB_NAME');
            $username = getenv('DB_USER');
            $password = getenv('DB_PASS');
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                die('Erro de conexão com o banco de dados: ' . $e->getMessage());
            }
        }

        return self::$instance;
    }
}