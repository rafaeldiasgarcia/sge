<?php
/**
 * Classe de Conexão com o Banco de Dados (Connection)
 * 
 * Implementa o padrão de projeto Singleton para garantir que apenas uma única
 * instância da conexão PDO seja criada durante toda a execução da aplicação.
 * 
 * Benefícios do Singleton neste contexto:
 * - Economia de recursos (evita múltiplas conexões desnecessárias)
 * - Controle centralizado da conexão
 * - Facilita o gerenciamento de transações
 * 
 * As credenciais do banco de dados estão definidas no arquivo docker-compose.yml
 * e são injetadas automaticamente no container quando ele é iniciado.
 * 
 * Configurações importantes:
 * - Charset: UTF8MB4 (suporte completo a caracteres especiais e emojis)
 * - Timezone: America/Sao_Paulo (GMT-3)
 * - Error Mode: Exceptions (facilita debug e tratamento de erros)
 * 
 * @package Application\Core
 */
namespace Application\Core;

use PDO;
use PDOException;

class Connection
{
    /**
     * Armazena a única instância da conexão PDO
     * @var PDO|null
     */
    private static ?PDO $instance = null;

    /**
     * Construtor privado para prevenir criação direta de instâncias
     * 
     * Parte do padrão Singleton - força o uso do método getInstance()
     */
    private function __construct() {}
    
    /**
     * Previne clonagem da instância
     * 
     * Parte do padrão Singleton - garante que não haja cópias da conexão
     */
    private function __clone() {}

    /**
     * Obtém a instância única da conexão com o banco de dados
     * 
     * Se a instância ainda não foi criada, este método:
     * 1. Cria a conexão PDO com as credenciais configuradas
     * 2. Define as opções de conexão (modo de erro, fetch mode, etc)
     * 3. Configura charset UTF8MB4 para suporte completo a caracteres
     * 4. Ajusta o timezone do MySQL para GMT-3 (horário de Brasília)
     * 
     * Em chamadas subsequentes, simplesmente retorna a instância já criada.
     * 
     * @return PDO A instância única da conexão PDO
     * @throws \Exception Se houver erro na conexão com o banco de dados
     */
    public static function getInstance(): PDO
    {
        // Lazy initialization - só cria a conexão quando realmente necessário
        if (self::$instance === null) {
            // Configurações do banco de dados conforme docker-compose.yml
            // Estas credenciais são as padrões definidas no ambiente de desenvolvimento
            $host = 'db';              // Nome do serviço no docker-compose.yml
            $dbname = 'application';   // Nome do banco de dados
            $username = 'appuser';     // Usuário do banco
            $password = 'apppass';     // Senha do usuário
            $charset = 'utf8mb4';      // Charset com suporte completo a Unicode

            // DSN (Data Source Name) - string de conexão
            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

            // Opções de configuração do PDO
            $options = [
                // ERRMODE_EXCEPTION: Lança exceções em caso de erro (melhor para debug)
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                
                // FETCH_ASSOC: Retorna resultados como arrays associativos (mais intuitivo)
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                
                // Desabilita emulação de prepared statements (mais seguro)
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                // Cria a conexão PDO
                self::$instance = new PDO($dsn, $username, $password, $options);

                // Configurar UTF8MB4 para suporte completo a acentos, caracteres especiais e emojis
                // Importante para armazenar nomes com acentuação correta
                self::$instance->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
                self::$instance->exec("SET CHARACTER SET utf8mb4");
                
                // Configurar timezone do MySQL para GMT-3 (Horário de Brasília)
                // Garante que datas e horas sejam armazenadas e recuperadas no timezone correto
                self::$instance->exec("SET time_zone = '-03:00'");

            } catch (PDOException $e) {
                // Em caso de erro, encerra a aplicação e exibe a mensagem de erro
                // Em produção, você pode querer logar o erro em vez de exibi-lo
                die('Erro de conexão com o banco de dados: ' . $e->getMessage());
            }
        }

        // Retorna a instância (seja recém-criada ou já existente)
        return self::$instance;
    }
}