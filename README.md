# engage: Integrated Community Management & Engagement Platform

## Project Title
**engage: An Integrated Web-Based Community Management and User Engagement Platform**

---

## Detailed Description

### Purpose
Engage is a comprehensive, full-stack web application designed to facilitate community engagement, event management, and complaint resolution within an academic or organizational ecosystem. The platform serves as a centralized hub for users to participate in missions, attend events, provide feedback, and submit complaints while enabling administrators to manage, classify, and respond to community interactions.

### Objectives
- **Community Participation**: Enable users to engage in missions and events with comprehensive tracking and feedback mechanisms
- **Intelligent Request Management**: Implement AI-powered classification and auto-tagging for reclamations (complaints/requests)
- **Administrative Oversight**: Provide administrators with intuitive dashboards for managing users, events, missions, partnerships, and community feedback
- **Seamless Integration**: Create a unified platform that connects multiple functional domains (missions, events, store, partnerships, user management)
- **Data-Driven Insights**: Support decision-making through organized categorization, prioritization, and tagging of community requests

### Goals
1. Streamline user participation workflows across multiple engagement types
2. Automate complaint classification using natural language processing techniques
3. Provide transparent communication channels between community members and administrators
4. Enable secure, role-based access to administrative and user-facing features
5. Support scalability for growing community sizes and transaction volumes

---

## Features

### User-Facing Features (Frontoffice)
- **User Authentication & Profile Management**
  - Secure login/registration with session management
  - User profile editing with preference settings
  - Password management and security settings

- **Mission Management**
  - Browse available missions with detailed descriptions
  - Submit mission candidatures with automatic validation
  - View mission history and participation records
  - Real-time mission status tracking

- **Event Management**
  - Discover and register for upcoming events
  - View event details, dates, and participation history
  - Submit event feedback and reviews
  - Track event participation timeline

- **Complaint & Feedback System**
  - Submit reclamations (complaints/requests) with automatic AI classification
  - Real-time form field auto-completion based on description content
  - Automatic entity matching (missions, events, partners, users)
  - Submit general feedback and product reviews
  - Track reclamation status and responses

- **Store Integration**
  - Browse store items with detailed product information
  - Partner-specific store catalogs
  - Product recommendations

- **Partner Directory**
  - View available partnerships
  - Partner information and offerings

### Administrative Features (Backoffice)
- **Dashboard & Analytics**
  - System overview with key metrics
  - User management interface
  - Event scheduling and management

- **Reclamation Management**
  - AI-powered automatic classification (category, priority, department)
  - Auto-tagging system with keyword-based and AI fallback
  - Tag-based filtering for complaint analysis
  - Status tracking and workflow management
  - Response management and escalation

- **Mission Administration**
  - Create, edit, delete missions
  - View mission applications
  - Track mission participation and outcomes

- **Event Management**
  - Create and edit events with image uploads
  - Manage event schedules
  - Track event participation
  - View participation history

- **User Management**
  - User account administration
  - Role and permission assignment
  - User security settings

- **Partnership Management**
  - Create and manage partner accounts
  - Partner store inventory management
  - Partnership analytics

- **Store Management**
  - Item creation and editing
  - Inventory tracking
  - Partner-specific catalog management

- **Order Management**
  - Order tracking and fulfillment
  - Order history and analytics

- **Response Management**
  - Respond to user reclamations
  - Track response history

---

## Tech Stack

### Backend Technologies
- **PHP 7.4+**: Core server-side language with OOP programming paradigm
- **MySQL 5.7+**: Relational database management system for persistent data storage
- **PDO (PHP Data Objects)**: Database abstraction layer for secure SQL operations
- **cURL**: HTTP client for external API integrations (AI tagging services)

### Frontend Technologies
- **HTML5**: Semantic markup for structure and accessibility
- **CSS3**: Styling with CSS custom properties (variables) for theming
- **Bootstrap 4**: Responsive design framework and component library
- **JavaScript (ES6+)**: Client-side interactivity with vanilla JavaScript (no framework dependencies)
- **Font Awesome 6.5.0**: Icon library for UI enhancements

### Architecture & Patterns
- **MVC (Model-View-Controller)**: Separation of concerns across application layers
- **OOP (Object-Oriented Programming)**: Class-based design for reusability and maintainability
- **RESTful Principles**: Stateless HTTP operations following REST conventions
- **Middleware Pattern**: Session authentication and authorization checks

### AI & NLP Components
- **ReclamationClassifier**: Keyword-based natural language processing for complaint categorization
- **AutoTagger**: External AI API integration with keyword-based fallback strategy
- **Token-Based Matching**: Entity name extraction and matching from user descriptions

