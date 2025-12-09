document.addEventListener('DOMContentLoaded', function() {
    // Gestion des inputs fichiers
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

    // Validation des formulaires
    const forms = document.querySelectorAll('form[name="partenaireForm"], form[name="storeItemForm"]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Prévisualisation des images
    const imageInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = this.files[0];
            const previewId = input.id + '-preview';
            const preview = document.getElementById(previewId);
            
            if (file) {
                // Vérification que c'est bien une image
                if (!file.type.startsWith('image/')) {
                    showToast('Le fichier sélectionné n\'est pas une image', 'error');
                    this.value = ''; // Réinitialiser l'input
                    if (preview) {
                        preview.style.display = 'none';
                    }
                    return;
                }

                // Prévisualisation de l'image
                const reader = new FileReader();
                reader.onload = function(event) {
                    if (preview) {
                        preview.src = event.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.onerror = function() {
                    showToast('Erreur lors de la lecture du fichier', 'error');
                };
                reader.readAsDataURL(file);
            } else {
                // Cacher la prévisualisation si aucun fichier
                if (preview) {
                    preview.style.display = 'none';
                }
            }
        });
    });
});

// Validation des champs avec messages spécifiques
function validateForm(form) {
    let isValid = true;
    const errors = [];
    const fieldErrors = {};

    // Effacer les messages d'erreur existants
    const errorMessages = form.querySelectorAll('.error-message, .alert-danger');
    errorMessages.forEach(msg => msg.remove());
    
    // Réinitialiser les classes d'erreur
    const invalidFields = form.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => field.classList.remove('is-invalid'));

    // Validation des champs obligatoires avec messages spécifiques
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        let errorMessage = '';
        const fieldName = field.getAttribute('name') || field.getAttribute('id') || 'Ce champ';

        // Messages d'erreur spécifiques par type de champ
        if (field.name === "telephone" || field.type === "tel") {
            errorMessage = "Le numéro de téléphone est obligatoire";
        } else if (field.name === "logo" || field.name === "image") {
            errorMessage = "Le logo est obligatoire";
        } else if (field.name === "nom" || field.name === "name") {
            errorMessage = "Le nom est obligatoire";
        } else if (field.name === "email" || field.type === "email") {
            errorMessage = "L'adresse email est obligatoire";
        } else if (field.name === "type") {
            errorMessage = "Le type est obligatoire";
        } else if (field.name === "prix" || field.name === "price") {
            errorMessage = "Le prix est obligatoire";
        } else if (field.name === "stock") {
            errorMessage = "Le stock est obligatoire";
        } else if (field.name === "statut" || field.name === "status") {
            errorMessage = "Le statut est obligatoire";
        } else if (field.name === "site_web" || field.name === "website" || field.name === "url") {
            errorMessage = "Le site web est obligatoire";
        } else {
            errorMessage = `Le champ ${fieldName} est obligatoire`;
        }

        // Vérification des champs vides (sauf description)
        if (!field.value.trim() && field.name !== "description") {
            isValid = false;
            if (!fieldErrors[field.name]) {
                fieldErrors[field.name] = errorMessage;
            }
            field.classList.add('is-invalid');
        }
    });

    // Validation spécifique du téléphone
    const phoneField = form.querySelector('input[name="telephone"], input[type="tel"]');
    if (phoneField) {
        if (phoneField.required && !phoneField.value.trim()) {
            isValid = false;
            fieldErrors['telephone'] = 'Le numéro de téléphone est obligatoire';
            phoneField.classList.add('is-invalid');
        } else if (phoneField.value.trim()) {
            const phoneRegex = /^[0-9+\-\s()]{10,}$/;
            if (!phoneRegex.test(phoneField.value.trim())) {
                isValid = false;
                fieldErrors['telephone'] = 'Le numéro de téléphone n\'est pas valide';
                phoneField.classList.add('is-invalid');
            }
        }
    }

    // Validation spécifique du logo/image
    const logoField = form.querySelector('input[name="logo"], input[name="image"]');
    if (logoField && logoField.required) {
        if (!logoField.files || !logoField.files.length) {
            isValid = false;
            fieldErrors['logo'] = 'Le logo est obligatoire';
            logoField.classList.add('is-invalid');
        } else if (logoField.files.length > 0) {
            const file = logoField.files[0];
            // Accepter tous les types d'images courants
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            if (!file.type.startsWith('image/') || !allowedTypes.includes(file.type)) {
                isValid = false;
                fieldErrors['logo'] = 'Le fichier doit être une image (JPEG, PNG, GIF, WebP, SVG)';
                logoField.classList.add('is-invalid');
            }
            
            // Validation de la taille du fichier (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                isValid = false;
                fieldErrors['logo'] = 'L\'image est trop volumineuse (max 5MB)';
                logoField.classList.add('is-invalid');
            }
        }
    }

    // Validation spécifique du statut (select)
    const statutField = form.querySelector('select[name="statut"], select[name="status"]');
    if (statutField && statutField.required) {
        if (!statutField.value || statutField.value === "") {
            isValid = false;
            fieldErrors['statut'] = 'Le statut est obligatoire';
            statutField.classList.add('is-invalid');
        }
    }

    // Validation spécifique du site web
    const websiteField = form.querySelector('input[name="site_web"], input[name="website"], input[name="url"], input[type="url"]');
    if (websiteField) {
        if (websiteField.required && !websiteField.value.trim()) {
            isValid = false;
            fieldErrors['site_web'] = 'Le site web est obligatoire';
            websiteField.classList.add('is-invalid');
        } else if (websiteField.value.trim()) {
            // Validation basique d'URL
            const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
            if (!urlRegex.test(websiteField.value.trim())) {
                isValid = false;
                fieldErrors['site_web'] = 'L\'URL du site web n\'est pas valide';
                websiteField.classList.add('is-invalid');
            }
        }
    }

    // Validation spécifique de l'email
    const emailField = form.querySelector('input[type="email"]');
    if (emailField) {
        if (emailField.required && !emailField.value.trim()) {
            isValid = false;
            fieldErrors['email'] = 'L\'adresse email est obligatoire';
            emailField.classList.add('is-invalid');
        } else if (emailField.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value.trim())) {
                isValid = false;
                fieldErrors['email'] = 'L\'adresse email n\'est pas valide';
                emailField.classList.add('is-invalid');
            }
        }
    }

    // Validation spécifique du prix
    const priceField = form.querySelector('input[name="prix"], input[name="price"]');
    if (priceField) {
        if (priceField.required && !priceField.value.trim()) {
            isValid = false;
            fieldErrors['prix'] = 'Le prix est obligatoire';
            priceField.classList.add('is-invalid');
        } else if (priceField.value.trim()) {
            const price = parseFloat(priceField.value.replace(',', '.'));
            if (isNaN(price) || price < 0) {
                isValid = false;
                fieldErrors['prix'] = 'Le prix doit être un nombre positif';
                priceField.classList.add('is-invalid');
            }
        }
    }

    // Validation spécifique du stock
    const stockField = form.querySelector('input[name="stock"]');
    if (stockField) {
        if (stockField.required && !stockField.value.trim()) {
            isValid = false;
            fieldErrors['stock'] = 'Le stock est obligatoire';
            stockField.classList.add('is-invalid');
        } else if (stockField.value.trim()) {
            const stock = parseInt(stockField.value);
            if (isNaN(stock) || stock < 0) {
                isValid = false;
                fieldErrors['stock'] = 'Le stock doit être un nombre positif';
                stockField.classList.add('is-invalid');
            }
        }
    }

    // Afficher les erreurs spécifiques pour chaque champ
    Object.keys(fieldErrors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-danger small mt-1';
            errorDiv.textContent = fieldErrors[fieldName];
            
            // Insérer le message d'erreur après le champ
            const parent = field.parentNode;
            if (parent.querySelector('.form-text')) {
                parent.insertBefore(errorDiv, parent.querySelector('.form-text'));
            } else {
                parent.appendChild(errorDiv);
            }
        }
    });

    // Afficher une alerte générale si il y a des erreurs
    if (!isValid) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.innerHTML = '<strong>Veuillez corriger les erreurs suivantes :</strong>';
        
        const ul = document.createElement('ul');
        Object.values(fieldErrors).forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            ul.appendChild(li);
        });
        
        alertDiv.appendChild(ul);
        form.insertBefore(alertDiv, form.firstChild);
        
        // Scroll vers le haut pour voir les erreurs
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    return isValid;
}

// Confirmation de suppression
function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}

// Notification
function showToast(message, type = 'info') {
    // Types Bootstrap valides
    const validTypes = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];
    const toastType = validTypes.includes(type) ? type : 'info';
    
    const toast = document.createElement('div');
    toast.className = `alert alert-${toastType} alert-dismissible fade show`;
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.maxWidth = '300px';
    
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);

    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (toast.parentNode) {
            toast.remove();
        }
    }, 5000);
}