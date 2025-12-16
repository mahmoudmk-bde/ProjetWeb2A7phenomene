// Gestion du menu déroulant de la sidebar
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const gestionSubmenu = document.getElementById('gestion-submenu');
    
    if (dropdownToggle && gestionSubmenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const isOpen = gestionSubmenu.classList.contains('show');
            
            if (isOpen) {
                gestionSubmenu.classList.remove('show');
            } else {
                gestionSubmenu.classList.add('show');
            }
            
            // Mettre à jour l'icône
            const icon = dropdownToggle.querySelector('i.fas');
            if (icon) {
                icon.className = isOpen ? 'fas fa-chevron-right' : 'fas fa-chevron-down';
            }
        });
    }
    
    // Recherche en temps réel dans le tableau
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.data-table tr');
            
            rows.forEach((row, index) => {
                if (index === 0) return; // Ignorer l'en-tête
                
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Confirmation de suppression
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
                e.preventDefault();
            }
        });
    });
    
    // Menu mobile
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }
});