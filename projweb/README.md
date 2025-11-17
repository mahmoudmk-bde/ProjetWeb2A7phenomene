# ENGAGE - Gamification Module

Plateforme de matchmaking pour le volontariat par le jeu vidéo.

## 📋 Description

**Module Gamification** - Gestion des partenaires et du store de jeux.

### Fonctionnalités

- ✅ **Gestion Partenaires** (Sponsors, Testeurs, Vendeurs)
- ✅ **Store de Jeux** (Listing, Détails, Catégories)
- ✅ **Admin Dashboard** (CRUD complet)
- ✅ **Système de Stock** (Gestion inventaire)
- ✅ **Upload d'Images** (Logos, Images jeux)
- ✅ **Validation Formulaires** (Client + Serveur)

---

## 🗂️ Structure du Projet

```
c:\Users\GIGABYTE\Desktop\proj web gam\
├── index.php                    # Point d'entrée principal
├── helpers.php                  # Fonctions utilitaires
├── schema.sql                   # Structure base de données
│
├── config/
│   └── database.php             # Configuration BDD
│
├── models/
│   ├── Partenaire.php           # Modèle Partenaire
│   └── StoreItem.php            # Modèle Article Store
│
├── controller/
│   ├── PartenaireController.php      # Front - Partenaires
│   ├── StoreController.php           # Front - Store
│   ├── AdminPartenaireController.php # Admin - Partenaires
│   └── AdminStoreController.php      # Admin - Store
│
├── view/
│   ├── frontoffice/
│   │   ├── partenaire/
│   │   │   ├── list.php         # Liste partenaires
│   │   │   └── profile.php      # Profil partenaire
│   │   └── store/
│   │       ├── index.php        # Store listing
│   │       └── item-detail.php  # Détail jeu
│   │
│   └── backoffice/
│       ├── partenaire/
│       │   ├── list.php         # Gestion partenaires
│       │   ├── create.php       # Créer partenaire
│       │   └── edit.php         # Modifier partenaire
│       └── store/
│           ├── items-list.php   # Gestion jeux
│           ├── items-create.php # Créer jeu
│           └── items-edit.php   # Modifier jeu
│
└── assets/
    ├── css/
    │   ├── gam.css              # Custom CSS
    │   └── *.css                # Bootstrap, Font Awesome, etc.
    ├── js/
    │   ├── partenaire-form.js   # Validation formulaires
    │   └── *.js                 # jQuery, Bootstrap, etc.
    ├── img/                     # Images frontoffice
    ├── webfonts/                # Polices
    └── backoffice/
        ├── css/                 # Styles backoffice
        ├── img/                 # Images admin
        └── webfonts/            # Polices
```

---

## 🗄️ Base de Données

### Tables Principales

#### `partenaires`
```sql
- id (INT PK)
- nom (VARCHAR 255)
- logo (VARCHAR 255)
- type (ENUM: sponsor, testeur, vendeur)
- statut (ENUM: actif, inactif, en_attente)
- description (LONGTEXT)
- email (VARCHAR 255)
- telephone (VARCHAR 20)
- site_web (VARCHAR 255)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### `store_items`
```sql
- id (INT PK)
- partenaire_id (INT FK)
- nom (VARCHAR 255)
- prix (DECIMAL 10,2)
- stock (INT)
- categorie (VARCHAR 100)
- image (VARCHAR 255)
- description (LONGTEXT)
- plateforme (VARCHAR 100)
- age_minimum (INT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

---

## 🚀 Installation

### 1. **Configurer la Base de Données**

```bash
# Ouvrir phpMyAdmin ou MySQL CLI
mysql -u root -p

# Exécuter le script
mysql> source schema.sql;
```

Ou importer `schema.sql` via phpMyAdmin.

### 2. **Configurer la Connexion BD**

Éditer `config/database.php` :
```php
private $host = "localhost";
private $db_name = "engage_db";
private $username = "root";
private $password = "";
```

### 3. **Accéder au Projet**

```
Frontend: http://localhost/proj%20web%20gam/
Admin:    http://localhost/proj%20web%20gam/?controller=AdminPartenaire&action=index
```

---

## 🔗 URLs Principales

### Frontend

| URL | Description |
|-----|-------------|
| `/?controller=Store&action=index` | Page d'accueil - Store |
| `/?controller=Store&action=show&id=1` | Détail d'un jeu |
| `/?controller=Partenaire&action=index` | Liste partenaires |
| `/?controller=Partenaire&action=show&id=1` | Profil partenaire |

### Backend

| URL | Description |
|-----|-------------|
| `/?controller=AdminPartenaire&action=index` | Gestion partenaires |
| `/?controller=AdminPartenaire&action=create` | Créer partenaire |
| `/?controller=AdminPartenaire&action=edit&id=1` | Modifier partenaire |
| `/?controller=AdminStore&action=index` | Gestion jeux |
| `/?controller=AdminStore&action=create` | Créer jeu |
| `/?controller=AdminStore&action=edit&id=1` | Modifier jeu |

---

## 🎨 Palette de Couleurs

```css
--primary-color: #ff4a57      /* Rouge/Rose principal */
--secondary-color: #1f2235    /* Gris foncé arrière-plan */
--accent-color: #24263b       /* Gris plus clair */
--text-color: #ffffff         /* Blanc texte */
```

---

## 📦 Dépendances Externes

- **Bootstrap 4.x** - Framework CSS
- **jQuery 1.12.1** - JavaScript
- **Font Awesome 5.x** - Icônes
- **Owl Carousel** - Carrousels
- **Magnific Popup** - Modals/Lightbox
- **Swiper/Slick** - Sliders

---

## ✅ Checklist Développement

- [x] Architecture MVC
- [x] Modèles (Partenaire, StoreItem)
- [x] Contrôleurs (Front + Admin)
- [x] Vues Frontoffice (List, Profile, Store, Detail)
- [x] Vues Backoffice (List, Create, Edit pour Partenaires & Items)
- [x] CSS personnalisé (gam.css)
- [x] JavaScript validation (partenaire-form.js)
- [x] Helpers functions
- [x] Schema SQL
- [x] Upload fichiers
- [x] Gestion erreurs/session

---

## 🔐 Sécurité

- ✅ Validation serveur des formulaires
- ✅ Échappement HTML (htmlspecialchars)
- ✅ Prepared statements PDO (injection SQL)
- ✅ Vérification type fichier (upload)
- ✅ Limite taille fichier (2MB logos, 5MB images)

**À améliorer :**
- [ ] CSRF tokens
- [ ] Authentification admin
- [ ] Hachage des mots de passe
- [ ] Rate limiting

---

## 🎯 Fonctionnalités Futures

- [ ] Système de panier/commandes
- [ ] Paiement en ligne
- [ ] Avis et notes (ratings)
- [ ] Wishlist/Favoris
- [ ] Système de commentaires
- [ ] Filtrages avancés
- [ ] Pagination
- [ ] API REST
- [ ] Cache système
- [ ] Analytics

---

## 📞 Support

Pour toute question ou problème, consultez la documentation du projet ou contactez l'équipe de développement.

---

## 📄 License

Projet ENGAGE - Tous droits réservés © 2025
