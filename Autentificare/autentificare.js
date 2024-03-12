document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.toggle-password').addEventListener('click', function (e) {
        const target = e.target;
        const input = target.closest('.input-group').querySelector('input');
        if (input.getAttribute("type") === "password") {
            input.setAttribute("type", "text");
            target.classList.add("fa-eye");
            target.classList.remove("fa-eye-slash");
        } else {
            input.setAttribute("type", "password");
            target.classList.remove("fa-eye");
            target.classList.add("fa-eye-slash");
        }
    });
});


