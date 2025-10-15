<?php
/**
 * ============================================================================
 * VIEW PARCIAL: CALENDÁRIO DE AGENDAMENTOS
 * ============================================================================
 * 
 * Renderiza o calendário interativo para seleção de datas e períodos de 
 * agendamento de quadras esportivas.
 * 
 * FUNCIONAMENTO:
 * - Exibe um mês por vez com navegação entre meses
 * - Mostra disponibilidade em tempo real de cada período
 * - Aplica regra de antecedência mínima de 4 dias
 * - Bloqueia datas passadas automaticamente
 * - Dois períodos por dia: P1 (19:15-20:55) e P2 (21:10-22:50)
 * 
 * VARIÁVEIS RECEBIDAS DO CONTROLLER:
 * @var DateTime $inicio       - Primeiro dia do mês sendo exibido
 * @var array    $ocupado      - Períodos ocupados indexados por data ['Y-m-d']['P1'|'P2']
 * @var int      $diasNoMes    - Quantidade de dias no mês
 * @var int      $primeiroW    - Dia da semana do primeiro dia (0=domingo)
 * @var string   $prevMes      - String Y-m do mês anterior
 * @var string   $nextMes      - String Y-m do próximo mês
 * @var bool     $isCampeonato - Se o evento sendo agendado é um campeonato (opcional)
 * 
 * INTEGRAÇÃO:
 * - Usado em: agenda.view.php, editar-evento.view.php
 * - JavaScript: calendar.js (navegação AJAX)
 * - CSS: calendar.css, agenda.css
 * - Controller: AgendaController::calendario()
 * 
 * REGRAS DE NEGÓCIO:
 * - Antecedência mínima: 4 dias
 * - Não permite agendamento em datas passadas
 * - Badge de cor indica disponibilidade:
 *   * Verde: ambos períodos livres
 *   * Amarelo: um período ocupado
 *   * Vermelho: ambos períodos ocupados
 *   * Cinza: data indisponível (passada ou antecedência insuficiente)
 */

// ============================================================================
// FUNÇÕES AUXILIARES DE RENDERIZAÇÃO
// ============================================================================

/**
 * Determina as classes CSS para um slot (botão de período) do calendário
 * 
 * @param bool $busy         - Se o período já está ocupado
 * @param bool $dateInvalid  - Se a data é inválida (passada ou antecedência insuficiente)
 * @param bool $isPastDate   - Se a data já passou
 * @param bool $isCampeonato - Se é um campeonato (opcional)
 * @return string            - Classes CSS do Bootstrap para o botão
 */
function slotClassCal(bool $busy, bool $dateInvalid, bool $isPastDate, bool $isCampeonato = false) { 
    // Para campeonatos: apenas desabilitar datas passadas
    if ($isCampeonato) {
        if ($isPastDate) {
            return 'btn-outline-secondary disabled';
        }
        // Para campeonatos, sempre permitir seleção (mesmo ocupado)
        return 'btn-success';
    }
    
    // Para dias passados ou com antecedência insuficiente, mantém as cores originais mas desabilitados
    if ($dateInvalid) {
        return $busy ? 'btn-outline-secondary disabled' : 'btn-success disabled';
    }
    
    // Para dias válidos, comportamento normal
    return $busy ? 'btn-outline-secondary disabled' : 'btn-success'; 
}

/**
 * Determina a cor do badge de disponibilidade do dia
 * 
 * @param string $ymd                    - Data no formato Y-m-d
 * @param array  $ocupado                - Array de períodos ocupados
 * @param bool   $isPastDate             - Se a data já passou
 * @param bool   $isInsufficientAdvance  - Se falta antecedência mínima
 * @param bool   $isCampeonato           - Se é um campeonato (opcional)
 * @return string                        - Classes CSS do badge
 */
function dayBadgeCal($ymd, $ocupado, $isPastDate, $isInsufficientAdvance, $isCampeonato = false) {
    // Para campeonatos: sempre mostrar como disponível (exceto datas passadas)
    if ($isCampeonato) {
        if ($isPastDate) {
            return 'bg-light border text-dark';
        }
        return 'bg-success';
    }
    
    // Para todos os casos (passados, antecedência insuficiente ou válidos), usa as cores normais
    $p1 = !empty($ocupado[$ymd]['P1']);
    $p2 = !empty($ocupado[$ymd]['P2']);
    
    // Ambos períodos livres
    if (!$p1 && !$p2) return 'bg-success';
    
    // Apenas um período ocupado (XOR lógico)
    if ($p1 xor $p2) return 'bg-warning text-dark';
    
    // Ambos períodos ocupados
    return 'bg-danger';
}

/**
 * Verifica se uma data já passou
 * 
 * @param string $ymd  - Data no formato Y-m-d
 * @return bool        - True se a data é anterior a hoje
 */
function isDateInPast($ymd) {
    $hoje = new \DateTime();
    $dataEvento = new \DateTime($ymd);
    return $dataEvento < $hoje;
}

/**
 * Verifica se uma data tem antecedência insuficiente (menos de 4 dias) ou excede o limite de 1 mês
 * 
 * @param string $ymd          - Data no formato Y-m-d
 * @param bool   $isCampeonato - Se é um campeonato (opcional)
 * @return bool                - True se faltam menos de 4 dias ou excede 1 mês
 */
function hasInsufficientAdvance($ymd, $isCampeonato = false) {
    // Para campeonatos: SEM restrições de antecedência
    if ($isCampeonato) {
        return false;
    }
    
    $hoje = new \DateTime();
    $dataEvento = new \DateTime($ymd);
    $diferencaDias = $hoje->diff($dataEvento)->days;
    
    // Se faltam menos de 4 dias (mas não é data passada)
    if ($dataEvento >= $hoje && $diferencaDias < 4) {
        return true;
    }
    
    // Se excede 1 mês (30 dias) de antecedência
    if ($diferencaDias > 30) {
        return true;
    }
    
    return false;
}
?>

