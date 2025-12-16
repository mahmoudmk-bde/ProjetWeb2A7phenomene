document.addEventListener('DOMContentLoaded', function() {
    // Gestion du menu déroulant de la sidebar
    const dropdownToggle = document.querySelector('.dropdown-toggle');
    const gestionSubmenu = document.getElementById('gestion-submenu');
    
    if (dropdownToggle && gestionSubmenu) {
        let isSubmenuOpen = gestionSubmenu.classList.contains('show');
        
        function updateDropdownIcon() {
            const icon = dropdownToggle.querySelector('i.fas');
            if (icon) {
                icon.className = isSubmenuOpen ? 'fas fa-chevron-down' : 'fas fa-chevron-right';
            }
        }
        
        updateDropdownIcon();
        
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            isSubmenuOpen = !isSubmenuOpen;
            
            if (isSubmenuOpen) {
                gestionSubmenu.classList.add('show');
            } else {
                gestionSubmenu.classList.remove('show');
            }
            
            updateDropdownIcon();
        });
    }
    
    // Gestion de l'état actif des liens
    const currentPage = window.location.pathname.split('/').pop();
    const menuLinks = document.querySelectorAll('#gestion-submenu a');
    
    menuLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
});