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

    // === VALIDATION DU FORMULAIRE DE MODIFICATION ARTICLE ===
    const form = document.querySelector('form');
    const contenuTextarea = document.getElementById('contenu');
    
    // Compteur de caractères pour le contenu
    if (contenuTextarea) {
        const charCounter = document.createElement('div');
        charCounter.className = 'char-counter';
        charCounter.style.cssText = 'font-size: 0.75rem; color: #8a8da5; text-align: right; margin-top: 5px;';
        contenuTextarea.parentNode.appendChild(charCounter);
        
        contenuTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCounter.textContent = length + ' caractères';
            
            if (length > 1000) {
                charCounter.style.color = '#ff6b6b';
            } else {
                charCounter.style.color = '#8a8da5';
            }
        });
        
        // Initialiser le compteur
        contenuTextarea.dispatchEvent(new Event('input'));
    }
    
    // Validation de date
    const dateInput = document.getElementById('date_publication');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.max = today;
    }
    
    // Fonction pour afficher les erreurs
    function showError(input, message) {
        removeError(input);
        
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
        input.style.borderColor = '#dc3545';
    }
    
    // Fonction pour supprimer les erreurs
    function removeError(input) {
        const existingError = input.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        input.style.borderColor = '';
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
            if (selectedDate > today) {
                showError(input, 'La date ne peut pas être dans le futur');
                return false;
            }
        }
        
        removeError(input);
        return true;
    }
    
    // Fonction pour obtenir le label du champ
    function getFieldLabel(input) {
        const label = input.previousElementSibling;
        if (label && label.tagName === 'LABEL') {
            return label.textContent.replace('*', '').trim();
        }
        return 'Ce champ';
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
            field.addEventListener('blur', function() {
                validateField(this);
            });
            
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    removeError(this);
                }
            });
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