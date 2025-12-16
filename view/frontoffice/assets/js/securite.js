// Gestion du menu utilisateur
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

// Fermer le menu utilisateur en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');
    
    if (!userMenu.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Auto-masquer les messages après 5 secondes
setTimeout(function() {
    const alertMessages = document.querySelectorAll('.alert-message');
    alertMessages.forEach(function(alert) {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.5s ease';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// Liste des questions de sécurité
const securityQuestions = [
    "Quel est le nom de votre animal de compagnie ?",
    "Quel est le nom de votre ville natale ?",
    "Quel est le nom de votre école primaire ?",
    "Quel est le métier de votre père ?",
    "Quel est votre film préféré ?",
    "Quel est le nom de votre meilleur ami d'enfance ?",
    "Quel est votre plat préféré ?",
    "Quel est votre livre préféré ?",
    "Quel est le nom de jeune fille de votre mère ?",
    "Quel est votre sport préféré ?"
];

// Mise à jour des options de la question 2
function updateQuestion2Options() {
    const question1 = document.getElementById('question1');
    const question2 = document.getElementById('question2');
    const selectedQuestion1 = question1.value;
    
    // Sauvegarder la sélection actuelle de la question 2
    const currentQuestion2 = question2.value;
    
    // Vider les options de la question 2
    question2.innerHTML = '<option value="">Choisissez une question</option>';
    
    // Ajouter toutes les questions sauf celle sélectionnée dans la question 1
    securityQuestions.forEach(question => {
        if (question !== selectedQuestion1) {
            const option = document.createElement('option');
            option.value = question;
            option.textContent = question;
            if (question === currentQuestion2 && question !== selectedQuestion1) {
                option.selected = true;
            }
            question2.appendChild(option);
        }
    });
}

// Mise à jour des options de la question 1
function updateQuestion1Options() {
    const question1 = document.getElementById('question1');
    const question2 = document.getElementById('question2');
    const selectedQuestion2 = question2.value;
    
    // Sauvegarder la sélection actuelle de la question 1
    const currentQuestion1 = question1.value;
    
    // Vider les options de la question 1
    question1.innerHTML = '<option value="">Choisissez une question</option>';
    
    // Ajouter toutes les questions sauf celle sélectionnée dans la question 2
    securityQuestions.forEach(question => {
        if (question !== selectedQuestion2) {
            const option = document.createElement('option');
            option.value = question;
            option.textContent = question;
            if (question === currentQuestion1 && question !== selectedQuestion2) {
                option.selected = true;
            }
            question1.appendChild(option);
        }
    });
}

// Validation du formulaire de questions de sécurité
function validateSecurityQuestionsForm(event) {
    const question1 = document.getElementById('question1');
    const question2 = document.getElementById('question2');
    const answer1 = document.querySelector('input[name="security_answer1"]');
    const answer2 = document.querySelector('input[name="security_answer2"]');
    const password = document.querySelector('input[name="security_password"]');
    
    let isValid = true;
    let errorMessage = '';
    
    // Vérifier si les questions sont identiques
    if (question1.value && question2.value && question1.value === question2.value) {
        isValid = false;
        errorMessage = 'Vous ne pouvez pas choisir la même question pour les deux questions de sécurité.';
    }
    
    // Vérifier si toutes les questions sont sélectionnées
    if (!question1.value || !question2.value) {
        isValid = false;
        errorMessage = 'Veuillez sélectionner les deux questions de sécurité.';
    }
    
    // Vérifier si toutes les réponses sont remplies
    if (!answer1.value || !answer2.value) {
        isValid = false;
        errorMessage = 'Veuillez remplir les deux réponses de sécurité.';
    }
    
    // Vérifier si le mot de passe est rempli
    if (!password.value) {
        isValid = false;
        errorMessage = 'Veuillez entrer votre mot de passe pour confirmer les modifications.';
    }
    
    // Vérifier la longueur minimale des réponses
    if (answer1.value.length < 2 || answer2.value.length < 2) {
        isValid = false;
        errorMessage = 'Les réponses doivent contenir au moins 2 caractères.';
    }
    
    if (!isValid) {
        event.preventDefault();
        showCustomAlert(errorMessage, 'error');
    }
    
    return isValid;
}

// Fonction pour basculer la visibilité des réponses
function toggleAnswerVisibility(button) {
    const input = button.previousElementSibling;
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        button.setAttribute('title', 'Masquer la réponse');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        button.setAttribute('title', 'Afficher la réponse');
    }
}

// Validation du formulaire de changement de mot de passe
function validatePasswordForm(event) {
    const currentPassword = document.querySelector('input[name="current_password"]');
    const newPassword = document.querySelector('input[name="new_password"]');
    const confirmPassword = document.querySelector('input[name="confirm_password"]');
    
    let isValid = true;
    let errorMessage = '';
    
    // Vérifier si tous les champs sont remplis
    if (!currentPassword.value || !newPassword.value || !confirmPassword.value) {
        isValid = false;
        errorMessage = 'Tous les champs du mot de passe doivent être remplis.';
    }
    
    // Vérifier la longueur minimale du nouveau mot de passe
    if (newPassword.value.length < 6) {
        isValid = false;
        errorMessage = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
    }
    
    // Vérifier si les nouveaux mots de passe correspondent
    if (newPassword.value !== confirmPassword.value) {
        isValid = false;
        errorMessage = 'Les nouveaux mots de passe ne correspondent pas.';
    }
    
    // Vérifier si le nouveau mot de passe est différent de l'actuel
    if (newPassword.value === currentPassword.value) {
        isValid = false;
        errorMessage = 'Le nouveau mot de passe doit être différent du mot de passe actuel.';
    }
    
    if (!isValid) {
        event.preventDefault();
        showCustomAlert(errorMessage, 'error');
    }
    
    return isValid;
}

// Fonction pour afficher des alertes personnalisées
function showCustomAlert(message, type = 'info') {
    // Supprimer les alertes existantes
    const existingAlerts = document.querySelectorAll('.custom-alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Créer la nouvelle alerte
    const alertDiv = document.createElement('div');
    alertDiv.className = `custom-alert alert-${type}`;
    alertDiv.innerHTML = `
        <div class="alert-content">
            <span class="alert-message">${message}</span>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    
    // Styles pour l'alerte
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        max-width: 500px;
        background: ${type === 'error' ? '#f8d7da' : type === 'success' ? '#d1edff' : '#fff3cd'};
        border: 1px solid ${type === 'error' ? '#f5c6cb' : type === 'success' ? '#b8daff' : '#ffeaa7'};
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (alertDiv.parentElement) {
            alertDiv.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => alertDiv.remove(), 300);
        }
    }, 5000);
}

// Animation CSS pour les alertes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .custom-alert .alert-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    
    .custom-alert .alert-message {
        flex: 1;
        margin-right: 10px;
        color: #333;
        font-size: 14px;
        line-height: 1.4;
    }
    
    .custom-alert .alert-close {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #666;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .custom-alert .alert-close:hover {
        color: #333;
    }
    
    /* Styles pour les boutons d'affichage/masquage */
    .password-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        padding: 5px;
    }
    
    .password-toggle:hover {
        color: #333;
    }
    
    .input-group {
        position: relative;
    }
    
    /* Indicateur de force du mot de passe */
    .password-strength {
        height: 4px;
        margin-top: 5px;
        border-radius: 2px;
        transition: all 0.3s ease;
    }
    
    .password-strength.weak {
        background: #dc3545;
        width: 25%;
    }
    
    .password-strength.fair {
        background: #ffc107;
        width: 50%;
    }
    
    .password-strength.good {
        background: #28a745;
        width: 75%;
    }
    
    .password-strength.strong {
        background: #007bff;
        width: 100%;
    }
`;

document.head.appendChild(style);

// Fonction pour vérifier la force du mot de passe
function checkPasswordStrength(password) {
    let strength = 0;
    
    // Longueur minimale
    if (password.length >= 8) strength++;
    
    // Contient des minuscules
    if (/[a-z]/.test(password)) strength++;
    
    // Contient des majuscules
    if (/[A-Z]/.test(password)) strength++;
    
    // Contient des chiffres
    if (/[0-9]/.test(password)) strength++;
    
    // Contient des caractères spéciaux
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
}

// Fonction pour mettre à jour l'indicateur de force du mot de passe
function updatePasswordStrengthIndicator(input) {
    const strengthIndicator = input.parentElement.querySelector('.password-strength');
    if (!strengthIndicator) return;
    
    const strength = checkPasswordStrength(input.value);
    
    strengthIndicator.className = 'password-strength';
    
    if (input.value.length === 0) {
        strengthIndicator.style.width = '0%';
        return;
    }
    
    if (strength <= 2) {
        strengthIndicator.classList.add('weak');
    } else if (strength === 3) {
        strengthIndicator.classList.add('fair');
    } else if (strength === 4) {
        strengthIndicator.classList.add('good');
    } else {
        strengthIndicator.classList.add('strong');
    }
}

// Initialisation après le chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter des écouteurs d'événements pour la validation en temps réel
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.name === 'new_password') {
                updatePasswordStrengthIndicator(this);
            }
        });
    });
    
    // Ajouter des indicateurs de force pour les nouveaux mots de passe
    const newPasswordInputs = document.querySelectorAll('input[name="new_password"]');
    newPasswordInputs.forEach(input => {
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength';
        input.parentElement.appendChild(strengthIndicator);
    });
    
    // Validation des formulaires
    const securityForm = document.getElementById('securityQuestionsForm');
    if (securityForm) {
        securityForm.addEventListener('submit', validateSecurityQuestionsForm);
    }
    
    const passwordForms = document.querySelectorAll('form');
    passwordForms.forEach(form => {
        if (form.querySelector('input[name="new_password"]')) {
            form.addEventListener('submit', validatePasswordForm);
        }
    });
    
    // Initialiser les options des questions si elles existent
    if (document.getElementById('question1') && document.getElementById('question2')) {
        updateQuestion1Options();
        updateQuestion2Options();
    }
    
    // Ajouter des boutons de basculement de visibilité pour les mots de passe
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        if (!field.parentElement.querySelector('.password-toggle')) {
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'password-toggle';
            toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
            toggleButton.setAttribute('title', 'Afficher le mot de passe');
            toggleButton.onclick = function() {
                toggleAnswerVisibility(this);
            };
            
            field.parentElement.style.position = 'relative';
            field.parentElement.appendChild(toggleButton);
        }
    });
});