### Server Requirements
- Apache/Nginx web server with `.htaccess` support
- PHP with `curl` and `pdo_mysql` extensions
- XAMPP or similar local development environment (for development)

---

## Project Structure

```
projetweb2/
├── controller/
│   ├── AutoTagger.php                  # AI tag extraction utility
│   ├── ReclamationClassifier.php       # Smart reclamation classification
│   ├── ReclamationController.php       # Reclamation CRUD + tagging
│   ├── AdminOrderController.php        # Order management
│   ├── AdminPartenaireController.php   # Partner administration
│   ├── AdminStoreController.php        # Store management
│   ├── condidaturecontroller.php       # Candidature handling
│   ├── evenementController.php         # Event management
│   ├── feedbackcontroller.php          # Feedback processing
│   ├── LikeController.php              # Like/rating system
│   ├── missioncontroller.php           # Mission management
│   ├── PartenaireController.php        # Partner operations
│   ├── participationController.php     # Event participation
│   ├── ResponseController.php          # Response to reclamations
│   ├── StoreController.php             # Store operations
│   └── utilisateurcontroller.php       # User management
│
├── model/
│   ├── Reclamation.php                 # Reclamation data model
│   ├── condidature.php                 # Candidature model
│   ├── evenementModel.php              # Event model
│   ├── feedback.php                    # Feedback model
│   ├── mission.php                     # Mission model
│   ├── Partenaire.php                  # Partner model
│   ├── participationModel.php          # Participation model
│   ├── StoreItem.php                   # Store item model
│   └── utilisateur.php                 # User model
│
├── view/
│   ├── backoffice/                     # Admin interface
│   │   ├── connexion.php               # Admin login
│   │   ├── dashboard.php               # Admin dashboard
│   │   ├── inscription.php             # Admin registration
│   │   ├── profile.php                 # Admin profile
│   │   ├── securite.php                # Security settings
│   │   ├── settings.php                # System settings
│   │   ├── reclamation/                # Reclamation management
│   │   │   ├── listReclamation.php     # List all reclamations
│   │   │   └── tags_partial.php        # Tag rendering component
│   │   ├── mission/                    # Mission administration
│   │   ├── events/                     # Event administration
│   │   ├── orders/                     # Order management
│   │   ├── partenaire/                 # Partner management
│   │   ├── store/                      # Store administration
│   │   ├── utilisateur/                # User administration
│   │   ├── assets/
│   │   │   ├── css/
│   │   │   │   ├── custom-backoffice.css
│   │   │   │   └── tags.css            # Tag styling
│   │   │   ├── js/
│   │   │   │   └── tags.js             # Tag filtering logic
│   │   │   ├── img/
│   │   │   └── uploads/
│   │   └── webfonts/
│   │
│   ├── frontoffice/                    # User interface
│   │   ├── index.php                   # Home page
│   │   ├── connexion.php               # User login
│   │   ├── inscription.php             # User registration
│   │   ├── profile.php                 # User profile
│   │   ├── header_common.php           # Common header
│   │   ├── addreclamation.php          # Submit reclamation
│   │   ├── addcondidature.php          # Submit candidature
│   │   ├── missionlist.php             # Mission browser
│   │   ├── missiondetails.php          # Mission details
│   │   ├── store.php                   # Store browser
│   │   ├── assets/
│   │   │   ├── css/
│   │   │   │   └── custom-frontoffice.css
│   │   │   ├── js/
│   │   │   ├── img/
│   │   │   ├── partenaire/             # Partner assets
│   │   │   ├── store/                  # Store assets
│   │   │   └── storepartenaireassets/
│   │   └── events/                     # Event pages
│   │
│   ├── fonts/                          # Font files
│   └── img/                            # Shared images
│
├── migrations/
│   └── create_likes_table.sql          # Database migrations
│
├── db_config.php                       # Database configuration
├── projetweb3.sql                      # Database schema dump
├── README.md                           # This file
└── [Additional setup scripts]
```

---

## Installation / Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server (with `mod_rewrite` for URL rewriting)
- XAMPP or equivalent local development environment
- cURL extension enabled in PHP (for AI tagging)

### Step 1: Clone/Download Repository
```bash
# Navigate to web root
cd C:\xampp\htdocs

# Clone or extract project
git clone [repository-url] projetweb2
# OR
unzip projetweb2.zip
```

### Step 2: Database Setup
```bash
# 1. Start XAMPP services (Apache + MySQL)
# 2. Access phpMyAdmin: http://localhost/phpmyadmin/

# 3. Create database
CREATE DATABASE projetweb2;
USE projetweb2;

# 4. Import SQL schema
SOURCE C:\xampp\htdocs\projetweb2\projetweb3.sql;

# 5. Run migration scripts (if needed)
SOURCE C:\xampp\htdocs\projetweb2\migrations\create_likes_table.sql;
```

