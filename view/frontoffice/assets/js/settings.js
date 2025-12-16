function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
}

document.addEventListener('click', function(event) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');
    
    if (!userMenu.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Fermer le menu avec la touche Echap
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.remove('show');
    }
});

// Activation des tabs Bootstrap
var triggerTabList = [].slice.call(document.querySelectorAll('#settingsTabs a'))
triggerTabList.forEach(function (triggerEl) {
    var tabTrigger = new bootstrap.Tab(triggerEl)
    triggerEl.addEventListener('click', function (event) {
        event.preventDefault()
        tabTrigger.show()
    })
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

// Gestion de la prévisualisation d'image
document.addEventListener('DOMContentLoaded', function() {
    const profilePictureInput = document.getElementById('profile_picture');
    const imagePreview = document.getElementById('imagePreview');
    const currentAvatar = document.getElementById('currentAvatar');
    const cancelBtn = document.getElementById('cancelUpload');
    const profilePictureForm = document.getElementById('profilePictureForm');
    
    // Prévisualisation d'image
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Vérification de la taille
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    alert('Le fichier est trop volumineux. Taille maximum: 10MB');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (imagePreview) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    
                    if (currentAvatar) {
                        currentAvatar.style.display = 'none';
                    }
                    
                    if (cancelBtn) {
                        cancelBtn.style.display = 'block';
                    }
                }
                
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Annuler la sélection
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            if (profilePictureInput) profilePictureInput.value = '';
            if (imagePreview) imagePreview.style.display = 'none';
            if (currentAvatar) currentAvatar.style.display = 'block';
            if (cancelBtn) cancelBtn.style.display = 'none';
        });
    }
    
    // Validation du formulaire
    if (profilePictureForm) {
        profilePictureForm.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('profile_picture');
            if (fileInput && !fileInput.files[0]) {
                e.preventDefault();
                alert('Veuillez sélectionner une photo.');
                return;
            }
            
            if (fileInput && fileInput.files[0]) {
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (fileInput.files[0].size > maxSize) {
                    e.preventDefault();
                    alert('Le fichier est trop volumineux. Taille maximum: 10MB');
                    fileInput.value = '';
                }
            }
        });
    }
});