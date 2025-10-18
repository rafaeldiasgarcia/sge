document.addEventListener("DOMContentLoaded", () => {
  function wireToggle(btnId, inputId) {
    const toggle = document.getElementById(btnId);
    const input  = document.getElementById(inputId);
    if (!toggle || !input) return; 

    const icon = toggle.querySelector("i");

    toggle.addEventListener("click", () => {
      const isPassword = input.type === "password";
      input.type = isPassword ? "text" : "password";

      if (icon) {
        icon.classList.toggle("bi-eye");
        icon.classList.toggle("bi-eye-slash");
      }

      input.focus();
    });
  }

  wireToggle("togglePassword", "senha");
  wireToggle("toggleConfirmPassword", "confirmar_senha");
});
