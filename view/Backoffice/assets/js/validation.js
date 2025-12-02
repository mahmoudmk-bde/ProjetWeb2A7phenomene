// Shared form validation for Backoffice pages
document.addEventListener('DOMContentLoaded', function () {
    function showFormError(container, messages) {
        let html = '<div class="alert alert-danger">';
        messages.forEach(function (m) { html += '<div>' + m + '</div>'; });
        html += '</div>';
        container.innerHTML = html;
        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function clearFormError(container) {
        container.innerHTML = '';
    }

    // Generic validate event form
    function validateEventForm(form) {
        const errors = [];
        const titre = form.querySelector('[name="titre"]');
        const description = form.querySelector('[name="description"]');
        const dateField = form.querySelector('[name="date_evenement"]');
        const lieu = form.querySelector('[name="lieu"]');
        const org = form.querySelector('[name="id_organisation"]');
        const image = form.querySelector('[name="image"]');

        if (!titre || !titre.value.trim()) errors.push('Le titre de l\'événement est requis.');
        else if (titre.value.trim().length < 3) errors.push('Le titre doit contenir au moins 3 caractères.');

        if (!description || !description.value.trim()) errors.push('La description est requise.');
        else if (description.value.trim().length < 10) errors.push('La description doit contenir au moins 10 caractères.');

        if (!dateField || !dateField.value) errors.push('La date de l\'événement est requise.');
        else {
            // basic YYYY-MM-DD format check
            const d = new Date(dateField.value);
            if (isNaN(d.getTime())) errors.push('La date fournie n\'est pas valide.');
        }

        if (!lieu || !lieu.value.trim()) errors.push('Le lieu est requis.');

        if (!org || !org.value) errors.push('Veuillez sélectionner l\'organisation.');

        if (image && image.files && image.files.length > 0) {
            const file = image.files[0];
            const maxSize = 3 * 1024 * 1024; // 3MB
            const allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
            if (file.size > maxSize) errors.push('L\'image doit être inférieure à 3MB.');
            if (allowed.indexOf(file.type) === -1) errors.push('Format d\'image non pris en charge (png, jpg, jpeg, gif).');
        }

        return errors;
    }

    // Attach to forms by id if present
    const createForm = document.getElementById('event-create-form');
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const errContainer = document.getElementById('event-create-errors');
            clearFormError(errContainer);
            const errors = validateEventForm(createForm);
            if (errors.length) {
                showFormError(errContainer, errors);
                return false;
            }
            createForm.submit();
        });
    }

    const editForm = document.getElementById('event-edit-form');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const errContainer = document.getElementById('event-edit-errors');
            clearFormError(errContainer);
            const errors = validateEventForm(editForm);
            if (errors.length) {
                showFormError(errContainer, errors);
                return false;
            }
            editForm.submit();
        });
    }
});