### Step 3: Database Configuration
```bash
# Edit database credentials in db_config.php
nano/vim view/../../db_config.php

# Update these values:
# - DB_HOST: localhost
# - DB_USER: root (default for XAMPP)
# - DB_PASS: [your mysql password, usually empty for XAMPP]
# - DB_NAME: projetweb2
```

### Step 4: File Permissions
```bash
# Ensure write permissions for upload directories
chmod 755 view/backoffice/assets/uploads/
chmod 755 view/frontoffice/assets/uploads/
chmod 755 view/backoffice/assets/img/
```

### Step 5: Start Application
```bash
# Start XAMPP Control Panel
# Ensure Apache and MySQL are running

# Open browser and navigate to:
http://localhost/projetweb2/view/frontoffice/index.php
```

### Step 6: Initial Configuration
1. **Create admin account**: Navigate to `/view/backoffice/inscription.php`
2. **Configure AI tagging** (optional):
   - Set `AI_TAG_API_URL` and `AI_TAG_API_KEY` environment variables
   - System falls back to keyword-based tagging if AI is unavailable
3. **Upload test data**: Use migration scripts or import from `projetweb3.sql`

---

## Usage / Demo Instructions

### For End Users (Frontoffice)

#### 1. Register & Login
- Navigate to: `http://localhost/projetweb2/view/frontoffice/inscription.php`
- Fill registration form with email, password, name
- Login at: `http://localhost/projetweb2/view/frontoffice/connexion.php`

#### 2. Browse Missions
- Home page displays available missions
- Click "Détails" to view mission specifications
- Click "Candidater" to submit a mission application
- View application history in user profile

#### 3. Submit a Reclamation
- Navigate to: `Déposer une Réclamation` from main menu
- **Key Feature**: As you type the description, the AI classifier automatically:
  - Suggests complaint category (mission, event, partner, user, technical, etc.)
  - Suggests priority level (urgent, high, medium, low)
  - Suggests responsible department
  - Auto-selects related entities (missions, events, partners, users) by name matching
- Review AI suggestions in the preview box
- Submit form to create reclamation with automatic tagging

#### 4. View Events & Participate
- Browse upcoming events on home page or events section
- Register for events with one click
- View event details including date, description, and participants
- Submit feedback on completed events

#### 5. Access Store
- Browse available products in partner stores
- View product details and pricing
- [E-commerce checkout functionality - *placeholder for future feature*]

### For Administrators (Backoffice)

#### 1. Login to Dashboard
- Navigate to: `http://localhost/projetweb2/view/backoffice/connexion.php`
- Use admin credentials created during setup
- Access admin dashboard: `dashboard.php`

#### 2. Manage Reclamations
- Navigate to: **Reclamations > Liste des Réclamations**
- **View Reclamations**: Each complaint shows:
  - Subject and description
  - AI-assigned category, priority, department
  - Auto-generated tags for quick filtering
  - Status and response information
- **Filter by Tags**: Click any tag pill to filter complaints by that tag
  - Tags like `delivery-delay`, `payment-issue`, `bug-report`, etc.
- **Respond to Reclamation**: 
  - Click "Répondre" to add a response
  - Update status (Non traité → En cours → Résolu)
  - Categorization is automatic on initial submission

#### 3. Create & Manage Events
- Navigate to: **Événements > Créer un Événement**
- Fill event details (title, date, description, image)
- Image upload with automatic validation
- Edit event details anytime
- View participation history and analytics

#### 4. Manage Missions
- Navigate to: **Missions > Ajouter une Mission**
- Create missions with title, description, requirements
- View mission candidatures and approve/reject applications
- Edit or delete missions as needed

#### 5. Manage Users & Partners
- Navigate to: **Utilisateurs** or **Partenaires**
- Create new user/partner accounts
- Edit user profiles and permissions
- Deactivate or remove users/partners
- View user activity logs

#### 6. Store & Order Management
- Navigate to: **Magasin** to manage inventory
- Create store items with partner associations
- Track orders and fulfillment status
- Manage inventory levels

### Demo Scenario

**Scenario**: A user submits a complaint about delayed delivery from a partner

1. **User Action**:
   - Opens frontoffice and navigates to "Déposer une Réclamation"
   - Types: "The partenaire esports isn't doing his job, delivery is 3 weeks late"
   - AI classifier suggests: Category=`partenaire`, Priority=`urgent`, Department=`logistics`
   - Auto-selects "esports" partner in the "Partenaire Concerné" field
   - Submits form
   - Green success message: "Réclamation envoyée avec succès!" appears for 5 seconds
   - **Form automatically clears**

