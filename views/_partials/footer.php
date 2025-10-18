<?php
/**
 * Rodapé institucional.
 * Scripts e fechamento de tags continuam no layout principal.
 */
?>
<footer class="unifio-footer <?php echo (isset($isAuthPage) && $isAuthPage) ? 'unifio-footer-auth' : ''; ?> bg-dark text-white py-4 mt-5">
  <div class="container">
    <div class="row align-items-center justify-content-between g-4">

      <!-- Lado Esquerdo: logo + endereço -->
      <div class="col-12 col-lg-auto">
        <div class="d-flex align-items-center gap-3 flex-column flex-sm-row text-center text-sm-start">
          <img src="/img/logo-unifio-branco.webp" alt="Logo UNIFIO" class="unifio-logo img-fluid">
          <div class="unifio-address small">
            <p class="mb-0">Rodovia BR 153, Km 338+420m,</p>
            <p class="mb-0">Bairro Água do Cateto, Ourinhos-SP.</p>
            <p class="mb-0">CEP 19909-100</p>
          </div>
        </div>
      </div>

      <!-- Lado Direito: contatos -->
      <div class="col-12 col-lg">
        <div class="unifio-footer-right">
          <h5 class="h6 fw-bold mb-3 text-white text-center">ATENDIMENTO - FALE CONOSCO</h5>

          <div class="unifio-contact-info d-flex flex-column flex-md-row gap-4 justify-content-center">
            <div class="unifio-contact-item text-center">
              <p class="mb-0"><a href="mailto:email@unifio.edu.br" class="footer-link">email@unifio.edu.br</a></p>
              <p class="mb-0 text-secondary small">Pró-Reitoria UNIFIO</p>
            </div>

            <div class="unifio-contact-item text-center">
              <p class="mb-0"><a href="https://wa.me/551433026400" class="footer-link" target="_blank" rel="noopener">(14) 3302-6400</a></p>
              <p class="mb-0 text-secondary small">Secretaria UNIFIO</p>
            </div>

            <div class="unifio-contact-item text-center">
              <p class="mb-0"><a href="mailto:email@unifio.edu.br" class="footer-link">email@unifio.edu.br</a></p>
              <p class="mb-0 text-secondary small">Coordenação Educação Física</p>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</footer>