<!-- ========================================================================
     CABEÇALHO DO CALENDÁRIO
     ======================================================================== -->
<h5 class="mb-3 text-primary border-bottom pb-2">
    <i class="bi bi-calendar-check"></i> Selecione a Data e o Período
    <span id="regra-antecedencia">(4 dias a 1 mês de antecedência)</span>
    <span id="regra-campeonato" style="display: none;" class="text-success">
        <i class="bi bi-trophy"></i> Campeonatos: sem restrições de data!
    </span>
</h5>

<!-- Legenda de cores do calendário -->
<div class="d-flex align-items-center gap-3 small mb-2">
    <span><span class="badge bg-success me-1">&nbsp;</span> Livre</span>
    <span><span class="badge bg-warning text-dark me-1">&nbsp;</span> Um período ocupado</span>
    <span><span class="badge bg-danger me-1">&nbsp;</span> Dois periodos Indisponiveis</span>
    <span><span class="badge bg-light border text-dark me-1">&nbsp;</span> Horário indisponível</span>
</div>

<!-- ========================================================================
     CONTAINER PRINCIPAL DO CALENDÁRIO
     Este div é o alvo das atualizações AJAX ao navegar entre meses
     ======================================================================== -->
<div id="cal" class="border rounded-3 p-3 mb-4">
    
    <!-- Navegação de mês (anterior/próximo) -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <button type="button" class="btn btn-sm btn-outline-secondary nav-cal" data-mes="<?= $prevMes; ?>">
            <i class="bi bi-chevron-left"></i>
        </button>

        <!-- Exibição do mês e ano atual -->
        <div class="fw-semibold">
            <?php
            // Array de nomes de meses em português
            $meses = [
                1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
                5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
                9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
            ];
            $mes = (int)$inicio->format('n');
            $ano = $inicio->format('Y');
            echo $meses[$mes] . ' de ' . $ano;
            ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline-secondary nav-cal" data-mes="<?= $nextMes; ?>">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <!-- Cabeçalho com dias da semana -->
    <div class="calendar-grid text-center fw-semibold text-muted mb-2">
        <?php foreach (['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $d): ?><div><?= $d ?></div><?php endforeach; ?>
    </div>

    <!-- Grid principal do calendário -->
    <div class="calendar-grid">
        <?php 
        // Adiciona células vazias antes do primeiro dia do mês
        for ($i=0; $i<$primeiroW; $i++): ?>
            <div class="calendar-cell"></div>
        <?php endfor; ?>
        
        <?php 
        // Verifica se é campeonato (variável opcional)
        $isCampeonato = isset($isCampeonato) ? $isCampeonato : false;
        
        // Loop por cada dia do mês
        for ($dia=1; $dia<=$diasNoMes; $dia++):
            // Formata data no padrão Y-m-d para comparações
            $ymd = $inicio->format('Y-m') . '-' . str_pad($dia, 2, '0', STR_PAD_LEFT);
            
            // Verifica restrições de data
            $isPastDate = isDateInPast($ymd);
            $isInsufficientAdvance = hasInsufficientAdvance($ymd, $isCampeonato);
            $dateInvalid = $isPastDate || $isInsufficientAdvance;
            
            // Determina cor do badge de disponibilidade
            $badge = dayBadgeCal($ymd, $ocupado, $isPastDate, $isInsufficientAdvance, $isCampeonato);
            
            // Verifica se cada período está ocupado
            $p1busy = !empty($ocupado[$ymd]['P1']);
            $p2busy = !empty($ocupado[$ymd]['P2']);
            
            // Para campeonatos: apenas desabilitar datas passadas
            // Para outros: desabilitar períodos ocupados ou datas inválidas
            if ($isCampeonato) {
                $p1disabled = $isPastDate;
                $p2disabled = $isPastDate;
            } else {
                $p1disabled = $p1busy || $dateInvalid;
                $p2disabled = $p2busy || $dateInvalid;
            }
            
            // Adiciona classe CSS especial para datas indisponíveis
            $cellClass = '';
            if ($isPastDate || $isInsufficientAdvance) {
                $cellClass = 'past-date';
            }
            ?>
            <div class="calendar-cell <?= $cellClass ?>">
                <!-- Linha superior: número do dia + badge de disponibilidade -->
                <div class="dayline">
                    <span class="calendar-day"><?= $dia ?></span>
                    <span class="badge <?= $badge ?>">&nbsp;</span>
                </div>
                
                <!-- Botão do Período 1 (19:15 - 20:55) -->
                <button type="button" 
                        class="btn btn-sm slot <?= slotClassCal($p1busy, $dateInvalid, $isPastDate, $isCampeonato) ?>" 
                        data-date="<?= $ymd ?>" 
                        data-periodo="P1" 
                        <?= $p1disabled ? 'disabled' : '' ?>>
                    19:15 - 20:55
                </button>
                
                <!-- Botão do Período 2 (21:10 - 22:50) -->
                <button type="button" 
                        class="btn btn-sm slot <?= slotClassCal($p2busy, $dateInvalid, $isPastDate, $isCampeonato) ?>" 
                        data-date="<?= $ymd ?>" 
                        data-periodo="P2" 
                        <?= $p2disabled ? 'disabled' : '' ?>>
                    21:10 - 22:50
                </button>
            </div>
        <?php endfor; ?>
    </div>
</div>