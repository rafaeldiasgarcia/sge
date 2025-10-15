<?php
/**
 * Repositório de Usuários (UsuarioRepository)
 * 
 * Camada de acesso a dados para a tabela 'usuarios'.
 * Esta classe implementa o padrão Repository, abstraindo toda a lógica SQL
 * e fornecendo uma interface limpa para operações de banco de dados.
 * 
 * Responsabilidades:
 * - CRUD completo de usuários (Create, Read, Update, Delete)
 * - Gerenciamento de autenticação (códigos de login, tokens de recuperação)
 * - Gerenciamento de perfis e permissões (roles)
 * - Operações de atlética (associar, desassociar, aprovar membros)
 * - Gerenciamento de inscrições em modalidades esportivas
 * - Validação de RAs (Registro Acadêmico)
 * - Busca e filtragem de usuários por diversos critérios
 * 
 * A classe segue o princípio de responsabilidade única, sendo a ÚNICA
 * camada que deve interagir diretamente com a tabela 'usuarios'.
 * 
 * @package Application\Repository
 */
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class UsuarioRepository
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
     * Busca um usuário pelo endereço de e-mail
     * 
     * Usado principalmente no processo de login para verificar se o e-mail existe
     * e obter as informações necessárias para autenticação.
     * 
     * @param string $email O endereço de e-mail do usuário
     * @return array|false Array com dados do usuário ou false se não encontrado
     */
    public function findByEmail(string $email)
    {
        $sql = "SELECT id, nome, email, senha, role, atletica_id, tipo_usuario_detalhado, curso_id,
                       is_coordenador, login_code, login_code_expires
                FROM usuarios 
                WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Atualiza o código de verificação 2FA para login
     * 
     * Armazena um código de 6 dígitos e sua data de expiração no banco.
     * Este código é enviado por e-mail e tem validade de 15 minutos.
     * 
     * @param int $id ID do usuário
     * @param string $code Código de 6 dígitos gerado
     * @param string $expires Data/hora de expiração no formato 'Y-m-d H:i:s'
     * @return bool True se atualizado com sucesso
     */
    public function updateLoginCode(int $id, string $code, string $expires): bool
    {
        $sql = "UPDATE usuarios SET login_code = :code, login_code_expires = :expires WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code, PDO::PARAM_STR);
        $stmt->bindValue(':expires', $expires, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        
        // Log para debug (útil em desenvolvimento)
        if ($result) {
            error_log("updateLoginCode: Código '{$code}' salvo com sucesso para user_id: {$id}, expira em: {$expires}");
        } else {
            error_log("updateLoginCode: Falha ao salvar código para user_id: {$id}");
        }
        
        return $result;
    }

    /**
     * Valida o código de verificação 2FA e retorna o usuário se válido
     * 
     * Este método realiza validação completa:
     * 1. Verifica se o e-mail existe
     * 2. Verifica se existe um código armazenado
     * 3. Verifica se o código não expirou
     * 4. Compara o código informado com o armazenado
     * 
     * Inclui logs detalhados para facilitar debug do processo de autenticação.
     * 
     * @param string $email E-mail do usuário
     * @param string $code Código de 6 dígitos informado pelo usuário
     * @return array|null Dados do usuário se código válido, null caso contrário
     */
    public function findUserByLoginCode(string $email, string $code)
    {
        // Buscar usuário pelo e-mail
        $sql = "SELECT id, nome, email, role, atletica_id, tipo_usuario_detalhado, curso_id, 
                       is_coordenador, login_code, login_code_expires
                FROM usuarios 
                WHERE email = :email";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        // Se não encontrou o usuário, retorna null
        if (!$user) {
            error_log("findUserByLoginCode: Usuário não encontrado para email: {$email}");
            return null;
        }

        // Logs de debug para rastrear problemas de autenticação
        error_log("findUserByLoginCode: Código no banco: '" . ($user['login_code'] ?? 'NULL') . "' (tipo: " . gettype($user['login_code']) . ")");
        error_log("findUserByLoginCode: Código recebido: '{$code}' (tipo: " . gettype($code) . ")");
        error_log("findUserByLoginCode: Expira em: " . ($user['login_code_expires'] ?? 'NULL'));
        error_log("findUserByLoginCode: Hora atual: " . date('Y-m-d H:i:s'));

        // Normaliza os códigos para comparação (remove espaços e garante que são strings)
        $dbCode = trim((string)($user['login_code'] ?? ''));
        $inputCode = trim((string)$code);

        // Verifica se existe código armazenado
        if (empty($dbCode)) {
            error_log("findUserByLoginCode: Código no banco está vazio");
            return null;
        }

        // Verifica se o código expirou (compara timestamp da expiração com hora atual)
        if (strtotime($user['login_code_expires']) <= time()) {
            error_log("findUserByLoginCode: Código expirado");
            return null;
        }

        // Compara os códigos (comparação case-sensitive)
        if ($dbCode === $inputCode) {
            error_log("findUserByLoginCode: Código válido!");
            return $user;
        }

        // Código não corresponde
        error_log("findUserByLoginCode: Código não corresponde. DB: '{$dbCode}' vs Input: '{$inputCode}'");
        return null;
    }

    /**
     * Limpa o código de verificação após uso bem-sucedido
     * 
     * Remove o código e sua expiração do banco após o login ser concluído,
     * garantindo que o código não possa ser reutilizado.
     * 
     * @param int $id ID do usuário
     * @return bool True se limpado com sucesso
     */
    public function clearLoginCode(int $id): bool
    {
        $sql = "UPDATE usuarios SET login_code = NULL, login_code_expires = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ========== MÉTODOS DE RECUPERAÇÃO DE SENHA ==========

    /**
     * Armazena o token de redefinição de senha e sua data de expiração
     * 
     * O token é gerado com bin2hex(random_bytes(32)) e tem validade de 1 hora.
     * É enviado por e-mail como parte do link de recuperação.
     * 
     * @param int $userId ID do usuário
     * @param string $token Token único de 64 caracteres hexadecimais
     * @param string $expires Data/hora de expiração no formato 'Y-m-d H:i:s'
     * @return bool True se salvo com sucesso
     */
    public function updateResetToken(int $userId, string $token, string $expires): bool
    {
        $sql = "UPDATE usuarios SET reset_token = :token, reset_token_expires = :expires WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':token', $token);
        $stmt->bindValue(':expires', $expires);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Busca um usuário por um token de redefinição válido (não expirado)
     * 
     * Valida automaticamente se o token ainda está dentro do prazo de validade
     * usando a comparação com NOW() diretamente no SQL.
     * 
     * @param string $token Token enviado na URL de redefinição
     * @return array|false Dados do usuário se token válido, false caso contrário
     */
    public function findUserByResetToken(string $token)
    {
        $sql = "SELECT * FROM usuarios WHERE reset_token = :token AND reset_token_expires > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':token', $token);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Atualiza a senha e limpa o token de redefinição
     * 
     * Operação atômica que:
     * 1. Define a nova senha (já deve vir hash ada)
     * 2. Remove o token de redefinição
     * 3. Remove a data de expiração do token
     * 
     * @param int $userId ID do usuário
     * @param string $newPasswordHash Hash da nova senha (gerado com password_hash)
     * @return bool True se atualizado com sucesso
     */
    public function updatePasswordAndClearToken(int $userId, string $newPasswordHash): bool
    {
        $sql = "UPDATE usuarios SET senha = :senha, reset_token = NULL, reset_token_expires = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':senha', $newPasswordHash);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ========== MÉTODOS DE BUSCA E PERFIL ==========

    /**
     * Busca um usuário por ID com informações da atlética associada
     * 
     * Faz JOINs com as tabelas 'cursos' e 'atleticas' para trazer informações
     * completas do usuário incluindo o nome da atlética do seu curso.
     * 
     * @param int $id ID do usuário
     * @return array|false Dados completos do usuário ou false se não encontrado
     */
    public function findById(int $id)
    {
        $sql = "SELECT u.*, c.atletica_id, a.nome as atletica_nome 
                FROM usuarios u 
                LEFT JOIN cursos c ON u.curso_id = c.id 
                LEFT JOIN atleticas a ON c.atletica_id = a.id 
                WHERE u.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Atualiza os dados do perfil do usuário de forma dinâmica
     * 
     * Constrói a query SQL dinamicamente baseado nos campos fornecidos no array $data.
     * Isso permite atualizar apenas os campos necessários sem precisar enviar todos.
     * 
     * Campos suportados:
     * - nome: Nome completo do usuário
     * - email: E-mail (deve ser único no sistema)
     * - data_nascimento: Data no formato 'Y-m-d'
     * - curso_id: ID do curso (pode ser null)
     * - telefone: Telefone com 11 dígitos
     * 
     * @param int $id ID do usuário
     * @param array $data Array associativo com os campos a serem atualizados
     * @return bool True se atualizado com sucesso (ou se não houver campos para atualizar)
     */
    public function updateProfileData(int $id, array $data): bool
    {
        // Construir a query dinamicamente baseado nos campos fornecidos
        $fields = [];
        $params = [':id' => $id];
        
        // Adiciona cada campo presente no array $data à query
        if (isset($data['nome'])) {
            $fields[] = "nome = :nome";
            $params[':nome'] = $data['nome'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        
        if (isset($data['data_nascimento'])) {
            $fields[] = "data_nascimento = :data_nascimento";
            $params[':data_nascimento'] = $data['data_nascimento'];
        }
        
        if (isset($data['curso_id'])) {
            $fields[] = "curso_id = :curso_id";
            // Converte valor vazio para null
            $params[':curso_id'] = $data['curso_id'] ?: null;
        }
        
        if (isset($data['telefone'])) {
            $fields[] = "telefone = :telefone";
            $params[':telefone'] = $data['telefone'];
        }
        
        if (isset($data['atletica_id'])) {
            $fields[] = "atletica_id = :atletica_id";
            $params[':atletica_id'] = $data['atletica_id'];
        }
        
        if (isset($data['atletica_join_status'])) {
            $fields[] = "atletica_join_status = :atletica_join_status";
            $params[':atletica_join_status'] = $data['atletica_join_status'];
        }
        
        if (isset($data['tipo_usuario_detalhado'])) {
            $fields[] = "tipo_usuario_detalhado = :tipo_usuario_detalhado";
            $params[':tipo_usuario_detalhado'] = $data['tipo_usuario_detalhado'];
        }
        
        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params[':role'] = $data['role'];
        }
        
        // Se não há campos para atualizar, retorna sucesso
        if (empty($fields)) {
            return true;
        }
        
        // Monta a query completa
        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        // Bind dos parâmetros com os tipos corretos
        foreach ($params as $key => $value) {
            if ($key === ':id' || $key === ':curso_id' || $key === ':atletica_id') {
                // IDs são inteiros
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                // Demais campos são strings
                $stmt->bindValue($key, $value);
            }
        }
        
        return $stmt->execute();
    }

    public function findPasswordHashById(int $id)
    {
        $sql = "SELECT senha FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $sql = "UPDATE usuarios SET senha = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':password', $newPassword);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateAtleticaJoinStatus(int $userId, string $status): bool
    {
        $sql = "UPDATE usuarios SET atletica_join_status = :status WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function sairDaAtletica(int $userId): bool
    {
        $sql = "UPDATE usuarios 
                SET atletica_join_status = :status, 
                    atletica_id = :atletica_id,
                    tipo_usuario_detalhado = :tipo_usuario
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', 'none', PDO::PARAM_STR);
        $stmt->bindValue(':atletica_id', null, PDO::PARAM_NULL);
        $stmt->bindValue(':tipo_usuario', 'Aluno', PDO::PARAM_STR);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
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

    public function createUser(array $data)
    {
        $sql = "INSERT INTO usuarios (nome, email, senha, ra, data_nascimento, telefone, tipo_usuario_detalhado, curso_id, role, atletica_join_status, atletica_id, is_coordenador) 
                VALUES (:nome, :email, :senha, :ra, :data_nascimento, :telefone, :tipo_usuario_detalhado, :curso_id, :role, :atletica_join_status, :atletica_id, :is_coordenador)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':senha', $data['senha']);
        $stmt->bindValue(':ra', $data['ra']);
        $stmt->bindValue(':data_nascimento', $data['data_nascimento']);
        $stmt->bindValue(':telefone', $data['telefone'] ?? null);
        $stmt->bindValue(':tipo_usuario_detalhado', $data['tipo_usuario_detalhado']);
        $stmt->bindValue(':curso_id', $data['curso_id'], PDO::PARAM_INT);
        $stmt->bindValue(':role', $data['role']);
        $stmt->bindValue(':atletica_join_status', $data['atletica_join_status']);
        $stmt->bindValue(':atletica_id', $data['atletica_id'], PDO::PARAM_INT);
        $stmt->bindValue(':is_coordenador', $data['is_coordenador'] ?? 0, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    public function findAllExcept(int $userIdToExclude): array
    {
        $sql = "SELECT id, nome, email, role, tipo_usuario_detalhado, is_coordenador 
                FROM usuarios 
                WHERE id != :user_id 
                ORDER BY nome ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userIdToExclude, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateUserByAdmin(int $id, array $data): bool
    {
        $sql = "UPDATE usuarios 
                SET nome = :nome, email = :email, ra = :ra, telefone = :telefone, role = :role, 
                    tipo_usuario_detalhado = :tipo_usuario_detalhado, curso_id = :curso_id, 
                    atletica_id = :atletica_id, is_coordenador = :is_coordenador,
                    atletica_join_status = :atletica_join_status
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nome', $data['nome']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':ra', $data['ra']);
        $stmt->bindValue(':telefone', $data['telefone'] ?? null);
        $stmt->bindValue(':role', $data['role']);
        $stmt->bindValue(':tipo_usuario_detalhado', $data['tipo_usuario_detalhado']);
        $stmt->bindValue(':curso_id', $data['curso_id'], PDO::PARAM_INT);
        $stmt->bindValue(':atletica_id', $data['atletica_id'], PDO::PARAM_INT);
        $stmt->bindValue(':is_coordenador', $data['is_coordenador'], PDO::PARAM_INT);
        $stmt->bindValue(':atletica_join_status', $data['atletica_join_status'] ?? 'none');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteUserById(int $id): bool
    {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function unlinkCurso(int $cursoId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET curso_id = NULL WHERE curso_id = :curso_id");
        $stmt->bindValue(':curso_id', $cursoId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findAdmins(): array
    {
        $sql = "SELECT u.id, u.nome, a.nome as atletica_nome 
                FROM usuarios u 
                JOIN atleticas a ON u.atletica_id = a.id 
                WHERE u.role = 'admin' 
                ORDER BY u.nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function findSuperAdmins(): array
    {
        $sql = "SELECT id, nome, email 
                FROM usuarios 
                WHERE role = 'superadmin' 
                ORDER BY nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function findEligibleAdmins(): array
    {
        $sql = "SELECT u.id, u.nome, a.nome as atletica_nome 
                FROM usuarios u 
                JOIN cursos c ON u.curso_id = c.id
                JOIN atleticas a ON c.atletica_id = a.id 
                WHERE u.role = 'usuario' 
                  AND u.tipo_usuario_detalhado = 'Membro das Atléticas'
                  AND u.curso_id IS NOT NULL
                  AND c.atletica_id IS NOT NULL
                ORDER BY u.nome";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function updateUserRole(int $userId, string $role): bool
    {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET role = :role WHERE id = :id");
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateUserRoleAndAtletica(int $userId, string $role, int $atleticaId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE usuarios SET role = :role, atletica_id = :atletica_id WHERE id = :id");
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findInscricoesByUserId(int $userId): array
    {
        $sql = "SELECT i.id, m.nome as modalidade_nome, i.status, i.data_inscricao
                FROM inscricoes_modalidade i
                JOIN modalidades m ON i.modalidade_id = m.id
                WHERE i.aluno_id = :user_id
                ORDER BY m.nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createInscricaoModalidade(int $userId, int $modalidadeId, int $atleticaId): bool
    {
        $sql = "INSERT INTO inscricoes_modalidade (aluno_id, modalidade_id, atletica_id, status)
                VALUES (:user_id, :modalidade_id, :atletica_id, 'pendente')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':modalidade_id', $modalidadeId, PDO::PARAM_INT);
        $stmt->bindValue(':atletica_id', $atleticaId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteInscricaoModalidade(int $inscricaoId, int $userId): bool
    {
        $sql = "DELETE FROM inscricoes_modalidade WHERE id = :id AND aluno_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $inscricaoId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ========== MÉTODOS DE VALIDAÇÃO DE RAs ==========

    /**
     * Busca participantes por uma lista de RAs
     * 
     * Usado para exibir a lista de participantes confirmados de um evento.
     * Retorna um array formatado com "Nome - RA" para cada participante encontrado.
     * 
     * @param array $ras Array de RAs (Registro Acadêmico) para buscar
     * @return array Array de strings no formato "Nome - RA"
     */
    public function findParticipantesByRAs(array $ras): array
    {
        if (empty($ras)) {
            return [];
        }
        
        // Cria placeholders dinâmicos para a cláusula IN
        // Exemplo: para 3 RAs, gera "?,?,?"
        $placeholders = str_repeat('?,', count($ras) - 1) . '?';
        $sql = "SELECT nome, ra FROM usuarios WHERE ra IN ($placeholders) ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ras);
        
        // Formata o resultado como "Nome - RA"
        $participantes = [];
        while ($row = $stmt->fetch()) {
            $participantes[] = $row['nome'] . ' - ' . $row['ra'];
        }
        return $participantes;
    }

    /**
     * Identifica RAs que não existem no sistema
     * 
     * Compara uma lista de RAs fornecida com os RAs existentes no banco,
     * retornando aqueles que não foram encontrados. Útil para validação
     * de listas de participantes antes de criar um evento.
     * 
     * @param array $ras Array de RAs para validar
     * @return array Array com os RAs que não existem no sistema
     */
    public function findRAsInexistentes(array $ras): array
    {
        if (empty($ras)) {
            return [];
        }
        
        // Busca quais RAs existem no banco
        $placeholders = str_repeat('?,', count($ras) - 1) . '?';
        $sql = "SELECT ra FROM usuarios WHERE ra IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ras);
        
        // Coleta os RAs encontrados
        $rasEncontrados = [];
        while ($row = $stmt->fetch()) {
            $rasEncontrados[] = $row['ra'];
        }
        
        // Retorna a diferença (RAs que não foram encontrados)
        return array_diff($ras, $rasEncontrados);
    }
}