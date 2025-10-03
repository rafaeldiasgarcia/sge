<?php
#
# View Parcial do Calendário.
# Este arquivo contém apenas o HTML do calendário. Toda a lógica de busca de dados
# foi movida para o AgendamentoController para ser carregada via AJAX.
# As variáveis ($inicio, $ocupado, etc.) são passadas pelo controller.
#

// Funções auxiliares para renderização do calendário
function slotClassCal(bool $busy, bool $dateInvalid, bool $isPastDate) { 
    // Para dias passados ou com antecedência insuficiente, mantém as cores originais mas desabilitados
    if ($dateInvalid) {
        return $busy ? 'btn-outline-secondary disabled' : 'btn-success disabled';
    }
    
    // Para dias válidos, comportamento normal
    return $busy ? 'btn-outline-secondary disabled' : 'btn-success'; 
}

function dayBadgeCal($ymd, $ocupado, $isPastDate, $isInsufficientAdvance) {
    // Para todos os casos (passados, antecedência insuficiente ou válidos), usa as cores normais
    $p1 = !empty($ocupado[$ymd]['P1']);
    $p2 = !empty($ocupado[$ymd]['P2']);
    if (!$p1 && !$p2) return 'bg-success';
    if ($p1 xor $p2) return 'bg-warning text-dark';
    return 'bg-danger';
}

function isDateInPast($ymd) {
    $hoje = new \DateTime();
    $dataEvento = new \DateTime($ymd);
    return $dataEvento < $hoje;
}

function hasInsufficientAdvance($ymd) {
    $hoje = new \DateTime();
    $dataEvento = new \DateTime($ymd);
    $diferencaDias = $hoje->diff($dataEvento)->days;
    
    // Se faltam menos de 4 dias (mas não é data passada)
    return $dataEvento >= $hoje && $diferencaDias < 4;
}
?>

<h5 class="mb-3 text-primary border-bottom pb-2"><i class="bi bi-calendar-check"></i> Selecione a Data e o Período com 4 dias de antecedência.</h5>

<div class="d-flex align-items-center gap-3 small mb-2">
    <span><span class="badge bg-success me-1">&nbsp;</span> Livre</span>
    <span><span class="badge bg-warning text-dark me-1">&nbsp;</span> Um período ocupado</span>
    <span><span class="badge bg-danger me-1">&nbsp;</span> Dois periodos Indisponiveis</span>
    <span><span class="badge bg-light border text-dark me-1">&nbsp;</span> Horário indisponível</span>
</div>

<div id="cal" class="border rounded-3 p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <button type="button" class="btn btn-sm btn-outline-secondary nav-cal" data-mes="<?= $prevMes; ?>">
            <i class="bi bi-chevron-left"></i>
        </button>

        <div class="fw-semibold">
            <?php
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

    <div class="calendar-grid text-center fw-semibold text-muted mb-2">
        <?php foreach (['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $d): ?><div><?= $d ?></div><?php endforeach; ?>
    </div>

    <div class="calendar-grid">
        <?php for ($i=0; $i<$primeiroW; $i++): ?><div class="calendar-cell"></div><?php endfor; ?>
        <?php for ($dia=1; $dia<=$diasNoMes; $dia++):
            $ymd    = $inicio->format('Y-m') . '-' . str_pad($dia, 2, '0', STR_PAD_LEFT);
            $isPastDate = isDateInPast($ymd);
            $isInsufficientAdvance = hasInsufficientAdvance($ymd);
            $dateInvalid = $isPastDate || $isInsufficientAdvance;
            $badge  = dayBadgeCal($ymd, $ocupado, $isPastDate, $isInsufficientAdvance);
            $p1busy = !empty($ocupado[$ymd]['P1']);
            $p2busy = !empty($ocupado[$ymd]['P2']);
            $p1disabled = $p1busy || $dateInvalid;
            $p2disabled = $p2busy || $dateInvalid;
            
            // Classes CSS para diferentes estados
            $cellClass = '';
            if ($isPastDate || $isInsufficientAdvance) {
                $cellClass = 'past-date';
            }
            ?>
            <div class="calendar-cell <?= $cellClass ?>">
                <div class="dayline">
                    <span class="calendar-day"><?= $dia ?></span>
                    <span class="badge <?= $badge ?>">&nbsp;</span>
                </div>
                <button type="button" class="btn btn-sm slot <?= slotClassCal($p1busy, $dateInvalid, $isPastDate) ?>" data-date="<?= $ymd ?>" data-periodo="P1" <?= $p1disabled?'disabled':'' ?>>19:15 - 20:55</button>
                <button type="button" class="btn btn-sm slot <?= slotClassCal($p2busy, $dateInvalid, $isPastDate) ?>" data-date="<?= $ymd ?>" data-periodo="P2" <?= $p2disabled?'disabled':'' ?>>21:10 - 22:50</button>
            </div>
        <?php endfor; ?>
    </div>
</div>