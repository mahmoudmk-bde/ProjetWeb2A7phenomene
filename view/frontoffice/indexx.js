// Gestion du chargement des images
document.addEventListener('DOMContentLoaded', function() {
    initializePage();
    setupImageLoading();
    setupCardAnimations();
    setupNavigation();
});

// Initialisation de la page
function initializePage() {
    console.log('Page Quiz Engage initialisée');
    
    // Vérifier si des articles sont disponibles
    const cards = document.querySelectorAll('.card');
    if (cards.length === 0) {
        showNoArticlesMessage();
    }
}

// Configuration du chargement des images - VERSION SIMPLIFIÉE
function setupImageLoading() {
    const images = document.querySelectorAll('.thumb');
    
    images.forEach(img => {
        // Marquer toutes les images comme chargées (puisque nous utilisons CSS)
        img.classList.add('loaded');
        
        // Vérifier si l'image existe vraiment
        const testImage = new Image();
        testImage.onload = function() {
            // L'image existe, laisser le navigateur la charger normalement
            img.src = img.src;
        };
        testImage.onerror = function() {
            // L'image n'existe pas, utiliser le style CSS de remplacement
            img.classList.add('error');
            console.warn('Image non trouvée, utilisation du style CSS:', img.alt);
        };
        testImage.src = img.src;
    });
}

function handleImageError(img) {
    img.classList.remove('loading');
    img.classList.add('error');
    if (!img.src.includes('default.png')) {
        img.src = 'image/default.png';
    }
    console.warn('Image non trouvée, utilisation par défaut:', img.alt);
}

// Animations des cartes
function setupCardAnimations() {
    const cards = document.querySelectorAll('.card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => {
        observer.observe(card);
    });
}

// Navigation et interactions
function setupNavigation() {
    // Gestion du clic sur les cartes
    const cardLinks = document.querySelectorAll('.card-link');
    cardLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const card = this.querySelector('.card');
            card.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                card.style.transform = '';
            }, 150);
        });
    });
    
    // Header scroll effect
    let lastScrollY = window.scrollY;
    const header = document.querySelector('.main_menu');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.style.background = 'linear-gradient(135deg, rgba(31, 34, 53, 0.95) 0%, rgba(36, 38, 59, 0.95) 100%)';
            header.style.backdropFilter = 'blur(10px)';
        } else {
            header.style.background = 'linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%)';
            header.style.backdropFilter = 'none';
        }
        
        lastScrollY = window.scrollY;
    });
}

// Message quand aucun article n'est disponible
function showNoArticlesMessage() {
    const grid = document.querySelector('.grid');
    const noArticles = document.createElement('div');
    noArticles.className = 'no-articles enhanced';
    noArticles.innerHTML = `
        <div class="no-articles-content">
            <i class="fas fa-newspaper"></i>
            <h3>Aucun article disponible</h3>
            <p>Revenez plus tard pour découvrir de nouveaux quiz !</p>
        </div>
    `;
    grid.appendChild(noArticles);
}

// Gestion du responsive
function handleResize() {
    const grid = document.querySelector('.grid');
    const cards = document.querySelectorAll('.card');
    
    if (window.innerWidth < 768) {
        cards.forEach(card => {
            card.style.animationDelay = '0s';
        });
    }
}

// Événement de redimensionnement
window.addEventListener('resize', handleResize);

// Preload des images importantes
function preloadCriticalImages() {
    const criticalImages = [
        'image/default.png',
        'image/sport.png',
        'image/education.png'
    ];
    
    criticalImages.forEach(src => {
        const img = new Image();
        img.src = src;
    });
}

// Initialiser le preload
preloadCriticalImages();