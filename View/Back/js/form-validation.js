document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('responseForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        var response = document.getElementById('response').value.trim();
        if (!response) {
            alert('La réponse ne peut pas être vide.');
            document.getElementById('response').focus();
            e.preventDefault();
            return false;
        }
    });
});
