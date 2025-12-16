document.addEventListener('DOMContentLoaded', function() {
    // === GESTION DU MENU DÉROULANT DE LA SIDEBAR ===
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const gestionSubmenu = document.getElementById('gestion-submenu');
    
    if (dropdownToggle && gestionSubmenu) {
        let isSubmenuOpen = gestionSubmenu.classList.contains('show');
        
        function updateDropdownIcon() {
            const icon = dropdownToggle.querySelector('i.fas');
            if (icon) {
                if (isSubmenuOpen) {
                    icon.className = 'fas fa-chevron-down';
                } else {
                    icon.className = 'fas fa-chevron-right';
                }
            }
        }
        
        updateDropdownIcon();
        
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            isSubmenuOpen = !isSubmenuOpen;
            
            if (isSubmenuOpen) {
                gestionSubmenu.classList.add('show');
                dropdownToggle.setAttribute('aria-expanded', 'true');
            } else {
                gestionSubmenu.classList.remove('show');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
            
            updateDropdownIcon();
        });
    }
    
    // Gestion de l'état actif des liens
    function setActiveMenuItem() {
        const currentPage = window.location.pathname.split('/').pop();
        const menuLinks = document.querySelectorAll('#gestion-submenu a');
        
        menuLinks.forEach(link => {
            link.classList.remove('active');
            const linkHref = link.getAttribute('href');
            if (linkHref === currentPage) {
                link.classList.add('active');
            }
        });
    }
    
    setActiveMenuItem();

    // === VALIDATION DU FORMULAIRE ARTICLE ===
    const form = document.querySelector('.quiz-form');
    const contenuTextarea = document.getElementById('contenu');
    const charCounter = document.querySelector('.char-counter');
    
    // Fonction pour afficher les erreurs
    function showError(input, message) {
        removeError(input);
        removeSuccess(input);
        
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error';
        errorElement.style.cssText = `
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        `;
        errorElement.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        
        input.parentNode.appendChild(errorElement);
        
        // Appliquer le style d'erreur directement
        input.style.borderColor = '#dc3545';
        input.style.borderWidth = '2px';
        input.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';
    }
    
    // Fonction pour afficher le succès (champ valide)
    function showSuccess(input) {
        removeError(input);
        
        // Appliquer le style de succès directement
        input.style.borderColor = '#28a745';
        input.style.borderWidth = '2px';
        input.style.boxShadow = '0 0 0 0.2rem rgba(40, 167, 69, 0.25)';
        
        // Ajouter une icône de validation si désiré
        const existingSuccess = input.parentNode.querySelector('.field-success');
        if (!existingSuccess) {
            const successElement = document.createElement('div');
            successElement.className = 'field-success';
            successElement.style.cssText = `
                color: #28a745;
                font-size: 0.875rem;
                margin-top: 5px;
                display: flex;
                align-items: center;
                gap: 5px;
            `;
            successElement.innerHTML = `<i class="fas fa-check-circle"></i> Champ valide`;
            
            input.parentNode.appendChild(successElement);
        }
    }
    
    // Fonction pour réinitialiser le style du champ
    function resetFieldStyle(input) {
        removeError(input);
        removeSuccess(input);
        
        // Réinitialiser le style
        input.style.borderColor = '';
        input.style.borderWidth = '';
        input.style.boxShadow = '';
    }
    
    // Fonction pour supprimer les erreurs
    function removeError(input) {
        const existingError = input.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }
    
    // Fonction pour supprimer le succès
    function removeSuccess(input) {
        const existingSuccess = input.parentNode.querySelector('.field-success');
        if (existingSuccess) {
            existingSuccess.remove();
        }
    }
    
    // Fonction pour obtenir le label du champ
    function getFieldLabel(input) {
        const label = input.previousElementSibling;
        if (label && label.tagName === 'LABEL') {
            return label.textContent.replace('*', '').trim();
        }
        return 'Ce champ';
    }
    
    // Compteur de caractères pour le contenu
    if (contenuTextarea && charCounter) {
        contenuTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCounter.textContent = length + ' caractères';
            
            if (length > 1000) {
                charCounter.style.color = '#ff6b6b';
            } else {
                charCounter.style.color = '#8a8da5';
            }
        });
        
        // Déclencher l'événement input pour initialiser le compteur
        if (contenuTextarea.value) {
            contenuTextarea.dispatchEvent(new Event('input'));
        }
    }
    
    // Validation de date
    const dateInput = document.getElementById('date_publication');
    if (dateInput) {
        // Définir la date maximale à aujourd'hui
        const today = new Date().toISOString().split('T')[0];
        dateInput.max = today;
        
        // Si aucune date n'est définie, mettre la date d'aujourd'hui par défaut
        if (!dateInput.value) {
            dateInput.value = today;
        }
    }
    
    // Fonction de validation
    function validateField(input) {
        const value = input.value.trim();
        
        if (!value) {
            const fieldName = getFieldLabel(input);
            showError(input, `${fieldName} est obligatoire`);
            return false;
        }
        
        // Validation spécifique pour la date
        if (input.type === 'date') {
            const selectedDate = new Date(value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Réinitialiser l'heure pour comparer seulement les dates
            
            if (selectedDate > today) {
                showError(input, 'La date ne peut pas être dans le futur');
                return false;
            }
        }
        
        // Validation spécifique pour le contenu (minimum 50 caractères)
        if (input.id === 'contenu' && value.length < 50) {
            showError(input, 'Le contenu doit contenir au moins 50 caractères');
            return false;
        }
        
        // Validation spécifique pour le titre (minimum 5 caractères)
        if (input.id === 'titre' && value.length < 5) {
            showError(input, 'Le titre doit contenir au moins 5 caractères');
            return false;
        }
        
        showSuccess(input);
        return true;
    }
    
    // Liste des champs obligatoires
    function getRequiredFields() {
        return [
            document.getElementById('titre'),
            document.getElementById('contenu'),
            document.getElementById('date_publication')
        ].filter(field => field !== null);
    }
    
    // Validation en temps réel
    if (form) {
        const requiredFields = getRequiredFields();
        
        requiredFields.forEach(field => {
            // Validation au blur (quand on quitte le champ)
            field.addEventListener('blur', function() {
                validateField(this);
            });
            
            // Validation en temps réel pendant la saisie
            field.addEventListener('input', function() {
                const value = this.value.trim();
                
                if (value) {
                    // Validation spécifique en temps réel
                    if (this.id === 'contenu' && value.length < 50) {
                        showError(this, 'Le contenu doit contenir au moins 50 caractères');
                    } else if (this.id === 'titre' && value.length < 5) {
                        showError(this, 'Le titre doit contenir au moins 5 caractères');
                    } else {
                        // Si les validations spécifiques passent, montrer le succès
                        resetFieldStyle(this);
                        showSuccess(this);
                    }
                } else {
                    // Champ vide - montrer l'erreur immédiatement
                    resetFieldStyle(this);
                    const fieldName = getFieldLabel(this);
                    showError(this, `${fieldName} est obligatoire`);
                }
            });
            
            // Validation spécifique pour la date
            if (field.type === 'date') {
                field.addEventListener('change', function() {
                    validateField(this);
                });
            }
        });
        
        // Initialiser la validation pour les champs pré-remplis
        requiredFields.forEach(field => {
            if (field.value.trim()) {
                validateField(field);
            }
        });
        
        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = getRequiredFields();
            
            requiredFields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Faire défiler vers le premier champ en erreur
                const firstError = form.querySelector('.field-error');
                if (firstError) {
                    firstError.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            }
        });
    }
});