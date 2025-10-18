function cancelarEvento(id, titulo) {
    var nomeEl = document.getElementById('nomeEvento');
    var idEl = document.getElementById('eventoIdCancelamento');
    if (nomeEl) nomeEl.textContent = titulo;
    if (idEl) idEl.value = id;
    var modalEl = document.getElementById('modalCancelamento');
    if (!modalEl) return;
    var modal = new bootstrap.Modal(modalEl);
    modal.show();
}


