<?php
/**
 * Template do Rodapé da Aplicação (Footer Partial)
 * 
 * Arquivo incluído automaticamente em todas as views pela função view() do helpers.php.
 * Contém o rodapé do site e o fechamento das tags HTML.
 * 
 * Conteúdo:
 * - Fecha tag </main> aberta no header
 * - Rodapé institucional com logo e informações de contato da UNIFIO
 * - Links e telefones de atendimento
 * - Scripts JavaScript do Bootstrap e da aplicação
 * - Fecha tags </body> e </html>
 * 
 * Scripts Incluídos Condicionalmente:
 * - Bootstrap Bundle 5.3.3 (sempre)
 * - calendar.js (exceto páginas de autenticação)
 * - header.js (exceto páginas de autenticação)
 * - event-popup.js (exceto páginas de autenticação)
 * - notifications.js (apenas para usuários autenticados)
 * 
 * @package Views\Partials
 */
?>
</main>

<footer class="unifio-footer <?php echo (isset($isAuthPage) && $isAuthPage) ? 'unifio-footer-auth' : 'mt-auto'; ?>">
    <div class="unifio-footer-container">
        <div class="unifio-footer-left">
            <img src="/img/logo-unifio-branco.webp" alt="Logo UNIFIO" class="unifio-logo">
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
                    <p><a href="mailto:email@unifio.edu.br" class="footer-link">email@unifio.edu.br</a></p>
                    <p>Pró-Reitoria UNIFIO</p>
                </div>
                <div class="unifio-contact-item">
                    <p><a href="https://wa.me/551433026400" class="footer-link" target="_blank">(14) 3302-6400</a></p>
                    <p>Secretaria UNIFIO</p>
                </div>
                <div class="unifio-contact-item">
                    <p><a href="mailto:email@unifio.edu.br" class="footer-link">email@unifio.edu.br</a></p>
                    <p>Coordenação Educação Física</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (!isset($isAuthPage) || !$isAuthPage): ?>
<script src="/js/calendar.js"></script>
<script src="/js/header.js"></script>
<script src="/js/event-popup.js"></script>
<?php endif; ?>
<?php
use Application\Core\Auth;
if (Auth::check()): ?>
<script src="/js/notifications.js"></script>
<?php endif; ?>
</body>
</html>
