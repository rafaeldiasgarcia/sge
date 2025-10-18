document.addEventListener('DOMContentLoaded', function () {
  // ===== Data de nascimento (não futura)
  const dataNascimentoInput = document.getElementById('data_nascimento');
  const hoje = new Date();
  dataNascimentoInput.setAttribute('max', hoje.toISOString().split('T')[0]);
  const validaData = (el) => {
    const sel = new Date(el.value);
    const hoje0 = new Date(); hoje0.setHours(0,0,0,0);
    el.setCustomValidity(sel > hoje0 ? 'A data de nascimento não pode ser uma data futura.' : '');
  };
  dataNascimentoInput.addEventListener('blur', e => validaData(e.target));
  dataNascimentoInput.addEventListener('input', e => validaData(e.target));

  // ===== Campos dinâmicos por tipo
  const tipoUsuarioSelect = document.getElementById('tipo_usuario_detalhado');
  const campoRa = document.getElementById('campo_ra');
  const inputRa = document.getElementById('ra');
  const campoCurso = document.getElementById('campo_curso');
  const emailInput = document.getElementById('email');
  const labelEmail = document.getElementById('label_email');

  function toggleFields() {
    const tipo = tipoUsuarioSelect.value;
    const exige = (tipo === 'Aluno' || tipo === 'Membro das Atléticas' || tipo === 'Professor');

    campoRa.style.display    = exige ? 'block' : 'none';
    inputRa.required         = !!exige;
    campoCurso.style.display = exige ? 'block' : 'none';

    if (exige) {
      labelEmail.textContent = 'E-mail institucional';
      emailInput.placeholder = 'Use seu e-mail @unifio.edu.br';
    } else if (tipo === 'Comunidade Externa') {
      labelEmail.textContent = 'E-mail';
      emailInput.placeholder = 'Seu e-mail';
    } else {
      labelEmail.textContent = 'E-mail';
      emailInput.placeholder = '*******@unifio.edu.br';
    }
  }
  tipoUsuarioSelect.addEventListener('change', toggleFields);
  toggleFields();

  // ===== Máscara de telefone
  const telefoneInput = document.getElementById('telefone');
  telefoneInput.addEventListener('input', function (e) {
    let v = e.target.value.replace(/\D/g, '').slice(0, 11);
    if (v.length === 0) { e.target.value = ''; return; }
    if (v.length <= 10) {
      v = v.replace(/^(\d{0,2})(\d{0,4})(\d{0,4}).*/, function(_, a, b, c){
        let r = ''; if (a) r += '('+a; if (a && a.length===2) r += ') ';
        if (b) r += b; if (c) r += '-'+c; return r;
      });
    } else {
      v = v.replace(/^(\d{0,2})(\d{0,5})(\d{0,4}).*/, function(_, a, b, c){
        let r=''; if (a) r+='('+a; if (a && a.length===2) r+=') ';
        if (b) r+=b; if (c) r+='-'+c; return r;
      });
    }
    e.target.value = v;
  });
  telefoneInput.addEventListener('blur', function (e) {
    const digits = e.target.value.replace(/\D/g, '');
    e.target.setCustomValidity(digits.length && digits.length !== 11 ? 'O telefone deve conter exatamente 11 dígitos.' : '');
  });
});