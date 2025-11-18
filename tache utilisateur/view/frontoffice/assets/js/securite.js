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

// Auto-masquer les messages après 5 secondes
setTimeout(function() {
    const alertMessages = document.querySelectorAll('.alert-message');
    alertMessages.forEach(function(alert) {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.5s ease';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);