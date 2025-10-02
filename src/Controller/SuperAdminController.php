<?php
#
# Controller para o Painel do Super Administrador.
# Centraliza todas as funcionalidades de mais alto nível, como gerenciamento
# de usuários, estrutura acadêmica (cursos, atléticas), modalidades,
# aprovação de agendamentos e geração de relatórios.
#
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
        view('super_admin/dashboard', ['title' => 'Painel Super Admin']);
    }

    public function gerenciarAgendamentos()
    {
        Auth::protectSuperAdmin();
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $pendentes = $agendamentoRepo->findPendingAgendamentos();
        view('super_admin/gerenciar-agendamentos', [
            'title' => 'Gerenciar Agendamentos',
            'pendentes' => $pendentes
        ]);
    }

    public function aprovarAgendamento()
    {
        Auth::protectSuperAdmin();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) redirect('/superadmin/agendamentos');
        $agendamentoRepo = $this->repository('AgendamentoRepository');
        $slot = $agendamentoRepo->findSlotById($id);
        if ($slot && $agendamentoRepo->isSlotOccupied($slot['data_agendamento'], $slot['periodo'])) {
            $_SESSION['error_message'] = "Falha na Aprovação! Já existe um evento aprovado para esta data e período.";
        } else {
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

    public function gerenciarUsuarios()
    {
        Auth::protectSuperAdmin();
        $userRepo = $this->repository('UsuarioRepository');
        $usuarios = $userRepo->findAllExcept(Auth::id());
        view('super_admin/gerenciar-usuarios', [
            'title' => 'Gerenciar Usuários',
            'usuarios' => $usuarios
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
            'user' => $user,
            'cursos' => $cursoRepo->findAll(),
            'atleticas' => $atleticaRepo->findAll()
        ]);
    }

    public function updateUser()
    {
        Auth::protectSuperAdmin();
        $userId = (int)($_POST['id'] ?? 0);
        $data = [
            'nome' => trim($_POST['nome'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'ra' => trim($_POST['ra'] ?? null),
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

        // Lógica para definir atletica_id baseado no role
        if ($data['role'] === 'admin') {
            if ($atleticaIdDoCurso) {
                $data['atletica_id'] = $atleticaIdDoCurso;
            } else {
                $_SESSION['error_message'] = "Não é possível promover a Admin. O curso selecionado não pertence a nenhuma atlética.";
                redirect('/superadmin/usuario/editar?id=' . $userId);
            }
        } else {
            // Para usuários normais, atletica_id é definido apenas se for membro aprovado
            if ($data['atletica_join_status'] === 'aprovado' && $atleticaIdDoCurso) {
                $data['atletica_id'] = $atleticaIdDoCurso;
                // Se tornou membro, automaticamente vira "Membro das Atléticas" se ainda não for
                if ($data['tipo_usuario_detalhado'] === 'Aluno') {
                    $data['tipo_usuario_detalhado'] = 'Membro das Atléticas';
                }
            } else {
                $data['atletica_id'] = null;
            }
        }

        $userRepo->updateUserByAdmin($userId, $data);
        $_SESSION['success_message'] = "Usuário atualizado com sucesso!";
        redirect('/superadmin/usuarios');
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
            'cursos' => $cursoRepo->findAll(),
            'atleticas_disponiveis' => $atleticaRepo->findUnlinked(),
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
}
