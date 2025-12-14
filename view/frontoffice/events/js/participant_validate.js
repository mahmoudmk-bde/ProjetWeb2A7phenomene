document.addEventListener('DOMContentLoaded', function () {
    function showErrors(container, messages) {
        if (!container) return;
        container.innerHTML = '';
        const div = document.createElement('div');
        div.className = 'alert alert-danger';
        messages.forEach(m => {
            const p = document.createElement('div');
            p.textContent = m;
            div.appendChild(p);
        });
        container.appendChild(div);
        container.scrollIntoView({behavior: 'smooth', block: 'center'});
    }

    function validateForm(form) {
        const errors = [];
        const prenomEl = form.querySelector('[name="prenom"]');
        const nomEl = form.querySelector('[name="nom"]');
        const emailEl = form.querySelector('[name="email"]');
        const prenom = prenomEl ? prenomEl.value.trim() : '';
        const nom = nomEl ? nomEl.value.trim() : '';
        const email = emailEl ? emailEl.value.trim() : '';

        if (!prenom) errors.push('Le prénom est requis.');
        if (!nom) errors.push('Le nom est requis.');
        if (!email) errors.push('L\'email est requis.');
        else {
            const re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\\.,;:\s@\"]+\.)+[^<>()[\]\\.,;:\s@\"]{2,})$/i;
            if (!re.test(email)) errors.push('L\'email n\'est pas valide.');
        }

        return errors;
    }

    // Attach to inline form if present
    const inlineForm = document.getElementById('participant-form');
    if (inlineForm) {
        inlineForm.setAttribute('novalidate', 'novalidate');
        inlineForm.addEventListener('submit', function (e) {
            const errors = validateForm(inlineForm);
            const errContainer = document.getElementById('participant-errors');
            if (errors.length) {
                e.preventDefault();
                showErrors(errContainer, errors);
                return false;
            }
        });
    }

    // Attach to modal form
    const modalForm = document.getElementById('participant-modal-form');
    if (modalForm) {
        modalForm.setAttribute('novalidate', 'novalidate');
        modalForm.addEventListener('submit', function (e) {
            const errors = validateForm(modalForm);
            const errContainer = document.getElementById('participant-errors-modal');
            if (errors.length) {
                e.preventDefault();
                showErrors(errContainer, errors);
                return false;
            }
            // allow submit — page will reload and modal will close
        });
    }
});
