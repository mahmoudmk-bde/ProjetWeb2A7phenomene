// Fonction pour afficher/masquer le mot de passe
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleButton = passwordField.nextElementSibling;
    const icon = toggleButton.querySelector('i');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Fonction pour afficher une erreur
function showError(fieldId, message) {
    const errorElement = document.getElementById(`error-${fieldId}`);
    const inputElement = document.getElementById(fieldId);
    
    errorElement.textContent = message;
    errorElement.style.display = 'block';
    inputElement.classList.add('error');
}

// Fonction pour cacher une erreur
function hideError(fieldId) {
    const errorElement = document.getElementById(`error-${fieldId}`);
    const inputElement = document.getElementById(fieldId);
    
    if (errorElement) {
        errorElement.style.display = 'none';
    }
    if (inputElement) {
        inputElement.classList.remove('error');
    }
}

// Fonction de validation
function validateForm() {
    let isValid = true;
    
    const pr = document.getElementById("prenom").value.trim();
    const nom = document.getElementById("nom").value.trim();
    const dt = document.getElementById("dt_naiss").value;
    const mail = document.getElementById("mail").value.trim();
    const tel = document.getElementById("num").value.trim();
    const mdp = document.getElementById("mdp").value;
    const cmdp = document.getElementById("cmdp").value;
    
    // Cacher toutes les erreurs
    hideError('prenom');
    hideError('nom');
    hideError('dt_naiss');
    hideError('mail');
    hideError('num');
    hideError('mdp');
    hideError('cmdp');
    
    // Validation prénom
    if (!pr) {
        showError('prenom', "Le prénom est obligatoire");
        isValid = false;
    }
    
    // Validation nom
    if (!nom) {
        showError('nom', "Le nom est obligatoire");
        isValid = false;
    }
    
    // Validation date de naissance
    if (!dt) {
        showError('dt_naiss', "La date de naissance est obligatoire");
        isValid = false;
    }
    
    // Validation email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!mail) {
        showError('mail', "L'adresse email est obligatoire");
        isValid = false;
    } else if (!emailRegex.test(mail)) {
        showError('mail', "Veuillez entrer une adresse email valide");
        isValid = false;
    }
    
    // Validation téléphone (optionnel mais doit être valide si rempli)
    if (tel && tel.length < 8) {
        showError('num', "Le numéro doit contenir au moins 8 chiffres");
        isValid = false;
    }
    
    // Validation mot de passe
    if (!mdp) {
        showError('mdp', "Le mot de passe est obligatoire");
        isValid = false;
    } else if (mdp.length < 6) {
        showError('mdp', "Le mot de passe doit contenir au moins 6 caractères");
        isValid = false;
    }
    
    // Validation confirmation mot de passe
    if (!cmdp) {
        showError('cmdp', "Veuillez confirmer votre mot de passe");
        isValid = false;
    } else if (mdp !== cmdp) {
        showError('cmdp', "Les mots de passe ne correspondent pas");
        isValid = false;
    }
    
    return isValid;
}

// MODIFICATION: Fonction pour valider et soumettre le formulaire
function validateAndSubmit() {
    if (validateForm()) {
        // Soumettre le formulaire
        document.getElementById('inscriptionForm').submit();
    } else {
        alert('Veuillez corriger les erreurs dans le formulaire');
    }
}

// Écouteurs d'événements pour la validation en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            hideError(this.id);
        });
        
        input.addEventListener('blur', function() {
            // Validation simple sur la perte de focus
            if (this.hasAttribute('required') && !this.value.trim()) {
                showError(this.id, "Ce champ est obligatoire");
            }
        });
    });
    
    // MODIFICATION: Ajouter l'événement de soumission du formulaire
    document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault(); // Empêcher la soumission si validation échoue
            alert('Veuillez corriger les erreurs dans le formulaire');
        }
    });
});