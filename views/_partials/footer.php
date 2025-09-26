<?php
#
# Rodapé Padrão da Aplicação.
# Contém o fechamento do HTML, links para scripts JS e a lógica de notificações via AJAX.
# É incluído em todas as páginas pela função view().
#
?>
</main>
<footer class="bg-dark text-white mt-auto">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-5 col-md-12 mb-4 mb-md-0"><h5 class="text-uppercase mb-4">SGE UNIFIO</h5><p>Rodovia BR 153, Km 338+420m,<br>Bairro Água do Cateto, Ourinhos-SP.<br>CEP 19909-100</p></div>
            <div class="col-lg-7 col-md-12 mb-4 mb-md-0"><h5 class="text-uppercase mb-4">Atendimento - Fale Conosco</h5><div class="row"><div class="col-md-4"><strong>Pró-Reitoria UNIFIO</strong><br><a href="mailto:email@unifio.edu.br" class="text-white">email@unifio.edu.br</a></div><div class="col-md-4"><strong>Secretaria UNIFIO</strong><br>(14) 3302-6400</div><div class="col-md-4"><strong>Coordenação Ed. Física</strong><br><a href="mailto:email@unifio.edu.br" class="text-white">email@unifio.edu.br</a></div></div></div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">© <?php echo date("Y"); ?> SGE - Sistema de Gerenciamento de Eventos</div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Caminho para o JS agora é direto na raiz de public -->
<script src="/js/calendar.js"></script>

<?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const badge = document.getElementById('notificationBadge');
            const list = document.getElementById('notificationsList');
            const markAllBtn = document.getElementById('markAllReadBtn');

            const fetchNotifications = async () => {
                try {
                    // Caminho da API ajustado para a raiz do domínio.
                    const response = await fetch('/notifications');
                    const data = await response.json();
                    if (data.success) updateUI(data.notifications, data.unreadCount);
                } catch (error) { console.error('Error fetching notifications:', error); }
            };

            const updateUI = (notifications, unreadCount) => {
                badge.style.display = unreadCount > 0 ? 'block' : 'none';
                badge.textContent = unreadCount;
                list.innerHTML = notifications.length === 0
                    ? '<div class="dropdown-item text-muted text-center">Nenhuma notificação</div>'
                    : notifications.map(n => `
                <div class="dropdown-item ${!n.lida ? 'bg-light' : ''}" data-id="${n.id}">
                    <h6 class="mb-1 ${!n.lida ? 'fw-bold' : ''}">${n.titulo}</h6>
                    <p class="mb-1 small text-muted">${n.mensagem || ''}</p>
                    <small class="text-muted">${new Date(n.data_criacao).toLocaleString('pt-BR')}</small>
                </div>`).join('');
            };

            const markAsRead = async (id = null) => {
                const body = id ? { notification_id: id } : {};
                // Caminho da API ajustado para a raiz do domínio.
                await fetch('/notifications/read', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                fetchNotifications();
            };

            list.addEventListener('click', e => {
                const item = e.target.closest('.dropdown-item');
                if (item && item.dataset.id) markAsRead(item.dataset.id);
            });

            markAllBtn.addEventListener('click', () => markAsRead());

            fetchNotifications();
            setInterval(fetchNotifications, 60000);
        });
    </script>
<?php endif; ?>

</body>
</html>