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