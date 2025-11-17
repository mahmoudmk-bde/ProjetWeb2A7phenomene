/**
 * ENGAGE - Gamification Module
 * Form Validation & Interactions
 */

document.addEventListener('DOMContentLoaded', function() {
    // Custom file input label update
    const fileInputs = document.querySelectorAll('.custom-file-input');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const fileName = this.files[0]?.name || 'Choisir un fichier...';
            const label = this.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
        });
    });

    // Form validation
    const forms = document.querySelectorAll('form[name="partenaireForm"], form[name="storeItemForm"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Image preview before upload
    const imageInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById(input.id + '-preview');
                    if (preview) {
                        preview.src = event.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

/**
 * Validate form fields
 */
function validateForm(form) {
    let isValid = true;
    const errors = [];

    // Clear previous error messages
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(msg => msg.remove());

    // Validate required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            errors.push(`Le champ ${field.name} est obligatoire`);
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });

    // Validate email if present
    const emailField = form.querySelector('input[type="email"]');
    if (emailField && emailField.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailField.value)) {
            isValid = false;
            errors.push('Email invalide');
            emailField.classList.add('is-invalid');
        }
    }

    // Validate price if present
    const priceField = form.querySelector('input[name="prix"]');
    if (priceField && priceField.value) {
        const price = parseFloat(priceField.value);
        if (isNaN(price) || price < 0) {
            isValid = false;
            errors.push('Le prix doit être un nombre positif');
            priceField.classList.add('is-invalid');
        }
    }

    // Validate stock if present
    const stockField = form.querySelector('input[name="stock"]');
    if (stockField && stockField.value) {
        const stock = parseInt(stockField.value);
        if (isNaN(stock) || stock < 0) {
            isValid = false;
            errors.push('Le stock doit être un nombre positif');
            stockField.classList.add('is-invalid');
        }
    }

    // Display errors
    if (errors.length > 0) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.innerHTML = '<strong>Erreurs de validation :</strong><ul>' +
            errors.map(err => '<li>' + err + '</li>').join('') +
            '</ul>';
        form.insertBefore(alertDiv, form.firstChild);
    }

    return isValid;
}

/**
 * Confirmation dialog for delete actions
 */
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}

/**
 * Format currency value
 */
function formatPrice(value) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.maxWidth = '300px';
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}
