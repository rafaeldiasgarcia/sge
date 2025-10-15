<?php
/**
 * Controller do Super Administrador (SuperAdminController)
 * 
 * Gerencia todas as funcionalidades administrativas de mais alto nível do sistema.
 * Super Administradores têm acesso completo a todas as funcionalidades e dados.
 * 
 * Áreas de Gerenciamento:
 * 
 * 1. Usuários:
 *    - Criar, editar e excluir usuários
 *    - Alterar perfis (roles)
 *    - Gerenciar coordenadores
 *    - Listar todos os usuários
 * 
 * 2. Estrutura Acadêmica:
 *    - CRUD de Atléticas
 *    - CRUD de Cursos
 *    - Vincular cursos a atléticas
 * 
 * 3. Modalidades Esportivas:
 *    - Criar, editar e excluir modalidades
 *    - Gerenciar lista de esportes disponíveis
 * 
 * 4. Agendamentos:
 *    - Aprovar/rejeitar agendamentos pendentes
 *    - Cancelar agendamentos aprovados
 *    - Alterar data/horário de agendamentos
 *    - Adicionar observações administrativas
 *    - Visualizar detalhes completos
 * 
 * 5. Administradores:
 *    - Promover usuários a admin de atlética
 *    - Rebaixar admins a usuários comuns
 *    - Listar admins por atlética
 * 
 * 6. Relatórios:
 *    - Relatório geral com estatísticas
 *    - Relatório por período
 *    - Relatório por evento
 *    - Relatório por usuário
 *    - Exportação para impressão
 * 
 * 7. Notificações:
 *    - Enviar notificações globais
 *    - Notificar todos os usuários
 * 
 * Todas as ações são protegidas pelo middleware Auth::protectSuperAdmin()
 * 
 * @package Application\Controller
 */
namespace Application\Controller;

use Application\Core\Auth;
use Application\Core\NotificationService;

class SuperAdminController extends BaseController
{
    private $notificationService;

    public function __construct()
    {
        $this->notificationService = new NotificationService();
    }

    public function dashboard()
    {
        Auth::protectSuperAdmin();
        view('super_admin/dashboard', [
            'title' => 'Painel Super Admin',
            'user' => $this->getUserData()
        ]);
    }

    public function gerenciarAgendamentos()
    {
        Auth::protectSuperAdmin();
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $pendentes = $agendamentoRepo->findPendingAgendamentos();
        $aprovados = $agendamentoRepo->findApprovedAgendamentos();
        $rejeitados = $agendamentoRepo->findRejectedAgendamentos();
        view('super_admin/gerenciar-agendamentos', [
            'title' => 'Gerenciar Agendamentos',
            'user' => $this->getUserData(),
            'pendentes' => $pendentes,
            'aprovados' => $aprovados,
            'rejeitados' => $rejeitados
        ]);
    }

