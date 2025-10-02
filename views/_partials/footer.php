<?php
#
# Rodapé Padrão da Aplicação.
# Fecha as tags HTML abertas no header.
#
?>
</main>

<footer class="unifio-footer <?php echo (isset($isAuthPage) && $isAuthPage) ? 'unifio-footer-auth' : 'mt-auto'; ?>">
    <div class="unifio-footer-container">
        <div class="unifio-footer-left">
            <img src="/img/logo-unifio.webp" alt="Logo UNIFIO" class="unifio-logo">
            <div class="unifio-address">
                <p>Rodovia BR 153, Km 338+420m,</p>
                <p>Bairro Água do Cateto, Ourinhos-SP.</p>
                <p>CEP 19909-100</p>
            </div>
        </div>
        <div class="unifio-footer-right">
            <h5>ATENDIMENTO - FALE CONOSCO</h5>
            <div class="unifio-contact-info">
                <div class="unifio-contact-item">
                    <p>email@unifio.edu.br</p>
                    <p>Pró-Reitoria UNIFIO</p>
                </div>
                <div class="unifio-contact-item">
                    <p>(14) 3302-6400</p>
                    <p>Secretaria UNIFIO</p>
                </div>
                <div class="unifio-contact-item">
                    <p>email@unifio.edu.br</p>
                    <p>Coordenação Educação Física</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!isset($isAuthPage) || !$isAuthPage): ?>
<script src="/js/calendar.js"></script>
<?php endif; ?>
</body>
</html>
