document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('reclamationForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        var sujet = document.getElementById('sujet').value.trim();
        var email = document.getElementById('email').value.trim();
        var description = document.getElementById('description').value.trim();

        if (!sujet) {
            alert('Veuillez renseigner le sujet.');
            document.getElementById('sujet').focus();
            e.preventDefault();
            return false;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            alert('Veuillez renseigner une adresse email valide.');
            document.getElementById('email').focus();
            e.preventDefault();
            return false;
        }

        if (!description) {
            alert('Veuillez renseigner la description de la réclamation.');
            document.getElementById('description').focus();
            e.preventDefault();
            return false;
        }
    });
});
