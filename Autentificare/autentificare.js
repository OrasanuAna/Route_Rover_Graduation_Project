document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.toggle-password').forEach(function(item) {
        item.addEventListener('click', function() {
            var input = document.querySelector(this.getAttribute("toggle"));
            if (input.getAttribute("type") == "password") {
                input.setAttribute("type", "text");
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            } else {
                input.setAttribute("type", "password");
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            }
        });
    });
});

// Verifică tema stocată în localStorage și aplică tema corespunzătoare
window.addEventListener('DOMContentLoaded', () => {
    const storedTheme = localStorage.getItem('theme') || 'light';
    if (storedTheme === 'dark') {
        document.body.classList.add('dark-mode');
    }
});