2. **Backend Processing**:
   - `ReclamationController::addReclamation()` saves complaint to DB
   - `AutoTagger::extractTags()` generates tags: `[delivery-delay, urgent, partner, complaint]`
   - Tags stored in `reclamation_tags` table with automatic indexing

3. **Admin Review**:
   - Admin opens backoffice Reclamations list
   - New complaint appears with tags: `delivery-delay`, `urgent`, `partner`, `complaint`
   - Admin clicks `urgent` tag → list filters to show only urgent complaints
   - Admin clicks complaint to view full details
   - Admin clicks "Répondre" to send response to user
   - Updates status to "En cours"

4. **User Notification**:
   - User can check profile to see reclamation status
   - Receives response from admin
   - Views tag-based categorization for transparency

---

## Database Schema Highlights

### Core Tables
- **utilisateur**: User accounts with authentication credentials
- **mission**: Mission listings and metadata
- **evenement**: Event information and scheduling
- **partenaires**: Partner organization data
- **reclamation**: Complaint/feedback submissions with AI-classified fields
- **reclamation_tags**: Tag-to-reclamation many-to-many relationship
- **response**: Admin responses to reclamations
- **participation**: Event participation tracking
- **store_items**: Product inventory
- **orders**: Order tracking and fulfillment
- **likes**: User engagement metrics

### Key Foreign Keys
- `reclamation.utilisateur_id` → `utilisateur.id_util`
- `reclamation_tags.reclamation_id` → `reclamation.id_reclamation`
- `participation.id_utilisateur` → `utilisateur.id_util`
- `response.reclamation_id` → `reclamation.id_reclamation`

---

## Author / Contributors

### Project Development
- **les phenomenes**: Full-stack application development, AI integration, database design, project creation and leadership
- **[Additional Contributors]**: Frontend design, UX optimization, testing, documentation

### Academic Institution
- **University/School**: [Institution Name]
- **Department**: [Department Name]
- **Program**: [Program/Course Name]
- **Academic Year**: [2024-2025 or applicable year]

### Acknowledgments
- Bootstrap Framework team for responsive UI components
- Font Awesome for icon resources
- PHP community for excellent documentation and tools
- [AI API provider, if applicable] for intelligent classification services

---

## License

### Academic License
This project is made available under the **[INSERT LICENSE - e.g., MIT, Apache 2.0, Creative Commons Attribution 4.0]** for educational and non-commercial use.

**Terms of Use:**
- ✅ **Permitted**: Educational use, research, personal projects, academic institution deployments
- ❌ **Prohibited**: Commercial distribution without explicit permission, removal of attribution
- ⚠️ **Required**: Proper attribution and acknowledgment of original authors

### Third-Party Libraries
- **Bootstrap 4**: [MIT License](https://github.com/twbs/bootstrap/blob/main/LICENSE)
- **Font Awesome**: [CC BY 4.0 for icons](https://fontawesome.com/license)
- **PHP PDO**: [PHP License](https://www.php.net/license/)

### Citation
If using this project in academic work, please cite as:
```bibtex
@software{engage_2025,
  title = {engage: Integrated Community Management Platform},
  author = {les phenomenes},
  year = {2025},
  url = {[Repository URL]}
}
```

---

## Support & Documentation

### Getting Help
- Review project documentation in `/docs` directory (if available)
- Check database schema in `projetweb3.sql` for table structures
- Examine controller files for API implementation details
- Review model classes for data validation rules

### Common Issues & Solutions
| Issue | Solution |
|-------|----------|
| Database connection fails | Verify MySQL is running, check `db_config.php` credentials |
| 404 errors on pages | Ensure Apache `mod_rewrite` is enabled, check `.htaccess` files |
| Images not uploading | Verify folder permissions: `chmod 755 /uploads` |
| AI tagging not working | System uses keyword fallback; check `AI_TAG_API_URL` env variable |
| Session timeout | Adjust `session.gc_maxlifetime` in PHP configuration |

### Development Roadmap (Future Features)
- [ ] Email notification system for complaint responses
- [ ] Advanced analytics dashboard with charts/graphs
- [ ] Mobile application (React Native/Flutter)
- [ ] Real-time chat system for support
- [ ] Machine learning model training with historical complaint data
- [ ] Multi-language support (i18n)
- [ ] SSO integration (OAuth 2.0, LDAP)

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 2.1.0 | December 2024 | Added AI-based auto-tagging, improved reclamation filtering |
| 2.0.5 | November 2024 | Fixed form styling, enhanced user feedback system |
| 2.0.0 | October 2024 | Complete application refactor, MVC architecture implementation |
| 1.0.0 | September 2024 | Initial project release |

---

**Last Updated**: December 2025
**Status**: Active Development  
**Created by**: les phenomenes  
**Maintenance**: les phenomenes

For questions or contributions, please contact: [contact-email or repository issues page]
