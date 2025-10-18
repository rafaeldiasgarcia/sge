// ====== Abas (corrigido: recebe o evento explicitamente)
function openTab(ev, tabName) {
  // esconder todos os conteúdos
  const tabContents = document.querySelectorAll('.profile-tab-content');
  tabContents.forEach(content => content.classList.remove('active'));

  // remover active de todos os botões
  const tabs = document.querySelectorAll('.profile-tab');
  tabs.forEach(tab => tab.classList.remove('active'));

  // mostrar conteúdo selecionado
  const pane = document.getElementById(tabName);
  if (pane) pane.classList.add('active');

  // marcar botão clicado
  if (ev && ev.currentTarget) ev.currentTarget.classList.add('active');
}

// Modais
function openPasswordModal() {
  const modal = new bootstrap.Modal(document.getElementById('modalAlterarSenha'));
  modal.show();
}
function openTrocaCursoModal() {
  const modal = new bootstrap.Modal(document.getElementById('modalTrocarCurso'));
  modal.show();
}

// Validação do formulário de troca de curso
document.getElementById('formTrocarCurso')?.addEventListener('submit', function(e) {
  const justificativa = document.getElementById('justificativa').value.trim();
  const cursoNovoId = document.getElementById('curso_novo_id').value;
  if (!cursoNovoId) { e.preventDefault(); alert('Por favor, selecione o curso desejado.'); return false; }
  if (justificativa.length < 50) {
    e.preventDefault();
    alert('A justificativa deve ter no mínimo 50 caracteres. Você digitou ' + justificativa.length + ' caracteres.');
    return false;
  }
  return confirm('Confirma o envio da solicitação de troca de curso? Você receberá uma resposta do coordenador em breve.');
});

// Ajustar tamanho do nome
function ajustarTamanhoNome() {
  const nomeElement = document.querySelector('.profile-info h2');
  if (!nomeElement) return;
  const containerWidth = nomeElement.parentElement.offsetWidth;
  let fontSize = 28;
  nomeElement.style.fontSize = fontSize + 'px';
  while (nomeElement.scrollWidth > containerWidth && fontSize > 16) {
    fontSize -= 1;
    nomeElement.style.fontSize = fontSize + 'px';
  }
}
document.addEventListener('DOMContentLoaded', ajustarTamanhoNome);
window.addEventListener('resize', ajustarTamanhoNome);