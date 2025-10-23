# 🦷 Cabinet Dupont - Dental Practice Management System

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.2-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-10.4-orange.svg)
![Tests](https://img.shields.io/badge/tests-18%20passing-brightgreen.svg)
![Status](https://img.shields.io/badge/status-stable-success.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Complete web application for dental practice management developed in native PHP with MVC architecture.

## 🌐 Live Website

The website is currently **deployed and accessible online** at:

### 🔗 **[https://dupontcare.wuaze.com](https://dupontcare.wuaze.com)**

**Hosting:** InfinityFree (free hosting)  
**Status:** ✅ In production  
**SSL/HTTPS:** ✅ Active SSL certificate  
**Database:** MySQL (sql210.infinityfree.com)

> **Note:** The website was successfully migrated from a local environment (XAMPP) to InfinityFree in October 2025. All features are operational in production.

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Technologies Used](#-technologies-used)
- [Prerequisites](#-prerequisites)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Project Structure](#-project-structure)
- [Usage](#-usage)
- [Test Accounts](#-test-accounts)
- [Quality and Testing](#-quality-and-testing)
- [Contributing](#-contributing)
- [Links](#-links)
- [Support](#-support)

## 🎯 Overview

Cabinet Dupont is a modern and intuitive solution for comprehensive dental practice management. It allows managing appointments, patients, services, news, and opening hours through a responsive web interface.

### Screenshots

- **Homepage**: Practice presentation, services, hours
- **Patient area**: Online appointment booking
- **Admin area**: Complete practice management
- **Schedule**: Interactive calendar view with drag & drop

## ✨ Features

### For Patients
- 📅 **Online appointment booking** with service selection
- 👤 **Profile management** (personal information, history)
- 📰 **News consultation** from the practice
- 🕐 **Opening hours visualization**
- 📱 **Responsive interface** (mobile, tablet, desktop)

### For Administrator
- 📊 **Centralized admin dashboard**
- 🗓️ **Interactive planning** with weekly/monthly view
- 👥 **Patient management** (complete CRUD)
- 💼 **Service management** (pricing, duration, colors)
- 📝 **News management** (creation, modification, publication)
- ⏰ **Practice hours configuration**
- 🎨 **Automatic slot generation**
- 📋 **Bulk actions** on slots

### Advanced Features
- 🔐 **Secure authentication system** (CSRF, sessions)
- 🎨 **Customizable theme** per service (colors)
- 📧 **Data validation** client and server-side
- 🔍 **Search and sort** in tables
- 💾 **Automatic database backup**
- ♿ **Accessibility** (ARIA, visible focus, keyboard navigation)

## 🛠️ Technologies Used

### Backend
- **PHP 8.2** - Server language
- **MySQL 10.4** (MariaDB) - Database
- **PDO** - Secure database connection
- **MVC Architecture** - Code organization

### Frontend
- **HTML5** - Semantic structure
- **CSS3** - Modern styles (Grid, Flexbox, animations)
- **JavaScript ES6+** - Interactivity
- **Lottie** - Vector animations
- **FontAwesome 6** - Icons

### Tools
- **XAMPP** - Development environment
- **Git** - Version control
- **phpMyAdmin** - Database administration

## 📦 Prerequisites

- **XAMPP** (or equivalent) with:
  - PHP >= 8.2
  - MySQL/MariaDB >= 10.4
  - Apache >= 2.4
- **Git** (to clone the project)
- Modern web browser (Chrome, Firefox, Edge, Safari)

## 🚀 Installation

### 1. Clone the project

```bash
# Via HTTPS
git clone https://github.com/Wandalf-dev/CabinetDupont.git

# Via SSH (if configured)
git clone git@github.com:Wandalf-dev/CabinetDupont.git
```

### 2. Place the project in XAMPP folder

```bash
# Windows
C:\xampp\htdocs\CabinetDupont

# Linux/Mac
/opt/lampp/htdocs/CabinetDupont
```

### 3. Start XAMPP services

1. Open **XAMPP Control Panel**
2. Start **Apache**
3. Start **MySQL**

### 4. Create the database

**Option A: Via phpMyAdmin (Graphical interface)**

1. Access [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Create a new database named `bdd_dupont`
3. Select the database
4. Click **Import**
5. Choose the file `Backup/bdd_dupont.sql`
6. Click **Execute**

**Option B: Via command line**

```bash
# Windows (PowerShell)
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS bdd_dupont CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
& "C:\xampp\mysql\bin\mysql.exe" -u root bdd_dupont < Backup/bdd_dupont.sql

# Linux/Mac
mysql -u root -e "CREATE DATABASE IF NOT EXISTS bdd_dupont CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root bdd_dupont < Backup/bdd_dupont.sql
```

### 5. Configure database connection

Check the file `app/config/database.php`:

```php
<?php
return [
    'host' => 'localhost',
    'dbname' => 'bdd_dupont',
    'username' => 'root',
    'password' => '', // Leave empty by default with XAMPP
    'charset' => 'utf8mb4'
];
```

### 6. Access the application

Open a browser and go to:
- **Homepage**: [http://localhost/CabinetDupont](http://localhost/CabinetDupont)
- **Login**: [http://localhost/CabinetDupont/auth/login](http://localhost/CabinetDupont/auth/login)

## ⚙️ Configuration

### Base URL Configuration

The file `config/config.php` automatically detects the environment (local vs production):

```php
<?php
// Automatic detection
$isLocal = ($host === 'localhost' || strpos($host, '127.0.0.1') !== false);

if ($isLocal) {
    // Local (XAMPP)
    define('BASE_URL', $protocol . '://' . $host . '/cabinetdupont-1');
} else {
    // Production (InfinityFree)
    define('BASE_URL', $protocol . '://' . $host);
}
```

If your local folder has a different name, adjust the `BASE_URL` line for local.

## 🚀 Production Deployment (Migration to InfinityFree)

The site was successfully migrated from a local environment to InfinityFree free hosting. Here's the complete process:

### Step 1: Hosting Preparation

1. **Create an account on [InfinityFree](https://infinityfree.com)**
2. **Create a website** with chosen subdomain (e.g., `dupontcare.wuaze.com`)
3. **Create a MySQL database** via control panel
   - Name: `if0_40207543_bdd_dupont`
   - Host: `sql210.infinityfree.com`
   - User: Provided by InfinityFree
   - Password: Provided by InfinityFree

### Step 2: File Configuration

1. **Update `app/config/database.php`** with production credentials:
```php
<?php
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isLocal = ($host === 'localhost' || strpos($host, '127.0.0.1') !== false);

if ($isLocal) {
    return [
        'host' => 'localhost',
        'dbname' => 'bdd_dupont',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];
} else {
    return [
        'host' => 'sql210.infinityfree.com',
        'dbname' => 'if0_40207543_bdd_dupont',
        'username' => 'if0_40207543',
        'password' => 'YOUR_PASSWORD',
        'charset' => 'utf8mb4'
    ];
}
```

2. **Check `config/config.php`** for automatic environment detection

### Step 3: File Upload

1. **Connect via FTP** (FileZilla recommended)
   - Host: `ftpupload.net`
   - User: InfinityFree account
   - Port: 21
2. **Upload all files** to `htdocs/` folder
3. **Check permissions** of `public/uploads/` folder (chmod 755)

### Step 4: Database Import

1. **Access phpMyAdmin** from InfinityFree panel
2. **Select the database**
3. **Import the file** `Backup/if0_40207543_bdd_dupont.sql`
4. **Verify** that all tables are created

### Step 5: Post-migration Tests

- ✅ Homepage accessible
- ✅ Admin login functional
- ✅ CSS/JS loading
- ✅ Lottie animations displayed
- ✅ Images loaded from `/assets/`
- ✅ Booking system operational
- ✅ Schedule agenda functional
- ✅ Image upload operational

### Common Issues and Solutions

#### Issue 1: Case-sensitive paths
**Symptom:** Error "Class App\Core\App not found"  
**Solution:** Linux servers are case-sensitive. Verify that:
- File names exactly match class names
- `Database.php` (not `database.php`)
- Lowercase paths: `app/core/App.php`

#### Issue 2: Images/CSS not loading
**Symptom:** Broken display, missing images  
**Solution:** Verify all paths use `<?php echo BASE_URL; ?>` instead of hardcoded paths

#### Issue 3: Database connection error
**Symptom:** "Connection failed: Access denied"  
**Solution:** Verify credentials in `app/config/database.php`

### InfinityFree Performance and Limitations

- ✅ **Free SSL/HTTPS** (Let's Encrypt)
- ✅ **Unlimited disk space**
- ✅ **Unlimited bandwidth**
- ⚠️ **50,000 hits/day limit**
- ⚠️ **Inactivity timeout**: Site may be suspended after several days of inactivity
- ⚠️ **Performance**: Slower than paid hosting

### Path Configuration

Paths are automatically configured in `config.php`. Verify that:

```php
define('BASE_URL', '/CabinetDupont');
define('ROOT_PATH', __DIR__);
```

### Folder Permissions

Ensure the `public/uploads/` folder is writable:

```bash
# Linux/Mac
chmod -R 755 public/uploads

# Windows: Properties > Security > Edit permissions
```

## 📁 Project Structure

```
CabinetDupont/
├── app/
│   ├── config/              # Configuration (database)
│   ├── controllers/         # MVC Controllers
│   ├── core/               # Core classes (App, Controller, Model, Csrf, Utils)
│   ├── models/             # Data models
│   └── views/              # Views (HTML/PHP templates)
│       ├── actu/           # News
│       ├── admin/          # Administration
│       ├── agenda/         # Schedule
│       ├── auth/           # Authentication
│       ├── creneaux/       # Time slots
│       ├── error/          # Error pages
│       ├── horaires/       # Hours
│       ├── patient/        # Patients
│       ├── rendezvous/     # Appointments
│       ├── service/        # Services
│       ├── templates/      # Reusable templates (header, footer)
│       └── user/           # User profile
├── assets/                 # Resources (Lottie JSON)
├── Backup/                 # SQL backups
├── css/                    # Stylesheets
│   ├── base/              # Base styles
│   ├── components/        # Reusable components
│   ├── layouts/           # Layouts (header, footer)
│   ├── modules/           # Specific modules
│   ├── pages/             # Specific pages
│   └── utils/             # Utilities
├── js/                     # JavaScript scripts
│   ├── components/        # JS components
│   ├── modules/           # JS modules (agenda, slots, etc.)
│   ├── pages/             # Scripts per page
│   └── utils/             # Utility functions
├── public/
│   └── uploads/           # Uploaded images (services, news)
├── .htaccess              # Apache configuration
├── config.php             # Global configuration
├── index.php              # Entry point
└── README.md              # This file
```

## 📖 Usage

### Login

#### As Patient
1. Go to [/auth/login](http://localhost/CabinetDupont/auth/login)
2. Create an account or use a test account
3. Access patient area

#### As Administrator
1. Log in with an administrator account
2. Access admin panel via menu

### Appointment Booking (Patient)

1. **Login** → Log in or create account
2. **Select service** → Choose consultation type
3. **Choose date** → Select available slot
4. **Confirm** → Validate appointment

### Schedule Management (Administrator)

1. **Access schedule** → "Schedule" menu
2. **Generate slots** → Slots > Generate
3. **View appointments** → Weekly/monthly view
4. **Appointment actions** → Right-click to edit/cancel
5. **Mark unavailable** → Select slots + bulk actions

### Service Management (Administrator)

1. **Admin** → "Services" tab
2. **Add** → Fill form (name, duration, price, color)
3. **Edit** → Click edit icon
4. **Delete** → Click delete icon

### News Management (Administrator)

1. **Admin** → "News" tab
2. **Create** → Write article with image
3. **Publish** → Change status to "PUBLISHED"
4. **Edit/Delete** → Actions available in list

### Hours Configuration (Administrator)

1. **Admin** → "Hours" tab
2. **Configure by day** → Add time slots (morning/afternoon)
3. **Closed** → Leave empty for closed day
4. **Save** → Hours display on homepage

## 👥 Test Accounts

### Administrator
- **Email**: `admin@cabinetdupont.fr`
- **Password**: `Admin123!`
- **Role**: `MEDECIN`

### Patient
- **Email**: `patient@test.fr`
- **Password**: `Patient123!`
- **Role**: `PATIENT`

## ✅ Quality and Testing

The project has been **fully tested** with **PHPUnit** and **Composer** to guarantee its stability and reliability in production.

### Test Results

```
PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: phpunit.xml

..................                                                18 / 18 (100%)

Time: 00:06.049, Memory: 8.00 MB

OK (18 tests, 38 assertions)
```

### Tests Performed

#### ✅ Unit Tests
- **User Model**: Creation, search, validation, update, deletion, password hashing
- **Service Model**: Creation, retrieval, data validation

#### ✅ Functional Tests
- **Authentication**: Valid/invalid login, role verification
- **Appointments**: Complete appointment booking flow, cancellation, retrieval

### Validation

- ✅ **18 tests passed** out of 18 (100% success)
- ✅ **38 assertions validated**
- ✅ Frontend ↔ Backend transmission verified
- ✅ Server-side data validation tested
- ✅ Database consistency confirmed

> **Note**: Test files have been removed from the production project to lighten the deployed code. The code has been validated and is **stable in production**.

## 🗃️ Database

### Main Tables

| Table | Description |
|-------|-------------|
| `utilisateur` | Users (patients, doctors, administrators) |
| `agenda` | Practitioner schedules |
| `creneau` | Available time slots |
| `rendezvous` | Confirmed appointments |
| `service` | Services offered by the practice |
| `actualite` | Practice news |
| `horaire_cabinet` | Practice opening hours |
| `cabinet` | Practice information |

### Relationships
- A **user** can have a **schedule**
- A **schedule** contains multiple **slots**
- A **slot** can have an **appointment**
- An **appointment** is linked to a **patient** and a **service**

## 🔒 Security

- ✅ CSRF protection on all forms
- ✅ Server-side data validation
- ✅ Prepared statements (PDO) against SQL injection
- ✅ Password hashing (bcrypt)
- ✅ Secure session management
- ✅ Route protection (middleware)
- ✅ Uploaded file type validation
- ✅ Display data escaping (XSS)

## 🎨 Customization

### Change Theme Colors

Edit `css/base/style.css`:

```css
:root {
  --bg: #f4f6fb;
  --brand: #3a6ea5;        /* Main color */
  --accent: #00c6ff;       /* Accent color 1 */
  --accent-2: #0072ff;     /* Accent color 2 */
  --text: #1e2936;         /* Text color */
  --white: #fff;
}
```

### Change Practice Information

Edit directly in views or via database:

```sql
UPDATE cabinet SET nom = 'Your Practice', adresse = 'Your Address' WHERE id = 1;
```

## 🤝 Contributing

Contributions are welcome! To contribute:

1. **Fork** the project
2. Create a branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a **Pull Request**

### Guidelines
- Respect MVC architecture
- Comment code in French
- Test features before pushing
- Follow existing naming conventions

## 📝 Changelog

### Version 1.0.0 (October 2025)
- ✅ Appointment management system
- ✅ Interactive schedule with drag & drop
- ✅ Patient, service, and news management
- ✅ Responsive interface
- ✅ Secure authentication system
- ✅ Opening hours configuration
- ✅ Automatic slot generation
- ✅ Bulk slot actions
- ✅ Admin dashboard

## 🔮 Roadmap

### Future Features
- [ ] Email notification system
- [ ] Data export (PDF, Excel)
- [ ] Advanced statistics and reports
- [ ] Calendar integration (Google Calendar, Outlook)
- [ ] Mobile application
- [ ] Online payment
- [ ] SMS reminders
- [ ] Telemedicine

## 🔗 Links

- **🌐 Production site**: [https://dupontcare.wuaze.com](https://dupontcare.wuaze.com)
- **💻 GitHub Repository**: [https://github.com/Wandalf-dev/CabinetDupont](https://github.com/Wandalf-dev/CabinetDupont)
- **🐛 Report a bug**: [GitHub Issues](https://github.com/Wandalf-dev/CabinetDupont/issues)
- **📖 Documentation**: This README
- **🏠 Local version**: [http://localhost/cabinetdupont-1](http://localhost/cabinetdupont-1)

## 📞 Support

For any questions or issues:

1. Consult the [documentation](#-usage)
2. Check [GitHub issues](https://github.com/Wandalf-dev/CabinetDupont/issues)
3. Create a new issue if necessary
4. Contact the development team

## 👨‍💻 Author

**Wandalf-dev**
- GitHub: [@Wandalf-dev](https://github.com/Wandalf-dev)
- Project: Cabinet Dupont

## 📋 Detailed Changelog

### Version 1.0.0 - October 2025

#### 🎉 Production Release
- ✅ **Migration to InfinityFree**: Site deployed at https://dupontcare.wuaze.com
- ✅ **SSL Certificate**: HTTPS enabled automatically
- ✅ **Production database**: MySQL on sql210.infinityfree.com

#### 🐛 Post-migration Fixes
- ✅ **Dynamic paths**: Replaced hardcoded paths with `BASE_URL`
- ✅ **Case-sensitivity**: Fixed file names for Linux compatibility
- ✅ **Autoloader**: Namespace conversion to lowercase paths
- ✅ **CSS encoding**: Fixed corrupted `agenda-grid.css` file
- ✅ **Lottie animations**: Replaced `Dentist.json` with `Doctor.json`
- ✅ **Booking system**: 
  - Fixed consecutive slots verification
  - Added consecutivity validation (exactly 30 min spacing)
  - Fixed 4-hour advance delay verification
  - Improved error messages for diagnostics

#### 🎨 UI/UX Improvements
- ✅ **Responsive**: Reduced gap between animation and title on mobile
- ✅ **Animation size**: Reduced Lottie animation (500px → mobile optimized)
- ✅ **Password toggle**: Added eye icon on login page
- ✅ **CSS Grid**: Fixed schedule border display

#### 🔧 Technical Optimizations
- ✅ **Auto environment detection**: Local vs Production
- ✅ **Debug logs removal**: Cleaned production code
- ✅ **Error handling**: Improved production error messages
- ✅ **Appointment overlap check**: Using real duration of existing appointments

## 📄 License

This project is under MIT license. See the `LICENSE` file for more details.

---

## 🚨 Troubleshooting

### Issue: Blank page

**Solution**:
1. Verify Apache and MySQL are started
2. Check PHP error logs in `C:\xampp\apache\logs\error.log`
3. Enable error display in `php.ini`: `display_errors = On`

### Issue: Database connection error

**Solution**:
1. Verify database `bdd_dupont` exists
2. Check credentials in `app/config/database.php`
3. Verify MySQL is running

### Issue: Images not displaying

**Solution**:
1. Verify folder `public/uploads/` exists
2. Check folder permissions (755)
3. Verify path in code (relative or absolute)

### Issue: CSS/JS not loading

**Solution**:
1. Check `BASE_URL` in `config.php`
2. Clear browser cache (Ctrl + F5)
3. Check browser console for 404 errors

### Issue: 404 error on routes

**Solution**:
1. Verify `.htaccess` file is present at root
2. Verify `mod_rewrite` is enabled in Apache
3. Check `BASE_URL` in `config.php`

---

## 🎉 Acknowledgments

Thank you for using Cabinet Dupont! Don't hesitate to ⭐ the project on GitHub if you appreciate it.

**Happy coding! 🚀**

## �📄 License

This project is licensed under MIT. See the `LICENSE` file for more details.
---
## 🚨 Troubleshooting
### Issue: Blank page
**Solution**:
1. Check that Apache and MySQL are started
2. Check PHP error logs in `C:\xampp\apache\logs\error.log`
3. Enable error display in `php.ini`: `display_errors = On`

### Issue: Database connection error
**Solution**:
1. Check that the `bdd_dupont` database exists
2. Check credentials in `app/config/database.php`
3. Check that MySQL is running
   
### Issue: Images not displaying
**Solution**:
1. Check that the `public/uploads/` folder exists
2. Check folder permissions (755)
3. Check the path in the code (relative or absolute)
   
### Issue: CSS/JS not loading
**Solution**:
1. Check the `BASE_URL` in `config.php`
2. Clear browser cache (Ctrl + F5)
3. Check browser console for 404 errors
   
### Issue: 404 error on routes
**Solution**:
1. Check that the `.htaccess` file is present at the root
2. Check that `mod_rewrite` is enabled in Apache
3. Check the `BASE_URL` in `config.php`
   
---
## 🎉 Acknowledgments

Merci d'utiliser Cabinet Dupont ! N'hésitez pas à ⭐ le projet sur GitHub si vous l'appréciez.

**Bon développement ! 🚀**
