<?php
/**
 * ============================================================================
 * VIEW: GERENCIAR PARTICIPAÇÕES EM EVENTOS (ADMIN ATLÉTICA)
 * ============================================================================
 * 
 * Permite que administradores de atléticas inscrevam e removam membros em
 * eventos esportivos aprovados pelo sistema.
 * 
 * FUNCIONALIDADES:
 * - Visualizar todos os eventos esportivos disponíveis em accordion
 * - Ver lista de alunos já inscritos em cada evento
 * - Inscrever novos membros da atlética em eventos
 * - Remover inscrições de membros
 * - Filtro automático: exibe apenas membros disponíveis para inscrição
 * 
 * VARIÁVEIS RECEBIDAS:
 * @var array $eventos - Lista de eventos com detalhes e inscrições
 *                       [id, titulo, esporte_tipo, data_agendamento, 
 *                        inscritos[], disponiveis[]]
 * 
 * ESTRUTURA DOS EVENTOS:
 * - inscritos: membros já inscritos no evento (nome, ra, inscricao_id)
 * - disponiveis: membros que podem ser inscritos (id, nome)
 * 
 * REGRAS DE NEGÓCIO:
 * - Apenas membros aprovados da atlética podem ser inscritos
 * - Um membro não pode ser inscrito duas vezes no mesmo evento
 * - Remoção requer confirmação do usuário
 * 
 * AÇÕES DISPONÍVEIS:
 * - POST /admin/atletica/eventos/inscrever - Inscrever membro
 * - POST /admin/atletica/eventos/remover - Remover inscrição
 * 
 * CONTROLLER: AdminAtleticaController::gerenciarEventos()
 */
?>

<h2>Gerenciar Participações em Eventos Esportivos</h2>
<p>Inscreva membros da sua atlética nos eventos esportivos aprovados.</p>

<!-- Mensagens de feedback -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<!-- ========================================================================
     ACCORDION DE EVENTOS
     Cada evento é um item expansível com duas colunas:
     - Esquerda: Alunos já inscritos
     - Direita: Membros disponíveis para inscrição
     ======================================================================== -->
<div class="accordion" id="accordionEventos">
    <?php if (empty($eventos)): ?>
        <!-- Mensagem quando não há eventos disponíveis -->
        <div class="card">
            <div class="card-body text-center py-5">
                <h5 class="text-muted">Nenhum evento esportivo disponível.</h5>
            </div>
        </div>
    <?php else: ?>
        <?php foreach($eventos as $evento): ?>
            <!-- Item do Accordion: Um evento -->
            <div class="accordion-item">
                <!-- Cabeçalho do evento (clicável para expandir) -->
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#collapse-<?php echo $evento['id']; ?>">
                        <strong><?php echo htmlspecialchars($evento['titulo']); ?></strong>
                        <span class="badge bg-primary ms-2">
                            <?php echo htmlspecialchars($evento['esporte_tipo']); ?>
                        </span>
                        <span class="text-muted ms-auto">
                            <?php echo date('d/m/Y', strtotime($evento['data_agendamento'])); ?>
                        </span>
                    </button>
                </h2>
                
                <!-- Conteúdo expansível do evento -->
                <div id="collapse-<?php echo $evento['id']; ?>" 
                     class="accordion-collapse collapse" 
                     data-bs-parent="#accordionEventos">
                    <div class="accordion-body">
                        <div class="row">
                            <!-- COLUNA ESQUERDA: Alunos Inscritos -->
                            <div class="col-md-7">
                                <h5>Alunos Inscritos</h5>
                                <?php if (empty($evento['inscritos'])): ?>
                                    <p class="text-muted">Nenhum aluno inscrito neste evento ainda.</p>
                                <?php else: ?>
                                    <!-- Tabela de alunos inscritos -->
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>RA</th>
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($evento['inscritos'] as $inscrito): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($inscrito['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($inscrito['ra']); ?></td>
                                                <td>
                                                    <!-- Formulário de remoção -->
                                                    <form method="post" 
                                                          action="/admin/atletica/eventos/remover" 
                                                          onsubmit="return confirm('Tem certeza?')">
                                                        <input type="hidden" name="inscricao_id" 
                                                               value="<?php echo $inscrito['inscricao_id']; ?>">
                                                        <input type="hidden" name="evento_id" 
                                                               value="<?php echo $evento['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            Remover
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                            
                            <!-- COLUNA DIREITA: Inscrever Membros -->
                            <div class="col-md-5 border-start">
                                <h5>Inscrever Membros</h5>
                                <?php if (empty($evento['disponiveis'])): ?>
                                    <p class="text-muted">Todos os membros já estão inscritos.</p>
                                <?php else: ?>
                                    <!-- Lista de membros disponíveis para inscrição -->
                                    <?php foreach($evento['disponiveis'] as $aluno): ?>
                                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                            <span><?php echo htmlspecialchars($aluno['nome']); ?></span>
                                            <!-- Formulário de inscrição -->
                                            <form method="post" action="/admin/atletica/eventos/inscrever">
                                                <input type="hidden" name="aluno_id" 
                                                       value="<?php echo $aluno['id']; ?>">
                                                <input type="hidden" name="evento_id" 
                                                       value="<?php echo $evento['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    Inscrever
                                                </button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>