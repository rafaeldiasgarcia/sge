<?php
#
# Repositório para a tabela 'usuarios'.
# Esta classe é a única que deve interagir diretamente com a tabela 'usuarios'.
# Ela abstrai toda a lógica SQL para criação, busca, atualização e exclusão de usuários,
# além de operações relacionadas como inscrições em modalidades e validação de RAs.
#
namespace Application\Repository;

use Application\Core\Connection;
use PDO;

class UsuarioRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Connection::getInstance();
    }

    public function findByEmail(string $email)
    {
        $sql = "SELECT id, nome, email, senha, role, atletica_id, tipo_usuario_detalhado, curso_id,
                       login_code, login_code_expires
                FROM usuarios 
                WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateLoginCode(int $id, string $code, string $expires): bool
    {
        $sql = "UPDATE usuarios SET login_code = :code, login_code_expires = :expires WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':code', $code, PDO::PARAM_STR);
        $stmt->bindValue(':expires', $expires, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();
        
        if ($result) {
            error_log("updateLoginCode: Código '{$code}' salvo com sucesso para user_id: {$id}, expira em: {$expires}");
        } else {
            error_log("updateLoginCode: Falha ao salvar código para user_id: {$id}");
        }
        
        return $result;
    }

    public function findUserByLoginCode(string $email, string $code)
    {
        // Buscar usuário e verificar código e expiração
        $sql = "SELECT id, nome, email, role, atletica_id, tipo_usuario_detalhado, curso_id, 
                       login_code, login_code_expires
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

        // Debug
        error_log("findUserByLoginCode: Código no banco: '" . ($user['login_code'] ?? 'NULL') . "' (tipo: " . gettype($user['login_code']) . ")");
        error_log("findUserByLoginCode: Código recebido: '{$code}' (tipo: " . gettype($code) . ")");
        error_log("findUserByLoginCode: Expira em: " . ($user['login_code_expires'] ?? 'NULL'));
        error_log("findUserByLoginCode: Hora atual: " . date('Y-m-d H:i:s'));

        // Normaliza os códigos para comparação (remove espaços e garante que são strings)
        $dbCode = trim((string)($user['login_code'] ?? ''));
        $inputCode = trim((string)$code);

        // Verifica se o código existe e não está vazio
        if (empty($dbCode)) {
            error_log("findUserByLoginCode: Código no banco está vazio");
            return null;
        }

        // Verifica se o código expirou
        if (strtotime($user['login_code_expires']) <= time()) {
            error_log("findUserByLoginCode: Código expirado");
            return null;
        }

        // Verifica se o código corresponde
        if ($dbCode === $inputCode) {
            error_log("findUserByLoginCode: Código válido!");
            return $user;
        }

        error_log("findUserByLoginCode: Código não corresponde. DB: '{$dbCode}' vs Input: '{$inputCode}'");
        return null;
    }

    public function clearLoginCode(int $id): bool
    {
        $sql = "UPDATE usuarios SET login_code = NULL, login_code_expires = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Salva o token de redefinição de senha e sua data de expiração no banco.
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
     * Busca um usuário por um token de redefinição válido (não expirado).
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
     * Atualiza a senha do usuário e limpa os campos de token de redefinição.
     */
    public function updatePasswordAndClearToken(int $userId, string $newPasswordHash): bool
    {
        $sql = "UPDATE usuarios SET senha = :senha, reset_token = NULL, reset_token_expires = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':senha', $newPasswordHash);
        $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

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

    public function updateProfileData(int $id, array $data): bool
    {
        // Construir a query dinamicamente baseado nos campos fornecidos
        $fields = [];
        $params = [':id' => $id];
        
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
            $params[':curso_id'] = $data['curso_id'] ?: null;
        }
        
        if (isset($data['telefone'])) {
            $fields[] = "telefone = :telefone";
            $params[':telefone'] = $data['telefone'];
        }
        
        // Se não há campos para atualizar, retorna verdadeiro
        if (empty($fields)) {
            return true;
        }
        
        $sql = "UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        foreach ($params as $key => $value) {
            if ($key === ':id' || $key === ':curso_id') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
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
        $sql = "INSERT INTO usuarios (nome, email, senha, ra, data_nascimento, telefone, tipo_usuario_detalhado, curso_id, role, atletica_join_status, atletica_id) 
                VALUES (:nome, :email, :senha, :ra, :data_nascimento, :telefone, :tipo_usuario_detalhado, :curso_id, :role, :atletica_join_status, :atletica_id)";
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

    public function findParticipantesByRAs(array $ras): array
    {
        if (empty($ras)) {
            return [];
        }
        $placeholders = str_repeat('?,', count($ras) - 1) . '?';
        $sql = "SELECT nome, ra FROM usuarios WHERE ra IN ($placeholders) ORDER BY nome";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ras);
        $participantes = [];
        while ($row = $stmt->fetch()) {
            $participantes[] = $row['nome'] . ' - ' . $row['ra'];
        }
        return $participantes;
    }

    public function findRAsInexistentes(array $ras): array
    {
        if (empty($ras)) {
            return [];
        }
        $placeholders = str_repeat('?,', count($ras) - 1) . '?';
        $sql = "SELECT ra FROM usuarios WHERE ra IN ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($ras);
        $rasEncontrados = [];
        while ($row = $stmt->fetch()) {
            $rasEncontrados[] = $row['ra'];
        }
        return array_diff($ras, $rasEncontrados);
    }
}