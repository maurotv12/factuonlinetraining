// JavaScript específico para la página de login
document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.querySelector('input[name="emailIngreso"]');
    const passwordInput = document.querySelector('input[name="passIngreso"]');
    const submitButton = document.querySelector('button[type="submit"], input[type="submit"]');

    // Solo aplicar validaciones básicas si estamos en login
    if (emailInput && passwordInput && submitButton) {

        // Validación básica de email en tiempo real
        emailInput.addEventListener('input', function () {
            const email = this.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email.length > 0 && !emailRegex.test(email)) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else if (email.length > 0) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });

        // Validación básica de contraseña
        passwordInput.addEventListener('input', function () {
            const password = this.value;

            if (password.length > 0) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            } else {
                this.classList.remove('is-valid', 'is-invalid');
            }
        });

        // Habilitar/deshabilitar botón basado en campos completos
        function actualizarBotonLogin() {
            const emailValido = emailInput.value.trim().length > 0 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim());
            const passwordCompleto = passwordInput.value.length > 0;

            if (emailValido && passwordCompleto) {
                submitButton.disabled = false;
                submitButton.classList.remove('btn-secondary');
                submitButton.classList.add('btn-primary');
            } else {
                submitButton.disabled = true;
                submitButton.classList.remove('btn-primary');
                submitButton.classList.add('btn-secondary');
            }
        }

        emailInput.addEventListener('input', actualizarBotonLogin);
        passwordInput.addEventListener('input', actualizarBotonLogin);

        // Inicializar estado del botón
        actualizarBotonLogin();
    }
});