// Fonction pour confirmer les actions critiques
function confirmAction(message) {
    return confirm(message);
}

// Gestion des onglets
function switchTab(tabId) {
    // Masquer tous les contenus d'onglets
    const tabContents = document.querySelectorAll('.tab-content .tab-pane');
    tabContents.forEach(content => {
        content.classList.remove('show', 'active');
    });
    
    // Désactiver tous les onglets
    const tabLinks = document.querySelectorAll('.settings-tabs .nav-link');
    tabLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Activer l'onglet sélectionné
    const selectedTab = document.getElementById(tabId);
    if (selectedTab) {
        selectedTab.classList.add('show', 'active');
    }
    
    // Activer le lien d'onglet correspondant
    const correspondingLink = document.querySelector(`[href="#${tabId}"]`);
    if (correspondingLink) {
        correspondingLink.classList.add('active');
    }
}

// Export des fonctions pour un usage global
window.toggleUserMenu = toggleUserMenu;
window.updateQuestion1Options = updateQuestion1Options;
window.updateQuestion2Options = updateQuestion2Options;
window.toggleAnswerVisibility = toggleAnswerVisibility;
window.validateSecurityQuestionsForm = validateSecurityQuestionsForm;
window.validatePasswordForm = validatePasswordForm;
window.showCustomAlert = showCustomAlert;
window.confirmAction = confirmAction;
window.switchTab = switchTab;