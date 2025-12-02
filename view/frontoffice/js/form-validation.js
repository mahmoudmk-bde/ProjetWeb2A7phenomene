document.addEventListener('DOMContentLoaded', function () {
    // Elements for email lookup + history
    var lookupSection = document.getElementById('emailLookupSection');
    var lookupEmail = document.getElementById('lookupEmail');
    var lookupBtn = document.getElementById('lookupBtn');
    var lookupMessage = document.getElementById('lookupMessage');
    var historyContainer = document.getElementById('historyContainer');
    var changeEmailBtn = document.getElementById('changeEmailBtn');
    var form = document.getElementById('reclamationForm');
    var pageEmailField = document.getElementById('email');

    if (lookupBtn) {
        lookupBtn.addEventListener('click', function (e) {
            e.preventDefault();
            lookupMessage.textContent = '';
            historyContainer.innerHTML = '';
            var em = (lookupEmail && lookupEmail.value) ? lookupEmail.value.trim() : '';
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!em || !emailRegex.test(em)) {
                lookupMessage.textContent = 'Veuillez saisir une adresse email valide.';
                return;
            }

            // fetch history
            lookupBtn.disabled = true;
            lookupBtn.textContent = 'Chargement...';
            fetch('../../Controller/get_reclamations_by_email.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(em)
            }).then(function (res) { return res.json(); })
            .then(function (data) {
                lookupBtn.disabled = false;
                lookupBtn.textContent = "Voir l'historique";
                if (!data || !data.success) {
                    lookupMessage.textContent = data && data.message ? data.message : 'Erreur lors de la recherche.';
                    // still allow showing form
                    showFormForEmail(em);
                    return;
                }

                var rows = data.data || [];
                if (rows.length === 0) {
                    historyContainer.innerHTML = '<div class="history-item">Aucune réclamation trouvée pour cette adresse.</div>';
                } else {
                    var out = '';
                    rows.forEach(function (r) {
                        out += '<div class="history-item">';
                        out += '<div class="history-meta">' + (r.date_creation ? r.date_creation : '') + ' — <strong>' + escapeHtml(r.sujet) + '</strong> — <em>' + escapeHtml(r.statut) + '</em></div>';
                        out += '<div>' + escapeHtml(r.description) + '</div>';
                        out += '</div>';
                    });
                    historyContainer.innerHTML = out;
                }

                // show the add form and prefill email
                showFormForEmail(em);
            }).catch(function (err) {
                lookupBtn.disabled = false;
                lookupBtn.textContent = "Voir l'historique";
                lookupMessage.textContent = 'Erreur réseau.';
                showFormForEmail(em);
            });
        });
    }

    function showFormForEmail(em) {
        if (form) {
            form.classList.remove('hidden');
        }
        if (pageEmailField) {
            pageEmailField.value = em || '';
            pageEmailField.setAttribute('readonly', 'readonly');
        }
        if (changeEmailBtn) changeEmailBtn.style.display = 'inline-block';
    }

    if (changeEmailBtn) {
        changeEmailBtn.addEventListener('click', function () {
            if (pageEmailField) {
                pageEmailField.removeAttribute('readonly');
                pageEmailField.focus();
            }
            if (form) form.classList.add('hidden');
            historyContainer.innerHTML = '';
            lookupMessage.textContent = '';
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
    }

    // end email lookup helpers

    // ensure form variable exists for validation below
    if (!form) return;
    form.addEventListener('submit', function (e) {
        var sujet = document.getElementById('sujet').value.trim();
        var email = document.getElementById('email').value.trim();
        var description = document.getElementById('description').value.trim();
        // clear previous errors
        clearError(document.getElementById('sujet'));
        clearError(document.getElementById('email'));
        clearError(document.getElementById('description'));

        var hasError = false;

        if (!sujet) {
            showError(document.getElementById('sujet'), 'Veuillez renseigner le sujet.');
            hasError = true;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRegex.test(email)) {
            showError(document.getElementById('email'), 'Veuillez renseigner une adresse email valide.');
            hasError = true;
        }

        if (!description) {
            showError(document.getElementById('description'), 'Veuillez renseigner la description de la réclamation.');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
            // focus the first invalid field
            var firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
            return false;
        }
    });

    // helper to show error under a field
    function showError(field, message) {
        if (!field) return;
        field.classList.add('is-invalid');
        var helpId = field.getAttribute('aria-describedby');
        if (helpId) {
            var help = document.getElementById(helpId);
            if (help) help.textContent = message;
        }
    }

    function clearError(field) {
        if (!field) return;
        field.classList.remove('is-invalid');
        var helpId = field.getAttribute('aria-describedby');
        if (helpId) {
            var help = document.getElementById(helpId);
            if (help) help.textContent = '';
        }
    }

    // Clear field error on input
    ['sujet', 'email', 'description'].forEach(function (id) {
        var f = document.getElementById(id);
        if (!f) return;
        f.addEventListener('input', function () { clearError(f); });
        f.addEventListener('blur', function () { clearError(f); });
    });
});
