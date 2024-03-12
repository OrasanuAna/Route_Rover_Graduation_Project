// Acest script va asigura că codul se execută după încărcarea completă a documentului
document.addEventListener("DOMContentLoaded", function() {
    // Obține toate elementele care au clasa 'toggle-password'
    var togglePasswordIcons = document.querySelectorAll('.toggle-password');

    // Adaugă un listener de evenimente la fiecare iconiță
    togglePasswordIcons.forEach(function(icon) {
        icon.addEventListener('click', function(event) {
            // Identifică elementul input corespunzător
            var passwordInput = event.target.closest('.input-group').querySelector('input');

            // Schimbă tipul input-ului și pictograma
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                event.target.classList.remove('fa-eye-slash');
                event.target.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                event.target.classList.remove('fa-eye');
                event.target.classList.add('fa-eye-slash');
            }
        });
    });
});