    public function aprovarAgendamento()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) redirect('/superadmin/agendamentos');
        
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        
        // Buscar detalhes do agendamento para verificar se é campeonato
        $agendamento = $agendamentoRepo->findById($id);
        if (!$agendamento) {
            $_SESSION['error_message'] = "Agendamento não encontrado.";
            redirect('/superadmin/agendamentos');
        }
        
        $slot = $agendamentoRepo->findSlotById($id);
        $isCampeonato = ($agendamento['tipo_agendamento'] === 'esportivo' && 
                         $agendamento['subtipo_evento'] === 'campeonato');
        
        // Verificar se o slot está ocupado
        if ($slot && $agendamentoRepo->isSlotOccupied($slot['data_agendamento'], $slot['periodo'])) {
            if ($isCampeonato) {
                // Para campeonatos, cancelar eventos conflitantes
                $eventosCancelados = $agendamentoRepo->cancelarEventosConflitantes(
                    $slot['data_agendamento'], 
                    $slot['periodo'], 
                    $id
                );
                
                // Aprovar o campeonato
                $agendamentoRepo->approveAgendamento($id);
                
                // Enviar notificações de cancelamento para usuários afetados
                foreach ($eventosCancelados as $eventoCancelado) {
                    $this->notificationService->notifyEventoCanceladoPorCampeonato(
                        $eventoCancelado['usuario_id'], 
                        $eventoCancelado['titulo'],
                        $slot['data_agendamento'],
                        $slot['periodo']
                    );
                }
                
                // Enviar notificação de aprovação do campeonato
                $this->notificationService->notifyAgendamentoAprovado($id);
                
                $mensagem = "Campeonato aprovado com sucesso!";
                if (!empty($eventosCancelados)) {
                    $mensagem .= " " . count($eventosCancelados) . " evento(s) conflitante(s) foi(ram) cancelado(s) automaticamente.";
                }
                $_SESSION['success_message'] = $mensagem;
            } else {
                $_SESSION['error_message'] = "Falha na Aprovação! Já existe um evento aprovado para esta data e período.";
            }
        } else {
            // Slot livre, aprovar normalmente
            $agendamentoRepo->approveAgendamento($id);
            // Enviar notificação de aprovação
            $this->notificationService->notifyAgendamentoAprovado($id);
            $_SESSION['success_message'] = "Agendamento aprovado com sucesso!";
        }
        
        redirect('/superadmin/agendamentos');
    }

    public function rejeitarAgendamento()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $motivo = trim($_POST['motivo_rejeicao'] ?? '');
        if ($id <= 0 || empty($motivo)) {
            $_SESSION['error_message'] = "O motivo da rejeição é obrigatório.";
            redirect('/superadmin/agendamentos');
        }
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $agendamentoRepo->rejectAgendamento($id, $motivo);
        // Enviar notificação de rejeição
        $this->notificationService->notifyAgendamentoRejeitado($id, $motivo);
        $_SESSION['success_message'] = "Agendamento rejeitado com sucesso.";
        redirect('/superadmin/agendamentos');
    }

    public function cancelarAgendamentoAprovado()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $motivo = trim($_POST['motivo_cancelamento'] ?? '');

        if ($id <= 0 || empty($motivo)) {
            $_SESSION['error_message'] = "O motivo do cancelamento é obrigatório.";
            redirect('/superadmin/agendamentos');
        }

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        if ($agendamentoRepo->cancelarAgendamentoAprovado($id, $motivo)) {
            $this->notificationService->notifyAgendamentoCanceladoAdmin($id, $motivo);
            $_SESSION['success_message'] = "Agendamento cancelado com sucesso.";
        } else {
            $_SESSION['error_message'] = "Erro ao cancelar o agendamento.";
        }
        redirect('/superadmin/agendamentos');
    }

    public function updateAgendamentoAprovado()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $data = [
            'data_agendamento' => $_POST['data_agendamento'] ?? '',
            'periodo' => $_POST['periodo'] ?? '',
            'observacoes_admin' => trim($_POST['observacoes_admin'] ?? '')
        ];

        if ($id <= 0 || empty($data['data_agendamento']) || empty($data['periodo'])) {
            $_SESSION['error_message'] = "Todos os campos são obrigatórios.";
            redirect('/superadmin/agendamentos');
        }

        $agendamentoRepo = $this->repository('AgendamentoRepository');

        // Verifica se o novo horário está disponível (excluindo o próprio evento)
        if ($agendamentoRepo->isSlotOccupied($data['data_agendamento'], $data['periodo'], $id)) {
            // Buscar detalhes do evento que está ocupando o horário
            $eventosNaData = $agendamentoRepo->findByDate($data['data_agendamento']);
            $eventoConflitante = null;

            foreach ($eventosNaData as $evt) {
                if ($evt['periodo'] === $data['periodo'] && $evt['id'] != $id) {
                    $eventoConflitante = $evt;
                    break;
                }
            }

            if ($eventoConflitante) {
                $periodoTexto = $data['periodo'] === 'primeiro' ? '19:15 - 20:55' : '21:10 - 22:50';
                $dataFormatada = date('d/m/Y', strtotime($data['data_agendamento']));
                $_SESSION['error_message'] = "❌ Horário já ocupado! O evento \"{$eventoConflitante['titulo']}\" já está agendado para {$dataFormatada} no período {$periodoTexto}.";
            } else {
                $_SESSION['error_message'] = "Este horário já está ocupado por outro evento.";
            }
            redirect('/superadmin/agendamentos');
        }

        if ($agendamentoRepo->updateAgendamentoAprovado($id, $data)) {
            $this->notificationService->notifyAgendamentoAlterado($id);
            $_SESSION['success_message'] = "Agendamento atualizado com sucesso.";
        } else {
            $_SESSION['error_message'] = "Erro ao atualizar o agendamento.";
        }
        redirect('/superadmin/agendamentos');
    }

    public function gerenciarUsuarios()
    {
        Auth::protectSuperAdmin();
        $userRepo = $this->repository('UsuarioRepository');
        $solicitacaoRepo = $this->repository('SolicitacaoTrocaCursoRepository');
        
        $usuarios = $userRepo->findAllExcept(Auth::id());
        $solicitacoesPendentes = $solicitacaoRepo->findPendentes();
        $solicitacoesProcessadas = $solicitacaoRepo->findProcessadas();
        
        view('super_admin/gerenciar-usuarios', [
            'title' => 'Gerenciar Usuários',
            'user' => $this->getUserData(),
            'usuarios' => $usuarios,
            'solicitacoes_pendentes' => $solicitacoesPendentes,
            'solicitacoes_processadas' => $solicitacoesProcessadas
        ]);
    }

    public function showEditUserForm()
    {
        Auth::protectSuperAdmin();
        $userId = (int)($_GET['id'] ?? 0);
        if ($userId <= 0) redirect('/superadmin/usuarios');
        $userRepo = $this->repository('UsuarioRepository');
        $cursoRepo = $this->repository('CursoRepository');
        $atleticaRepo = $this->repository('AtleticaRepository');
        $user = $userRepo->findById($userId);
        if (!$user) {
            $_SESSION['error_message'] = "Usuário não encontrado.";
            redirect('/superadmin/usuarios');
        }
        view('super_admin/editar-usuario', [
            'title' => 'Editar Usuário',
            'user' => $this->getUserData(),
            'usuario_editado' => $user,
            'cursos' => $cursoRepo->findAll(),
            'atleticas' => $atleticaRepo->findAll()
        ]);
    }

    public function updateUser()
    {
        Auth::protectSuperAdmin();
        $userId = (int)($_POST['id'] ?? 0);
        
        // Limpar telefone removendo formatação (parênteses, traços, espaços)
        $telefone = trim($_POST['telefone'] ?? '');
        if (!empty($telefone)) {
            $telefone = preg_replace('/[^0-9]/', '', $telefone); // Remove tudo que não é número
        } else {
            $telefone = null;
        }
        
        $data = [
            'nome' => trim($_POST['nome'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'ra' => !empty(trim($_POST['ra'] ?? '')) ? trim($_POST['ra']) : null,
            'telefone' => $telefone,
            'role' => $_POST['role'] ?? 'usuario',
            'tipo_usuario_detalhado' => $_POST['tipo_usuario_detalhado'] ?? 'Aluno',
            'curso_id' => !empty($_POST['curso_id']) ? (int)$_POST['curso_id'] : null,
            'is_coordenador' => isset($_POST['is_coordenador']) ? 1 : 0,
            'atletica_join_status' => $_POST['atletica_join_status'] ?? 'none'
        ];

        if ($userId <= 0) redirect('/superadmin/usuarios');

        $userRepo = $this->repository('UsuarioRepository');
        $cursoRepo = $this->repository('CursoRepository');
        $atleticaIdDoCurso = $data['curso_id'] ? $cursoRepo->findAtleticaIdByCursoId($data['curso_id']) : null;

        // Se o status da atlética for "none" ou "pendente", rebaixar de admin automaticamente
        if ($data['atletica_join_status'] === 'none' || $data['atletica_join_status'] === 'pendente') {
            // Se o usuário era admin da atlética, rebaixar para usuário comum
            if ($data['role'] === 'admin') {
                $data['role'] = 'usuario';
            }
            // Sempre resetar para Aluno quando não for membro
            $data['tipo_usuario_detalhado'] = 'Aluno';
            $data['atletica_id'] = null;
        }
        // Lógica para definir atletica_id baseado no role
        elseif ($data['role'] === 'admin') {
            if ($atleticaIdDoCurso) {
                $data['atletica_id'] = $atleticaIdDoCurso;
                // Admin sempre deve ser Membro das Atléticas
                $data['tipo_usuario_detalhado'] = 'Membro das Atléticas';
            } else {
                $_SESSION['error_message'] = "Não é possível promover a Admin. O curso selecionado não pertence a nenhuma atlética.";
                redirect('/superadmin/usuario/editar?id=' . $userId);
            }
        } else {
            // Para usuários normais, atletica_id é definido apenas se for membro aprovado
            if ($data['atletica_join_status'] === 'aprovado' && $atleticaIdDoCurso) {
                $data['atletica_id'] = $atleticaIdDoCurso;
                // Se tornou membro aprovado, automaticamente vira "Membro das Atléticas"
                $data['tipo_usuario_detalhado'] = 'Membro das Atléticas';
            } else {
                $data['atletica_id'] = null;
            }
        }

        $userRepo->updateUserByAdmin($userId, $data);
        $_SESSION['success_message'] = "Usuário atualizado com sucesso!";
        redirect('/superadmin/usuario/editar?id=' . $userId);
    }

    public function deleteUser()
    {
        Auth::protectSuperAdmin();
        $userId = (int)($_POST['id'] ?? 0);
        $confirmationPassword = $_POST['confirmation_password'] ?? '';
        if ($userId <= 0 || empty($confirmationPassword)) {
            $_SESSION['error_message'] = "Requisição inválida.";
            redirect('/superadmin/usuarios');
        }
        if ($userId === Auth::id()) {
            $_SESSION['error_message'] = "Você não pode excluir sua própria conta.";
            redirect('/superadmin/usuario/editar?id=' . $userId);
        }
        $userRepo = $this->repository('UsuarioRepository');
        $adminPasswordHash = $userRepo->findPasswordHashById(Auth::id());
        if (password_verify($confirmationPassword, $adminPasswordHash)) {
            $userRepo->deleteUserById($userId);
            $_SESSION['success_message'] = "Usuário excluído com sucesso!";
            redirect('/superadmin/usuarios');
        } else {
            $_SESSION['error_message'] = "Senha de confirmação incorreta. A exclusão foi cancelada.";
            redirect('/superadmin/usuario/editar?id=' . $userId);
        }
    }

    public function gerenciarEstrutura()
    {
        Auth::protectSuperAdmin();
        $cursoRepo = $this->repository('CursoRepository');
        $atleticaRepo = $this->repository('AtleticaRepository');

        view('super_admin/gerenciar-estrutura', [
            'title' => 'Gerenciar Estrutura Acadêmica',
            'user' => $this->getUserData(),
            'cursos' => $cursoRepo->findAll(),
            'atleticas_disponiveis' => $atleticaRepo->findAll(),
            'todas_atleticas' => $atleticaRepo->findAll()
        ]);
    }

    public function createCurso()
    {
        Auth::protectSuperAdmin();
        $nome = trim($_POST['nome'] ?? '');
        $atleticaId = !empty($_POST['atletica_id']) ? (int)$_POST['atletica_id'] : null;
        if (!empty($nome)) {
            $this->repository('CursoRepository')->create($nome, $atleticaId);
            $_SESSION['success_message'] = "Curso adicionado com sucesso!";
        }
        redirect('/superadmin/estrutura');
    }

    public function showEditCursoForm()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $curso = $this->repository('CursoRepository')->findById($id);
        if (!$curso) redirect('/superadmin/estrutura');
        $atleticas = $this->repository('AtleticaRepository')->findAll();
        view('super_admin/editar-curso', [
            'title' => 'Editar Curso',
            'user' => $this->getUserData(),
            'curso' => $curso,
            'atleticas' => $atleticas
        ]);
    }

    public function updateCurso()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $atleticaId = !empty($_POST['atletica_id']) ? (int)$_POST['atletica_id'] : null;
        if ($id > 0 && !empty($nome)) {
            $this->repository('CursoRepository')->update($id, $nome, $atleticaId);
            $_SESSION['success_message'] = "Curso atualizado com sucesso!";
        }
        redirect('/superadmin/estrutura');
    }

    public function deleteCurso()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->repository('UsuarioRepository')->unlinkCurso($id);
            $this->repository('CursoRepository')->delete($id);
            $_SESSION['success_message'] = "Curso excluído com sucesso!";
        }
        redirect('/superadmin/estrutura');
    }

    public function createAtletica()
    {
        Auth::protectSuperAdmin();
        $nome = trim($_POST['nome'] ?? '');
        if (!empty($nome)) {
            $this->repository('AtleticaRepository')->create($nome);
            $_SESSION['success_message'] = "Atlética adicionada com sucesso!";
        }
        redirect('/superadmin/estrutura');
    }

    public function showEditAtleticaForm()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $atletica = $this->repository('AtleticaRepository')->findById($id);
        if (!$atletica) redirect('/superadmin/estrutura');
        view('super_admin/editar-atletica', [
            'title' => 'Editar Atlética',
            'user' => $this->getUserData(),
            'atletica' => $atletica
        ]);
    }

    public function updateAtletica()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        if ($id > 0 && !empty($nome)) {
            $this->repository('AtleticaRepository')->update($id, $nome);
            $_SESSION['success_message'] = "Atlética atualizada com sucesso!";
        }
        redirect('/superadmin/estrutura');
    }

    public function deleteAtletica()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->repository('CursoRepository')->unlinkAtletica($id);
            $this->repository('AtleticaRepository')->delete($id);
            $_SESSION['success_message'] = "Atlética excluída com sucesso!";
        }
        redirect('/superadmin/estrutura');
    }

    public function gerenciarModalidades()
    {
        Auth::protectSuperAdmin();
        $modalidadeRepo = $this->repository('ModalidadeRepository');
        view('super_admin/gerenciar-modalidades', [
            'title' => 'Gerenciar Modalidades',
            'user' => $this->getUserData(),
            'modalidades' => $modalidadeRepo->findAll()
        ]);
    }

    public function createModalidade()
    {
        Auth::protectSuperAdmin();
        $nome = trim($_POST['nome'] ?? '');
        if (!empty($nome)) {
            $this->repository('ModalidadeRepository')->create($nome);
            $_SESSION['success_message'] = "Modalidade adicionada com sucesso!";
        }
        redirect('/superadmin/modalidades');
    }

    public function showEditModalidadeForm()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_GET['id'] ?? 0);
        $modalidade = $this->repository('ModalidadeRepository')->findById($id);
        if (!$modalidade) redirect('/superadmin/modalidades');
        view('super_admin/editar-modalidade', [
            'title' => 'Editar Modalidade',
            'user' => $this->getUserData(),
            'modalidade' => $modalidade
        ]);
    }

    public function updateModalidade()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        if ($id > 0 && !empty($nome)) {
            $this->repository('ModalidadeRepository')->update($id, $nome);
            $_SESSION['success_message'] = "Modalidade atualizada com sucesso!";
        }
        redirect('/superadmin/modalidades');
    }

    public function deleteModalidade()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->repository('ModalidadeRepository')->delete($id);
            $_SESSION['success_message'] = "Modalidade excluída com sucesso!";
        }
        redirect('/superadmin/modalidades');
    }

    public function gerenciarAdmins()
    {
        Auth::protectSuperAdmin();
        $userRepo = $this->repository('UsuarioRepository');
        view('super_admin/gerenciar-admins', [
            'title' => 'Gerenciar Admins',
            'user' => $this->getUserData(),
            'admins' => $userRepo->findAdmins(),
            'elegiveis' => $userRepo->findEligibleAdmins()
        ]);
    }

    public function promoteAdmin()
    {
        Auth::protectSuperAdmin();
        $userId = (int)($_POST['aluno_id'] ?? 0);
        if ($userId <= 0) {
            redirect('/superadmin/admins');
        }
        $userRepo = $this->repository('UsuarioRepository');
        $cursoRepo = $this->repository('CursoRepository');
        $user = $userRepo->findById($userId);
        if (!$user || !$user['curso_id']) {
            $_SESSION['error_message'] = "Não é possível promover: o usuário não está associado a um curso.";
            redirect('/superadmin/admins');
        }
        $atleticaId = $cursoRepo->findAtleticaIdByCursoId($user['curso_id']);
        if (!$atleticaId) {
            $_SESSION['error_message'] = "Não é possível promover: o curso do usuário não pertence a uma atlética.";
            redirect('/superadmin/admins');
        }
        $success = $userRepo->updateUserRoleAndAtletica($userId, 'admin', $atleticaId);
        if ($success) {
            $_SESSION['success_message'] = "Usuário promovido a Admin com sucesso!";
        } else {
            $_SESSION['error_message'] = "Ocorreu um erro ao promover o usuário.";
        }
        redirect('/superadmin/admins');
    }

    public function demoteAdmin()
    {
        Auth::protectSuperAdmin();
        $userId = (int)($_POST['admin_id'] ?? 0);
        if ($userId > 0) {
            $this->repository('UsuarioRepository')->updateUserRole($userId, 'usuario');
            $_SESSION['success_message'] = "Admin rebaixado para Usuário com sucesso!";
        }
        redirect('/superadmin/admins');
    }

    public function showRelatorios()
    {
        Auth::protectSuperAdmin();
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $userRepo = $this->repository('UsuarioRepository');

        view('super_admin/relatorios', [
            'title' => 'Relatórios',
            'user' => $this->getUserData(),
            'eventos' => $agendamentoRepo->findAllForSelect(),
            'usuarios' => $userRepo->findAllExcept(Auth::id()),
            'dados_relatorio' => null
        ]);
    }

    public function gerarRelatorio()
    {
        Auth::protectSuperAdmin();
        $tipo = $_POST['tipo_relatorio'] ?? '';
        $relatorioRepo = $this->repository('RelatorioRepository');
        $dadosRelatorio = null;

        switch ($tipo) {
            case 'periodo':
                $dataInicio = $_POST['data_inicio'] ?? '';
                $dataFim = $_POST['data_fim'] ?? '';
                if ($dataInicio && $dataFim) {
                    $dadosRelatorio = [
                        'tipo' => 'periodo',
                        'periodo' => ['inicio' => $dataInicio, 'fim' => $dataFim],
                        'estatisticas' => $relatorioRepo->getRelatorioGeral($dataInicio, $dataFim),
                        'eventos_lista' => $relatorioRepo->getEventosNoPeriodo($dataInicio, $dataFim)
                    ];
                }
                break;

            case 'evento_especifico':
                $eventoId = (int)($_POST['evento_id'] ?? 0);
                if ($eventoId > 0) {
                    $eventoData = $relatorioRepo->getDadosEvento($eventoId);
                    if (!empty($eventoData['lista_participantes'])) {
                        $userRepo = $this->repository('UsuarioRepository');
                        $ras = array_filter(array_map('trim', explode("\n", $eventoData['lista_participantes'])));
                        $participantesFormatados = $userRepo->findParticipantesByRAs($ras);
                        $eventoData['participantes_formatados'] = $participantesFormatados;
                    }
                    $dadosRelatorio = [
                        'tipo' => 'evento_especifico',
                        'evento' => $eventoData,
                        'presencas' => $relatorioRepo->getPresencasPorEvento($eventoId)
                    ];
                }
                break;

            case 'usuario':
                $usuarioId = (int)($_POST['usuario_id'] ?? 0);
                if ($usuarioId > 0) {
                    $dadosRelatorio = [
                        'tipo' => 'usuario',
                        'usuario' => $this->repository('UsuarioRepository')->findById($usuarioId),
                        'agendamentos' => $relatorioRepo->getAgendamentosPorUsuario($usuarioId),
                        'presencas' => $relatorioRepo->getPresencasPorUsuario($usuarioId)
                    ];
                }
                break;
        }

        if (!$dadosRelatorio) {
            $_SESSION['error_message'] = "Parâmetros inválidos para gerar o relatório.";
            redirect('/superadmin/relatorios');
        }

        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $userRepo = $this->repository('UsuarioRepository');
        view('super_admin/relatorios', [
            'title' => 'Resultado do Relatório',
            'user' => $this->getUserData(),
            'eventos' => $agendamentoRepo->findAllForSelect(),
            'usuarios' => $userRepo->findAllExcept(Auth::id()),
            'dados_relatorio' => $dadosRelatorio
        ]);
    }

    public function imprimirRelatorio()
    {
        Auth::protectSuperAdmin();
        $tipo = $_POST['tipo_relatorio'] ?? '';
        $relatorioRepo = $this->repository('RelatorioRepository');
        $dadosRelatorio = null;

        switch ($tipo) {
            case 'periodo':
                $dataInicio = $_POST['data_inicio'] ?? '';
                $dataFim = $_POST['data_fim'] ?? '';
                if ($dataInicio && $dataFim) {
                    $dadosRelatorio = [
                        'tipo' => 'periodo',
                        'periodo' => ['inicio' => $dataInicio, 'fim' => $dataFim],
                        'estatisticas' => $relatorioRepo->getRelatorioGeral($dataInicio, $dataFim),
                        'eventos_lista' => $relatorioRepo->getEventosNoPeriodo($dataInicio, $dataFim)
                    ];
                }
                break;

            case 'evento_especifico':
                $eventoId = (int)($_POST['evento_id'] ?? 0);
                if ($eventoId > 0) {
                    $dadosRelatorio = [
                        'tipo' => 'evento_especifico',
                        'evento' => $relatorioRepo->getDadosEvento($eventoId),
                        'presencas' => $relatorioRepo->getPresencasPorEvento($eventoId)
                    ];
                }
                break;

            case 'usuario':
                $usuarioId = (int)($_POST['usuario_id'] ?? 0);
                if ($usuarioId > 0) {
                    $dadosRelatorio = [
                        'tipo' => 'usuario',
                        'usuario' => $this->repository('UsuarioRepository')->findById($usuarioId),
                        'agendamentos' => $relatorioRepo->getAgendamentosPorUsuario($usuarioId),
                        'presencas' => $relatorioRepo->getPresencasPorUsuario($usuarioId)
                    ];
                }
                break;
        }

        if (!$dadosRelatorio) {
            $_SESSION['error_message'] = "Não foi possível gerar a versão para impressão.";
            redirect('/superadmin/relatorios');
        }

        // Para a view de impressão, não usamos o layout padrão.
        extract(['title' => 'Imprimir Relatório', 'dados_relatorio' => $dadosRelatorio]);
        require ROOT_PATH . '/views/super_admin/relatorio-print.view.php';
    }

    public function enviarNotificacaoGlobal()
    {
        Auth::protectSuperAdmin();
        view('super_admin/enviar-notificacao-global', [
            'title' => 'Enviar Notificação Global',
            'user' => $this->getUserData()
        ]);
    }

    public function processarNotificacaoGlobal()
    {
        Auth::protectSuperAdmin();

        $titulo = trim($_POST['titulo'] ?? '');
        $mensagem = trim($_POST['mensagem'] ?? '');

        if (empty($titulo) || empty($mensagem)) {
            $_SESSION['error_message'] = "Título e mensagem são obrigatórios.";
            redirect('/superadmin/notificacao-global');
            return;
        }

        $notificationRepo = $this->repository('NotificationRepository');
        // Tipo não é mais necessário - usa o padrão 'sistema' do repository
        $success = $notificationRepo->createGlobalNotification($titulo, $mensagem);

        if ($success) {
            $_SESSION['success_message'] = "Notificação enviada com sucesso para todos os usuários!";
        } else {
            $_SESSION['error_message'] = "Erro ao enviar a notificação. Tente novamente.";
        }

        redirect('/superadmin/dashboard');
    }

    // ===================================================================
    // GERENCIAMENTO DE SOLICITAÇÕES DE TROCA DE CURSO
    // ===================================================================

    public function aprovarTrocaCurso()
    {
        Auth::protectSuperAdmin();

        try {
            $solicitacaoId = (int)($_POST['solicitacao_id'] ?? 0);

            if ($solicitacaoId <= 0) {
                $_SESSION['error_message'] = "Solicitação inválida.";
                redirect('/superadmin/usuarios');
                return;
            }

            $solicitacaoRepo = $this->repository('SolicitacaoTrocaCursoRepository');
            $userRepo = $this->repository('UsuarioRepository');

            // Buscar dados da solicitação
            $solicitacao = $solicitacaoRepo->findById($solicitacaoId);

            if (!$solicitacao) {
                $_SESSION['error_message'] = "Solicitação não encontrada.";
                redirect('/superadmin/usuarios');
                return;
            }

            // Buscar dados do usuário
            $usuario = $userRepo->findById($solicitacao['usuario_id']);

            if (!$usuario) {
                $_SESSION['error_message'] = "Usuário não encontrado.";
                redirect('/superadmin/usuarios');
                return;
            }

            // REGRA IMPORTANTE: Se o usuário for membro de uma atlética, 
            // ao trocar de curso ele deve voltar a ser aluno padrão
            $eraMembroAtletica = false;
            if ($usuario['atletica_id'] && $usuario['atletica_join_status'] === 'aprovado') {
                $eraMembroAtletica = true;
            }

            // Atualizar curso do usuário
            $updateData = [
                'curso_id' => $solicitacao['curso_novo_id']
            ];

            // Se era membro de atlética, resetar para aluno padrão
            if ($eraMembroAtletica) {
                $updateData['atletica_id'] = null;
                $updateData['atletica_join_status'] = 'none';
                $updateData['tipo_usuario_detalhado'] = 'Aluno';
                
                // Se era admin da atlética, rebaixar para usuário comum
                if ($usuario['role'] === 'admin') {
                    $updateData['role'] = 'usuario';
                }
            }

            // Atualizar perfil do usuário
            $userRepo->updateProfileData($solicitacao['usuario_id'], $updateData);

            // Aprovar a solicitação
            $solicitacaoRepo->aprovar($solicitacaoId, Auth::id());

            // Enviar notificação ao usuário
            $notificationRepo = $this->repository('NotificationRepository');
            $mensagem = "Sua solicitação de troca de curso foi APROVADA! Seu curso foi alterado para: " . $solicitacao['curso_novo_nome'] . ".";
            
            if ($eraMembroAtletica) {
                $mensagem .= " Como você era membro de uma atlética, seu status foi alterado para 'Aluno'. Você pode solicitar entrada na nova atlética do seu curso.";
            }
            
            $notificationRepo->create(
                $solicitacao['usuario_id'],
                'Troca de Curso Aprovada ✅',
                $mensagem,
                'info'
            );

            $_SESSION['success_message'] = "Solicitação aprovada com sucesso! O curso do aluno foi alterado.";
        } catch (\Exception $e) {
            error_log("Erro ao aprovar troca de curso: " . $e->getMessage());
            $_SESSION['error_message'] = "Erro ao processar a solicitação.";
        }

        redirect('/superadmin/usuarios');
    }

    public function recusarTrocaCurso()
    {
        Auth::protectSuperAdmin();

        try {
            $solicitacaoId = (int)($_POST['solicitacao_id'] ?? 0);
            $motivo = trim($_POST['motivo_recusa'] ?? '');

            if ($solicitacaoId <= 0) {
                $_SESSION['error_message'] = "Solicitação inválida.";
                redirect('/superadmin/usuarios');
                return;
            }

            $solicitacaoRepo = $this->repository('SolicitacaoTrocaCursoRepository');

            // Buscar dados da solicitação
            $solicitacao = $solicitacaoRepo->findById($solicitacaoId);

            if (!$solicitacao) {
                $_SESSION['error_message'] = "Solicitação não encontrada.";
                redirect('/superadmin/usuarios');
                return;
            }

            // Recusar a solicitação
            $solicitacaoRepo->recusar($solicitacaoId, Auth::id(), $motivo);

            // Enviar notificação ao usuário
            $notificationRepo = $this->repository('NotificationRepository');
            $mensagem = "Sua solicitação de troca de curso foi RECUSADA.";
            
            if (!empty($motivo)) {
                $mensagem .= " Motivo: " . $motivo;
            }
            
            $notificationRepo->create(
                $solicitacao['usuario_id'],
                'Troca de Curso Recusada ❌',
                $mensagem,
                'aviso'
            );

            $_SESSION['success_message'] = "Solicitação recusada. O aluno foi notificado.";
        } catch (\Exception $e) {
            error_log("Erro ao recusar troca de curso: " . $e->getMessage());
            $_SESSION['error_message'] = "Erro ao processar a solicitação.";
        }

        redirect('/superadmin/usuarios');
    }
}
