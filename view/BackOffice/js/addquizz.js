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

    // === VALIDATION DU FORMULAIRE QUIZ ===
    const form = document.querySelector('.quiz-form');
    const reponseInputs = ['reponse1', 'reponse2', 'reponse3'];
    
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
        input.style.borderColor = '#dc3545';
    }
    
    // Fonction pour afficher le succès (champ valide)
    function showSuccess(input) {
        removeError(input);
        input.style.borderColor = '#28a745';
        
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
    
    // Fonction pour supprimer les erreurs
    function removeError(input) {
        const existingError = input.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        input.style.borderColor = '';
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
            return label.textContent.replace('*', '').replace(':', '').trim();
        }
        return 'Ce champ';
    }
    
    // Fonction de validation générique
    function validateField(input) {
        const value = input.value.trim();
        
        if (!value) {
            const fieldName = getFieldLabel(input);
            showError(input, `${fieldName} est obligatoire`);
            return false;
        }
        
        removeError(input);
        showSuccess(input);
        return true;
    }
    
    // Fonction pour valider les réponses différentes
    function validateDifferentResponses() {
        let isValid = true;
        const responses = reponseInputs.map(id => {
            const input = document.getElementById(id);
            return {
                input: input,
                value: input ? input.value.trim() : '',
                id: id
            };
        });
        
        // Vérifier que toutes les réponses sont remplies
        responses.forEach(response => {
            if (!response.value) {
                showError(response.input, `${getFieldLabel(response.input)} est obligatoire`);
                isValid = false;
            }
        });
        
        if (!isValid) return false;
        
        // Vérifier que les réponses sont différentes
        const uniqueValues = new Set(responses.map(r => r.value.toLowerCase()));
        if (uniqueValues.size !== responses.length) {
            responses.forEach(response => {
                showError(response.input, 'Les réponses doivent être différentes');
            });
            return false;
        }
        
        // Supprimer les erreurs de différence si tout est bon
        responses.forEach(response => {
            removeError(response.input);
            showSuccess(response.input);
        });
        
        return true;
    }
    
    // Fonction pour valider la bonne réponse
    function validateBonneReponse() {
        const select = document.getElementById('bonne_reponse');
        if (!select) return false;
        
        const value = select.value;
        if (!value) {
            showError(select, 'Veuillez sélectionner la bonne réponse');
            return false;
        }
        
        // Vérifier que les réponses sont remplies avant de pouvoir sélectionner
        const allResponsesFilled = reponseInputs.every(id => {
            const input = document.getElementById(id);
            return input && input.value.trim();
        });
        
        if (!allResponsesFilled) {
            showError(select, 'Veuillez d\'abord remplir toutes les réponses');
            return false;
        }
        
        removeError(select);
        showSuccess(select);
        return true;
    }
    
    // Fonction pour valider l'article associé
    function validateArticle() {
        const select = document.getElementById('id_article');
        if (!select) return false;
        
        const value = select.value;
        if (!value) {
            showError(select, 'Veuillez sélectionner un article associé');
            return false;
        }
        
        removeError(select);
        showSuccess(select);
        return true;
    }
    
    // Mise à jour dynamique des options de bonne réponse
    function updateBonneReponseOptions() {
        const bonneReponseSelect = document.getElementById('bonne_reponse');
        if (!bonneReponseSelect) return;
        
        const currentSelection = bonneReponseSelect.value;
        bonneReponseSelect.innerHTML = '<option value="">Sélectionnez la bonne réponse</option>';
        
        reponseInputs.forEach((inputId, index) => {
            const input = document.getElementById(inputId);
            if (input) {
                const reponseText = input.value.trim() || `Réponse ${String.fromCharCode(65 + index)}`;
                const option = document.createElement('option');
                option.value = (index + 1).toString();
                option.textContent = `${String.fromCharCode(65 + index)} - ${reponseText}`;
                bonneReponseSelect.appendChild(option);
            }
        });
        
        // Restaurer la sélection si possible
        if (currentSelection && bonneReponseSelect.querySelector(`option[value="${currentSelection}"]`)) {
            bonneReponseSelect.value = currentSelection;
        }
    }
    
    // Validation en temps réel pour les champs de réponse
    reponseInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('blur', function() {
                const isValid = validateField(this);
                if (isValid) {
                    validateDifferentResponses();
                    updateBonneReponseOptions();
                }
            });
            
            input.addEventListener('input', function() {
                if (this.value.trim()) {
                    removeError(this);
                    showSuccess(this);
                    updateBonneReponseOptions();
                } else {
                    removeSuccess(this);
                }
            });
        }
    });
    
    // Validation pour la question
    const questionInput = document.getElementById('question');
    if (questionInput) {
        questionInput.addEventListener('blur', function() {
            validateField(this);
        });
        
        questionInput.addEventListener('input', function() {
            if (this.value.trim()) {
                removeError(this);
                showSuccess(this);
            } else {
                removeSuccess(this);
            }
        });
    }
    
    // Validation pour la bonne réponse
    const bonneReponseSelect = document.getElementById('bonne_reponse');
    if (bonneReponseSelect) {
        bonneReponseSelect.addEventListener('change', function() {
            if (this.value) {
                removeError(this);
                showSuccess(this);
            } else {
                removeSuccess(this);
            }
        });
        
        bonneReponseSelect.addEventListener('blur', function() {
            validateBonneReponse();
        });
    }
    
    // Validation pour l'article associé
    const articleSelect = document.getElementById('id_article');
    if (articleSelect) {
        articleSelect.addEventListener('change', function() {
            if (this.value) {
                removeError(this);
                showSuccess(this);
            } else {
                removeSuccess(this);
            }
        });
        
        articleSelect.addEventListener('blur', function() {
            validateArticle();
        });
    }
    
    // Validation complète à la soumission
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Valider la question
            if (!validateField(questionInput)) {
                isValid = false;
            }
            
            // Valider les réponses différentes
            if (!validateDifferentResponses()) {
                isValid = false;
            }
            
            // Valider la bonne réponse
            if (!validateBonneReponse()) {
                isValid = false;
            }
            
            // Valider l'article associé
            if (!validateArticle()) {
                isValid = false;
            }
            
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
    
    // Initialiser les options de bonne réponse
    updateBonneReponseOptions();
});