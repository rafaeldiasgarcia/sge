<?php
#
# Rodapé Padrão da Aplicação.
# Fecha as tags HTML abertas no header.
#
?>
<?php if (isset($isAuthPage) && $isAuthPage): ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php else: ?>
</main>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <div class="container">
        <p>&copy; 2024 SGE UNIFIO - Sistema de Gerenciamento de Eventos</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php endif; ?>
