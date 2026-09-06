<!-- PROJECT LOGO -->
<div align="center">
  <img src="https://img.shields.io/badge/SkillShare-Hub-6C63FF?style=for-the-badge&logo=github" alt="SkillShare Hub" width="400"/>

  <h3>🎓 Learn from Industry Experts</h3>
  <p>A modern e-learning platform connecting freshers with mentors.</p>

  <p>
    <a href="https://php.net">
      <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
    </a>
    <a href="https://mysql.com">
      <img src="https://img.shields.io/badge/MySQL-5.7+-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
    </a>
    <a href="https://getbootstrap.com">
      <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap"/>
    </a>
    <a href="https://code.visualstudio.com">
      <img src="https://img.shields.io/badge/VS_Code-1.80+-007ACC?style=for-the-badge&logo=visualstudiocode&logoColor=white" alt="VS Code"/>
    </a>
    <a href="LICENSE">
      <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="MIT License"/>
    </a>
  </p>
</div>

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [VS Code Setup](#-vs-code-setup)
- [Database Setup](#-database-setup)
- [Default Credentials](#-default-credentials)
- [Project Structure](#-project-structure)
- [API Endpoints](#-api-endpoints)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [Development Guidelines](#-development-guidelines)
- [Roadmap](#-roadmap)
- [License](#-license)
- [Contact](#-contact--support)

---

## 🚀 Overview

**SkillShare Hub** is a complete e-learning platform designed to connect freshers with industry mentors. It provides a modern and intuitive interface for course management, live sessions, assignments, certificates, messaging, and progress tracking.

The platform supports three user roles:

- 👑 **Admin** — Manages users, courses, payments, reports, and platform settings.
- 👨‍🏫 **Mentor** — Creates courses, manages lessons, schedules sessions, and tracks student progress.
- 👨‍🎓 **Fresher** — Enrolls in courses, attends sessions, submits assignments, and earns certificates.

### 🎯 Purpose

- Bridge the gap between freshers and industry experts.
- Provide practical and structured learning experiences.
- Enable mentorship and career guidance.
- Build a collaborative community of learners and educators.

---

## ✨ Features

### 🔐 Authentication & Authorization

- Multi-role authentication system: Admin, Mentor, and Fresher.
- Secure login and registration forms.
- Input validation and password strength meter.
- Remember Me functionality.
- Forgot password and email reset workflow.
- Session-based access control.

### 👨‍🎓 Fresher Features

- 📚 Browse and enroll in available courses.
- 📹 Watch video lessons with progress tracking.
- 📊 View learning progress from a personalized dashboard.
- 🗓️ Book and join mentor live sessions.
- 📝 Submit assignments online.
- 🎓 Earn certificates after course completion.
- 💬 Message mentors directly.
- ⭐ Review and rate courses and mentors.
- 🔔 Receive notifications for course updates and sessions.

### 👨‍🏫 Mentor Features

- 📚 Create, edit, and manage courses.
- 📹 Add modules, video lessons, and learning resources.
- 🗓️ Schedule and manage live sessions.
- 📝 Create assignments and grade student submissions.
- 👥 Track enrolled students and their progress.
- 💰 Monitor revenue and course earnings.
- 💬 Communicate with students through messaging.

### 👑 Admin Features

- 📊 View platform analytics through the admin dashboard.
- 👥 Manage student, mentor, and admin accounts.
- 📚 Approve, monitor, and manage courses.
- 💰 Track payment transactions.
- 📋 Generate reports.
- ⚙️ Configure platform settings.
- 🔔 Manage notifications and announcements.

---

## 🛠️ Tech Stack

### Backend

| Technology | Purpose |
|---|---|
| PHP 7.4+ | Server-side scripting |
| MySQL 5.7+ | Database management |
| PDO | Secure database connections |
| XAMPP / WAMP | Local development environment |

### Frontend

| Technology | Purpose |
|---|---|
| HTML5 | Semantic page structure |
| CSS3 | Custom styling and glassmorphism UI |
| JavaScript | Dynamic client-side interactions |
| Bootstrap 5.3 | Responsive UI framework |
| Chart.js | Analytics and dashboard charts |

### Development Tools

| Tool | Purpose |
|---|---|
| VS Code | Primary code editor |
| Git | Version control |
| GitHub | Repository hosting |
| XDebug | PHP debugging |

---

## 💻 Installation

### Prerequisites

Before installing the project, make sure you have the following:

- [XAMPP](https://www.apachefriends.org/) or WAMP with PHP 7.4+ and MySQL 5.7+
- [VS Code](https://code.visualstudio.com/)
- [Git](https://git-scm.com/) *(optional but recommended)*

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/SkillShare-Hub.git
cd SkillShare-Hub
If you downloaded the project as a ZIP file, extract it into your XAMPP htdocs directory:

text

C:\xampp\htdocs\SkillShare-Hub
Step 2: Start XAMPP Services
Open the XAMPP Control Panel and start:

Apache
MySQL
Step 3: Create the Database
Open phpMyAdmin:

text

http://localhost/phpmyadmin
Create a database named:

text

skillshare_hub
Step 4: Import the Database Schema
Import the main schema file:

text

database/skillshare_hub.sql
Optionally, import sample records for testing:

text

database/seed_data.sql
You can also import using the command line:

Bash

mysql -u root -p skillshare_hub < database/skillshare_hub.sql
mysql -u root -p skillshare_hub < database/seed_data.sql
Step 5: Configure Database Connection
Open the file:

text

config/database.php
Update the database credentials if needed:

PHP

<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'skillshare_hub');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
Step 6: Run the Application
Open the project in your browser:

text

http://localhost/SkillShare-Hub/
🔧 VS Code Setup
1. Open the Project
From your terminal:

Bash

code .
Or open VS Code and select:

text

File → Open Folder → SkillShare-Hub
2. Install Recommended Extensions
Create or use the .vscode/extensions.json file to install recommended extensions.

Suggested extensions:

PHP Intelephense
PHP Debug
Prettier
Bootstrap 5 Snippets
HTML CSS Support
MySQL
GitLens
Live Server
Example .vscode/extensions.json:

JSON

{
  "recommendations": [
    "bmewburn.vscode-intelephense-client",
    "xdebug.php-debug",
    "esbenp.prettier-vscode",
    "thekalinga.bootstrap4-vscode",
    "ecmel.vscode-html-css",
    "ms-azuretools.vscode-docker",
    "ritwickdey.liveserver",
    "eamodio.gitlens"
  ]
}
3. Configure Editor Settings
Example .vscode/settings.json:

JSON

{
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  "files.autoSave": "afterDelay",
  "php.validate.enable": true,
  "intelephense.environment.phpVersion": "7.4.0"
}
4. Enable XDebug for PHP Debugging
Edit your PHP configuration file:

text

C:\xampp\php\php.ini
Add or update the following configuration:

ini

zend_extension=xdebug
xdebug.mode=debug
xdebug.start_with_request=yes
xdebug.client_port=9000
Restart Apache after making changes.

Example .vscode/launch.json:

JSON

{
  "version": "0.2.0",
  "configurations": [
    {
      "name": "Listen for XDebug",
      "type": "php",
      "request": "launch",
      "port": 9000
    }
  ]
}
5. Run a Local PHP Server
You can run the project without XAMPP Apache using PHP's built-in development server:

Bash

php -S localhost:8000
Then open:

text

http://localhost:8000
Note: MySQL must still be running for database features to work.

🗄️ Database Setup
Main Database Files
File	Description
database/skillshare_hub.sql	Complete database schema
database/seed_data.sql	Sample users, courses, sessions, and enrollments
database/README.md	Database documentation
Database Tables
Table	Description
users	User accounts and profile data
courses	Course information
course_modules	Course modules and sections
course_lessons	Individual course lessons
enrollments	Student course enrollments
sessions	Mentor live sessions
bookings	Student session bookings
assignments	Course assignments
submissions	Student assignment submissions
certificates	Course completion certificates
payments	Payment and transaction details
messages	Mentor-student chat messages
notifications	System and user notifications
ratings	Course and mentor reviews
Sample Database Import
Bash

mysql -u root -p skillshare_hub < database/skillshare_hub.sql
mysql -u root -p skillshare_hub < database/seed_data.sql
🔑 Default Credentials
Use the following accounts after importing the sample data:

Role	Email	Password
👑 Admin	admin@skillsharehub.com	admin123
👨‍🏫 Mentor	john@example.com	mentor123
👨‍🎓 Fresher	alice@example.com	fresher123
⚠️ Security Note: Change all default credentials before deploying the project to production.

📁 Project Structure
text

SkillShare-Hub/
│
├── .vscode/                         # VS Code configuration
│   ├── settings.json                # Editor settings
│   ├── extensions.json              # Recommended extensions
│   └── launch.json                  # PHP debugging configuration
│
├── admin/                           # Admin panel
│   ├── dashboard.php
│   ├── index.html                   # Admin UI demo
│   ├── users.html                   # User management UI
│   └── courses.html                 # Course management UI
│
├── fresher/                         # Fresher dashboard and modules
│   ├── dashboard.php
│   ├── learning/
│   ├── sessions/
│   ├── assignments/
│   ├── certificates/
│   ├── messages/
│   └── notifications/
│
├── mentor/                          # Mentor dashboard and modules
│   ├── dashboard.php
│   ├── my-courses/
│   ├── sessions/
│   ├── assignments/
│   └── students.php
│
├── config/                          # Configuration files
│   ├── database.php                 # Database connection
│   ├── session.php                  # Session management
│   ├── auth.php                     # Authentication helpers
│   ├── functions.php                # Common helper functions
│   └── validation.php               # Form validation
│
├── includes/                        # Reusable layout components
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   ├── sidebar.php
│   └── alerts.php
│
├── assets/                          # Static assets
│   ├── css/
│   │   ├── style.css                # Main application styles
│   │   ├── modern-ui.css            # Modern UI styles
│   │   └── responsive.css           # Responsive design styles
│   │
│   ├── js/
│   │   ├── main.js                  # Main JavaScript
│   │   ├── validation.js            # Client-side validation
│   │   └── dashboard.js             # Dashboard charts and logic
│   │
│   └── images/
│
├── uploads/                         # User-generated uploads
│   ├── profiles/                    # Profile images
│   ├── courses/                     # Course thumbnails
│   ├── resources/                   # Course resources
│   └── assignments/                 # Assignment submissions
│
├── database/                        # Database files
│   ├── skillshare_hub.sql           # Complete schema
│   ├── seed_data.sql                # Sample test data
│   └── README.md                    # Database documentation
│
├── api/                             # API endpoints
│   ├── courses.php
│   ├── mentors.php
│   ├── sessions.php
│   ├── bookings.php
│   ├── notifications.php
│   └── search.php
│
├── index.php                        # Home page
├── login.php                        # Login page
├── register.php                     # Registration page
├── about.php                        # About page
├── contact.php                      # Contact page
├── logout.php                       # Logout handler
├── 404.php                          # Not found page
├── .htaccess                        # Apache configuration
├── .gitignore                       # Git ignore rules
├── LICENSE                          # MIT license
└── README.md                        # Project documentation
🌐 API Endpoints
Method	Endpoint	Description
GET	/api/courses.php	Fetch all courses
GET	/api/courses.php?id=1	Fetch a specific course
GET	/api/mentors.php	Fetch all mentors
GET	/api/sessions.php	Fetch available live sessions
POST	/api/bookings.php	Create a session booking
GET	/api/notifications.php	Fetch user notifications
GET	/api/search.php?q=query	Search courses, mentors, and content
Example API Request
text

GET http://localhost/SkillShare-Hub/api/courses.php
Example Search Request
text

GET http://localhost/SkillShare-Hub/api/search.php?q=php
📸 Screenshots
🏠 Home Page
Home Page

📊 Admin Dashboard
Admin Dashboard

👥 User Management
User Management

📚 Course Management
Course Management

👨‍🎓 Fresher Dashboard
Fresher Dashboard

👨‍🏫 Mentor Dashboard
Mentor Dashboard

🤝 Contributing
Contributions are welcome and appreciated.

Steps to Contribute
Fork this repository.
Clone your fork locally.
Create a feature branch.
Make your changes.
Commit your changes.
Push the branch to GitHub.
Open a Pull Request.
Bash

# Clone your fork
git clone https://github.com/yourusername/SkillShare-Hub.git
cd SkillShare-Hub

# Create a new feature branch
git checkout -b feature/your-feature-name

# Add changes
git add .

# Commit changes
git commit -m "feat(scope): add your feature description"

# Push branch
git push origin feature/your-feature-name
🧑‍💻 Development Guidelines
Coding Standards
Follow PSR-12 coding standards for PHP.
Use prepared statements with PDO for all database queries.
Validate and sanitize all user inputs.
Escape output to prevent XSS vulnerabilities.
Use meaningful names for variables, functions, and files.
Keep reusable logic inside the config/ or includes/ directories.
Comment complex code where necessary.
Test changes before creating a pull request.
Update documentation whenever you add or modify features.
Commit Message Format
Use the following convention:

text

<type>(<scope>): <subject>
Commit Types
Type	Description
feat	New feature
fix	Bug fix
docs	Documentation changes
style	Formatting or styling changes
refactor	Code restructuring
test	Tests added or updated
chore	Maintenance task
Examples
Bash

git commit -m "feat(auth): add password reset functionality"
git commit -m "fix(register): correct email validation"
git commit -m "docs(readme): update installation steps"
git commit -m "refactor(database): improve PDO connection handling"
🧾 .gitignore
Create a .gitignore file with the following content:

gitignore

# VS Code
.vscode/
*.code-workspace

# XAMPP/WAMP
htdocs/
www/
xampp/

# PHP
*.log
*.sqlite
*.db
composer.phar
vendor/
.env
.env.backup

# Uploads
uploads/
!uploads/.gitkeep

# Cache and temporary files
cache/
logs/
tmp/

# IDE files
.idea/
*.iml
.DS_Store
Thumbs.db
*.tmp
*.swp

# Database backups
*.sql
!database/skillshare_hub.sql
!database/seed_data.sql

# System files
error_log
.htaccess.backup
If you want to commit VS Code project settings, remove .vscode/ from .gitignore.

⚙️ Apache Configuration
Create a .htaccess file in the project root:

apache

# Enable Rewrite Engine
RewriteEngine On

# Redirect to HTTPS in production
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]

# Remove trailing slash when the target is not a directory
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)/$ /$1 [L,R=301]

# Protect database files
<FilesMatch "\.(sql|sqlite|db)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Disable directory browsing
Options -Indexes

# Default character encoding
AddDefaultCharset UTF-8

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/json
</IfModule>

# Custom error pages
ErrorDocument 404 /404.php
ErrorDocument 403 /403.php
ErrorDocument 500 /500.php
Ensure Apache's mod_rewrite module is enabled in XAMPP before using rewrite rules.

🚀 Quick Start in VS Code
1. Open the Project
Bash

code .
2. Install Extensions
When VS Code detects .vscode/extensions.json, select:

text

Install All Recommended Extensions
3. Start Development
Option 1: XAMPP
Start Apache and MySQL from XAMPP Control Panel.
Open:
text

http://localhost/SkillShare-Hub/
Option 2: PHP Built-in Server
Bash

php -S localhost:8000
Then open:

text

http://localhost:8000
Option 3: Live Server
Live Server is suitable for static HTML/CSS/JS previews. PHP pages require Apache, XAMPP, WAMP, or PHP's built-in server to execute correctly.

🚧 Roadmap
✅ Completed
 User authentication system
 Role-based dashboards
 Course management
 Session booking
 Assignment system
 Certificate generation
 Messaging system
 Admin panel
 Responsive design
🚧 In Progress
 Video conferencing integration
 Payment gateway integration using Stripe or PayPal
 Mobile application using React Native
 AI-powered course recommendations
🔮 Future Plans
 Organization and company accounts
 Bulk user management
 Advanced analytics and reporting
 White-label platform support
 Multi-language support
 Dark mode
🙏 Acknowledgments
Bootstrap — Responsive UI framework
Font Awesome — Icons
Chart.js — Charts and data visualization
Google Fonts — Typography
All contributors who help improve SkillShare Hub
📄 License
This project is licensed under the MIT License.

text

MIT License

Copyright (c) 2024 SkillShare Hub

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
See the LICENSE file for complete details.

📞 Contact & Support
Platform	Link
📧 Email	support@skillsharehub.com
🐛 Issues	GitHub Issues
📖 Documentation	Project Wiki
💬 Discord	Join the SkillShare Hub Discord community
⭐ Show Your Support
If this project helped you, please consider giving it a ⭐ on GitHub.

<div align="center"> <a href="https://github.com/yourusername/SkillShare-Hub/stargazers"> <img src="https://img.shields.io/github/stars/yourusername/SkillShare-Hub?style=social" alt="GitHub Stars"/> </a> <a href="https://github.com/yourusername/SkillShare-Hub/fork"> <img src="https://img.shields.io/github/forks/yourusername/SkillShare-Hub?style=social" alt="GitHub Forks"/> </a> </div>
📊 GitHub Stats
<div align="center"> <img src="https://github-readme-stats.vercel.app/api?username=yourusername&show_icons=true&theme=radical" alt="GitHub Stats" width="400"/> <img src="https://github-readme-stats.vercel.app/api/top-langs/?username=yourusername&layout=compact&theme=radical" alt="Top Languages" width="400"/> </div>
<div align="center">
Made with ❤️ by the SkillShare Hub Team

⬆ Back to Top

</div> ```


sql code
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2026 at 06:15 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `careerbridge`
--
CREATE DATABASE IF NOT EXISTS `careerbridge` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `careerbridge`;
--
-- Database: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Table structure for table `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Table structure for table `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

-- --------------------------------------------------------

--
-- Table structure for table `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Table structure for table `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Table structure for table `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Table structure for table `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Table structure for table `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Dumping data for table `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"skillshare_hub\",\"table\":\"academic_fields\"},{\"db\":\"skillshare_hub\",\"table\":\"courses\"},{\"db\":\"skillshare_hub\",\"table\":\"course_lessons\"},{\"db\":\"skillshare_hub\",\"table\":\"assignments\"},{\"db\":\"skillshare_hub\",\"table\":\"users\"},{\"db\":\"skillshare_hub\",\"table\":\"user_profiles\"},{\"db\":\"skillswap\",\"table\":\"swaps\"},{\"db\":\"skillswap\",\"table\":\"users\"},{\"db\":\"skillswap\",\"table\":\"user_skills\"},{\"db\":\"skilshopdb\",\"table\":\"users\"}]');

-- --------------------------------------------------------

--
-- Table structure for table `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Table structure for table `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

-- --------------------------------------------------------

--
-- Table structure for table `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Table structure for table `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Dumping data for table `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-09-06 16:15:41', '{\"Console\\/Mode\":\"collapse\"}');

-- --------------------------------------------------------

--
-- Table structure for table `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Table structure for table `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indexes for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indexes for table `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indexes for table `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indexes for table `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indexes for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indexes for table `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indexes for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indexes for table `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indexes for table `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indexes for table `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indexes for table `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indexes for table `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indexes for table `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Database: `skillshare_hub`
--
CREATE DATABASE IF NOT EXISTS `skillshare_hub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skillshare_hub`;

-- --------------------------------------------------------

--
-- Table structure for table `academic_fields`
--

CREATE TABLE `academic_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_fields`
--

INSERT INTO `academic_fields` (`id`, `name`, `description`, `icon`, `color`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science', 'Explore programming, software development, and computer technology.', 'fa-laptop-code', '#2563EB', '', 1, '2026-09-05 14:39:41', '2026-09-05 14:39:41'),
(2, 'Engineering', 'Engineering disciplines.', 'fa-gear', '#7C3AED', 'engineering', 1, '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(3, 'Business', 'Management and entrepreneurship.', 'fa-briefcase', '#059669', 'business', 1, '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(4, 'Health Sciences', 'Health and medicine.', 'fa-heart-pulse', '#DC2626', 'health-sciences', 1, '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(5, 'Education', 'Teaching and leadership.', 'fa-graduation-cap', '#D97706', 'education', 1, '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(6, 'Law', 'Legal studies.', 'fa-scale-balanced', '#4F46E5', 'law', 1, '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(7, 'Cybersecurity', 'Protect systems and networks from digital attacks', 'shield-alt', '#FF6B6B', 'cybersecurity', 1, '2026-09-05 15:53:37', '2026-09-05 15:53:37'),
(8, 'Blockchain', 'Build decentralized applications and smart contracts', 'link', '#339AF0', 'blockchain', 1, '2026-09-05 15:53:37', '2026-09-05 15:53:37'),
(9, 'UI/UX Design', 'Design user-centered interfaces and experiences', 'paint-brush', '#FCC419', 'ui-ux-design', 1, '2026-09-05 15:53:37', '2026-09-05 15:53:37'),
(10, 'Digital Marketing', 'Promote products and services using digital channels', 'bullhorn', '#51CF66', 'digital-marketing', 1, '2026-09-05 15:53:37', '2026-09-05 15:53:37'),
(12, 'Finance', 'Financial management and investment', 'chart-line', '#20C997', 'finance', 1, '2026-09-05 15:53:37', '2026-09-05 15:53:37'),
(13, 'Healthcare', 'Healthcare and medical sciences', 'heartbeat', '#F06595', 'healthcare', 1, '2026-09-05 15:53:37', '2026-09-05 15:53:37');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` longtext DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `max_score` decimal(5,2) DEFAULT 100.00,
  `weight` decimal(5,2) DEFAULT 10.00,
  `attachment_url` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `mentor_id`, `course_id`, `title`, `description`, `instructions`, `due_date`, `max_score`, `weight`, `attachment_url`, `is_published`, `created_at`, `updated_at`) VALUES
(3, 2, 1, 'Build a Personal Portfolio', 'Create a responsive portfolio website using HTML and CSS.', 'Include a header, about section, projects gallery, and contact form.', '2026-09-12 20:31:27', 100.00, 10.00, NULL, 1, '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(4, 3, 2, 'Design a Mobile App Interface', 'Create a mobile app UI design for a fitness tracking app.', 'Include at least 5 screens with a consistent design system.', '2026-09-15 20:31:27', 100.00, 10.00, NULL, 1, '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(5, 2, 1, 'Build a Personal Portfolio', 'Create a responsive portfolio website using HTML and CSS.', 'Include a header, about section, projects gallery, and contact form.', '2026-09-12 20:31:55', 100.00, 10.00, NULL, 1, '2026-09-05 14:46:55', '2026-09-05 14:46:55'),
(6, 3, 2, 'Design a Mobile App Interface', 'Create a mobile app UI design for a fitness tracking app.', 'Include at least 5 screens with a consistent design system.', '2026-09-15 20:31:55', 100.00, 10.00, NULL, 1, '2026-09-05 14:46:55', '2026-09-05 14:46:55');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submissions`
--

CREATE TABLE `assignment_submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `submission_file` varchar(255) DEFAULT NULL,
  `submission_text` longtext DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `score` decimal(5,2) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `graded_by` int(11) DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `is_late` tinyint(1) DEFAULT 0,
  `resubmission_count` int(11) DEFAULT 0,
  `status` enum('pending','submitted','graded','resubmitted') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_comments`
--

CREATE TABLE `blog_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `content` longtext NOT NULL,
  `excerpt` text DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `views` int(11) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `attended` tinyint(1) DEFAULT 0,
  `feedback` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `fresher_id`, `session_id`, `booking_date`, `status`, `notes`, `attended`, `feedback`, `rating`, `created_at`, `updated_at`) VALUES
(3, 4, 1, '2026-09-05 14:46:27', 'pending', 'Excited to learn web development!', 0, NULL, NULL, '2026-09-05 14:46:27', '2026-09-05 15:55:46'),
(4, 5, 1, '2026-09-05 14:46:27', 'pending', 'Looking forward to this session.', 0, NULL, NULL, '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(7, 1, 1, '2026-09-05 14:47:18', 'approved', NULL, 0, NULL, NULL, '2026-09-05 14:47:18', '2026-09-05 15:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `certificate_code` varchar(50) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` date DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `verification_url` varchar(255) DEFAULT NULL,
  `is_valid` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `field_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `long_description` longtext DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `duration` int(11) DEFAULT 0 COMMENT 'in hours',
  `total_lessons` int(11) DEFAULT 0,
  `requirements` text DEFAULT NULL,
  `learning_outcomes` text DEFAULT NULL,
  `target_audience` text DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_students` int(11) DEFAULT 0,
  `status` enum('draft','pending','active','inactive','archived') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `mentor_id`, `field_id`, `title`, `slug`, `description`, `long_description`, `thumbnail`, `video_url`, `price`, `discount_price`, `level`, `duration`, `total_lessons`, `requirements`, `learning_outcomes`, `target_audience`, `featured`, `rating`, `total_students`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Web Development Fundamentals', 'web-development-fundamentals', 'Learn HTML, CSS, JavaScript and build real-world websites.', NULL, 'web-dev.jpg', NULL, 49.99, NULL, 'beginner', 40, 0, NULL, NULL, NULL, 1, 0.00, 120, 'active', '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(2, 3, 1, 'UI/UX Design Mastery', 'uiux-design-mastery', 'Master user interface and experience design principles.', NULL, 'uiux.jpg', NULL, 59.99, NULL, 'intermediate', 35, 0, NULL, NULL, NULL, 1, 0.00, 85, 'active', '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(3, 2, 1, 'Data Science with Python', 'data-science-with-python', 'Learn data analysis and machine learning.', NULL, 'datascience.jpg', NULL, 79.99, NULL, 'advanced', 50, 0, NULL, NULL, NULL, 0, 0.00, 60, 'active', '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(4, 3, 3, 'Digital Marketing Strategy', 'digital-marketing-strategy', 'Master SEO and social media marketing.', NULL, 'marketing.jpg', NULL, 39.99, NULL, 'beginner', 25, 0, NULL, NULL, NULL, 0, 0.00, 95, 'active', '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(5, 3, 8, 'blockchai', 'blockchai', 'hello', NULL, NULL, NULL, 0.06, NULL, 'intermediate', 12, 0, NULL, NULL, NULL, 0, 0.00, 0, 'active', '2026-09-05 15:55:14', '2026-09-05 15:55:14');

-- --------------------------------------------------------

--
-- Table structure for table `course_lessons`
--

CREATE TABLE `course_lessons` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `video_duration` int(11) DEFAULT 0 COMMENT 'in seconds',
  `resource_url` varchar(255) DEFAULT NULL,
  `resource_type` enum('pdf','doc','ppt','video','audio','zip','other') DEFAULT 'other',
  `order_number` int(11) DEFAULT 0,
  `is_free` tinyint(1) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_modules`
--

CREATE TABLE `course_modules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `order_number` int(11) DEFAULT 0,
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `progress` decimal(5,2) DEFAULT 0.00,
  `completed_lessons` int(11) DEFAULT 0,
  `total_lessons` int(11) DEFAULT 0,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','paused','completed','dropped') DEFAULT 'active',
  `certificate_issued` tinyint(1) DEFAULT 0,
  `certificate_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `fresher_id`, `course_id`, `progress`, `completed_lessons`, `total_lessons`, `started_at`, `completed_at`, `last_accessed_at`, `status`, `certificate_issued`, `certificate_code`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 35.00, 0, 0, '2026-09-05 14:43:17', NULL, NULL, 'active', 0, NULL, '2026-09-05 14:43:17', '2026-09-05 14:43:17'),
(2, 4, 2, 20.00, 0, 0, '2026-09-05 14:43:17', NULL, NULL, 'active', 0, NULL, '2026-09-05 14:43:17', '2026-09-05 14:43:17'),
(3, 5, 1, 50.00, 0, 0, '2026-09-05 14:43:17', NULL, NULL, 'active', 0, NULL, '2026-09-05 14:43:17', '2026-09-05 14:43:17'),
(4, 5, 3, 100.00, 0, 0, '2026-09-05 14:43:17', NULL, NULL, 'completed', 0, NULL, '2026-09-05 14:43:17', '2026-09-05 14:43:17');

-- --------------------------------------------------------

--
-- Table structure for table `interview_questions`
--

CREATE TABLE `interview_questions` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `field_id` int(11) DEFAULT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `category` varchar(100) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `is_published` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interview_questions`
--

INSERT INTO `interview_questions` (`id`, `mentor_id`, `field_id`, `question`, `answer`, `difficulty`, `category`, `tags`, `is_published`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'What is the difference between GET and POST in PHP?', 'GET sends data in URL parameters while POST sends data in the request body. POST is more secure for sensitive data.', 'easy', 'PHP', NULL, 1, '2026-09-05 14:39:42', '2026-09-05 14:39:42'),
(2, 2, 1, 'Explain MVC architecture.', 'MVC stands for Model-View-Controller. It separates application logic into three interconnected components for better organization.', 'medium', 'Architecture', NULL, 1, '2026-09-05 14:39:42', '2026-09-05 14:39:42'),
(3, 3, 1, 'What is the difference between UI and UX?', 'UI (User Interface) is about visual design, while UX (User Experience) is about overall user satisfaction and usability.', 'easy', 'Design', NULL, 1, '2026-09-05 14:39:42', '2026-09-05 14:39:42'),
(4, 2, 1, 'What is the difference between GET and POST in PHP?', 'GET sends data in URL parameters while POST sends data in the request body. POST is more secure for sensitive data.', 'easy', 'PHP', NULL, 1, '2026-09-05 14:46:28', '2026-09-05 14:46:28'),
(5, 2, 1, 'Explain MVC architecture.', 'MVC stands for Model-View-Controller. It separates application logic into three interconnected components for better organization.', 'medium', 'Architecture', NULL, 1, '2026-09-05 14:46:28', '2026-09-05 14:46:28'),
(6, 3, 1, 'What is the difference between UI and UX?', 'UI (User Interface) is about visual design, while UX (User Experience) is about overall user satisfaction and usability.', 'easy', 'Design', NULL, 1, '2026-09-05 14:46:28', '2026-09-05 14:46:28'),
(7, 2, 1, 'What is the difference between GET and POST in PHP?', 'GET sends data in URL parameters while POST sends data in the request body. POST is more secure for sensitive data.', 'easy', 'PHP', NULL, 1, '2026-09-05 14:46:56', '2026-09-05 14:46:56'),
(8, 2, 1, 'Explain MVC architecture.', 'MVC stands for Model-View-Controller. It separates application logic into three interconnected components for better organization.', 'medium', 'Architecture', NULL, 1, '2026-09-05 14:46:56', '2026-09-05 14:46:56'),
(9, 3, 1, 'What is the difference between UI and UX?', 'UI (User Interface) is about visual design, while UX (User Experience) is about overall user satisfaction and usability.', 'easy', 'Design', NULL, 1, '2026-09-05 14:46:56', '2026-09-05 14:46:56');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `watched_duration` int(11) DEFAULT 0 COMMENT 'in seconds',
  `completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_watched_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `lesson_progress`
--
DELIMITER $$
CREATE TRIGGER `update_enrollment_progress` AFTER UPDATE ON `lesson_progress` FOR EACH ROW BEGIN
    DECLARE total INT;
    DECLARE completed INT;
    
    SELECT COUNT(*), SUM(completed) 
    INTO total, completed
    FROM lesson_progress lp
    JOIN course_lessons cl ON lp.lesson_id = cl.id
    JOIN course_modules cm ON cl.module_id = cm.id
    WHERE lp.fresher_id = NEW.fresher_id
    AND cm.course_id = (
        SELECT course_id FROM course_modules 
        WHERE id = (SELECT module_id FROM course_lessons WHERE id = NEW.lesson_id)
    );
    
    UPDATE enrollments 
    SET progress = (completed / total) * 100,
        completed_lessons = completed,
        total_lessons = total,
        last_accessed_at = NOW()
    WHERE fresher_id = NEW.fresher_id 
    AND course_id = (
        SELECT course_id FROM course_modules 
        WHERE id = (SELECT module_id FROM course_lessons WHERE id = NEW.lesson_id)
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `icon`, `is_read`, `read_at`, `data`, `created_at`) VALUES
(1, 4, 'booking', 'Session Booked', 'Your booking for Introduction to Web Development has been confirmed.', 'fresher/my-sessions.php', 'fa-calendar-check', 0, NULL, NULL, '2026-09-05 14:39:41'),
(2, 4, 'message', 'New Message', 'You have received a new message from Roshan Timalsina.', 'fresher/messages/chat.php?user_id=2', 'fa-envelope', 0, NULL, NULL, '2026-09-05 14:39:41'),
(3, 5, 'enrollment', 'Course Enrollment', 'You have been enrolled in Data Science with Python.', 'fresher/my-courses.php', 'fa-book-open', 1, NULL, NULL, '2026-09-04 14:39:41'),
(4, 2, 'booking', 'New Booking', 'Amit Sharma has booked your upcoming session.', 'mentor/sessions/my-sessions.php', 'fa-calendar-check', 0, NULL, NULL, '2026-09-05 14:39:41'),
(5, 2, 'booking', 'New Booking Request', 'Roshan Timalsina has requested to book your session: Introduction to Web Development', '1', NULL, 0, NULL, NULL, '2026-09-05 14:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'USD',
  `payment_method` enum('card','paypal','bank_transfer','crypto') DEFAULT 'card',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_intent_id` varchar(100) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed','refunded') DEFAULT 'pending',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `refund_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `session_id` int(11) DEFAULT NULL,
  `rating` decimal(2,1) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `helpful_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `mentor_id`, `fresher_id`, `course_id`, `session_id`, `rating`, `review`, `is_public`, `helpful_count`, `created_at`, `updated_at`) VALUES
(1, 2, 4, NULL, NULL, 5.0, 'Excellent mentor! Very knowledgeable and patient.', 1, 0, '2026-09-05 14:39:41', '2026-09-05 14:39:41'),
(2, 2, 5, NULL, NULL, 5.0, 'Great teaching style, highly recommended.', 1, 0, '2026-08-26 14:39:41', '2026-09-05 14:39:41'),
(3, 3, 4, NULL, NULL, 4.0, 'Very helpful and professional.', 1, 0, '2026-09-05 14:39:41', '2026-09-05 14:39:41'),
(4, 3, 5, NULL, NULL, 5.0, 'Amazing UI/UX insights, learned a lot!', 1, 0, '2026-08-31 14:39:41', '2026-09-05 14:39:41'),
(5, 2, 4, NULL, NULL, 5.0, 'Excellent mentor! Very knowledgeable and patient.', 1, 0, '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(6, 2, 5, NULL, NULL, 5.0, 'Great teaching style, highly recommended.', 1, 0, '2026-08-26 14:46:27', '2026-09-05 14:46:27'),
(7, 3, 4, NULL, NULL, 4.0, 'Very helpful and professional.', 1, 0, '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(8, 3, 5, NULL, NULL, 5.0, 'Amazing UI/UX insights, learned a lot!', 1, 0, '2026-08-31 14:46:27', '2026-09-05 14:46:27'),
(9, 2, 4, NULL, NULL, 5.0, 'Excellent mentor! Very knowledgeable and patient.', 1, 0, '2026-09-05 14:46:55', '2026-09-05 14:46:55'),
(10, 2, 5, NULL, NULL, 5.0, 'Great teaching style, highly recommended.', 1, 0, '2026-08-26 14:46:55', '2026-09-05 14:46:55'),
(11, 3, 4, NULL, NULL, 4.0, 'Very helpful and professional.', 1, 0, '2026-09-05 14:46:55', '2026-09-05 14:46:55'),
(12, 3, 5, NULL, NULL, 5.0, 'Amazing UI/UX insights, learned a lot!', 1, 0, '2026-08-31 14:46:55', '2026-09-05 14:46:55');

--
-- Triggers `ratings`
--
DELIMITER $$
CREATE TRIGGER `update_course_rating` AFTER INSERT ON `ratings` FOR EACH ROW BEGIN
    IF NEW.course_id IS NOT NULL THEN
        UPDATE courses c
        SET rating = (
            SELECT AVG(rating) 
            FROM ratings 
            WHERE course_id = NEW.course_id
        )
        WHERE c.id = NEW.course_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `research_applications`
--

CREATE TABLE `research_applications` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `research_projects`
--

CREATE TABLE `research_projects` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `field_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `objectives` text DEFAULT NULL,
  `methodology` text DEFAULT NULL,
  `expected_outcomes` text DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `max_applicants` int(11) DEFAULT 10,
  `current_applicants` int(11) DEFAULT 0,
  `status` enum('open','in_progress','completed','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `research_projects`
--

INSERT INTO `research_projects` (`id`, `mentor_id`, `field_id`, `title`, `description`, `objectives`, `methodology`, `expected_outcomes`, `required_skills`, `start_date`, `end_date`, `max_applicants`, `current_applicants`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, 'E-commerce Website Development', 'Build a full-stack e-commerce platform with payment integration.', 'Learn modern web development practices.', NULL, NULL, 'PHP, MySQL, JavaScript', '2026-09-19', '2026-11-04', 5, 0, 'open', '2026-09-05 14:39:42', '2026-09-05 14:39:42'),
(2, 3, NULL, 'Mobile App UX Research', 'Conduct user research and design a mobile app experience.', 'Understand user needs and design solutions.', NULL, NULL, 'UI/UX, Figma, User Research', '2026-09-12', '2026-10-20', 3, 0, 'open', '2026-09-05 14:39:42', '2026-09-05 14:39:42'),
(3, 2, NULL, 'E-commerce Website Development', 'Build a full-stack e-commerce platform with payment integration.', 'Learn modern web development practices.', NULL, NULL, 'PHP, MySQL, JavaScript', '2026-09-19', '2026-11-04', 5, 0, 'open', '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(4, 3, NULL, 'Mobile App UX Research', 'Conduct user research and design a mobile app experience.', 'Understand user needs and design solutions.', NULL, NULL, 'UI/UX, Figma, User Research', '2026-09-12', '2026-10-20', 3, 0, 'open', '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(5, 2, NULL, 'E-commerce Website Development', 'Build a full-stack e-commerce platform with payment integration.', 'Learn modern web development practices.', NULL, NULL, 'PHP, MySQL, JavaScript', '2026-09-19', '2026-11-04', 5, 0, 'open', '2026-09-05 14:46:56', '2026-09-05 14:46:56'),
(6, 3, NULL, 'Mobile App UX Research', 'Conduct user research and design a mobile app experience.', 'Understand user needs and design solutions.', NULL, NULL, 'UI/UX, Figma, User Research', '2026-09-12', '2026-10-20', 3, 0, 'open', '2026-09-05 14:46:56', '2026-09-05 14:46:56');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `file_url` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT 'other',
  `file_size` bigint(20) DEFAULT 0,
  `is_public` tinyint(1) DEFAULT 1,
  `download_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`id`, `mentor_id`, `course_id`, `title`, `description`, `file_url`, `file_type`, `file_size`, `is_public`, `download_count`, `created_at`, `updated_at`) VALUES
(4, 2, 1, 'HTML Cheat Sheet', 'Quick reference for HTML tags and attributes.', '/resources/html-cheat-sheet.pdf', 'application/pdf', 0, 1, 45, '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(5, 2, 1, 'CSS Flexbox Guide', 'Complete guide to CSS Flexbox layout.', '/resources/css-flexbox-guide.pdf', 'application/pdf', 0, 1, 38, '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(6, 3, 2, 'UI Design System Template', 'Figma template for consistent UI design.', '/resources/ui-design-system.fig', 'application/octet-stream', 0, 1, 22, '2026-09-05 14:46:27', '2026-09-05 14:46:27'),
(7, 2, 1, 'HTML Cheat Sheet', 'Quick reference for HTML tags and attributes.', '/resources/html-cheat-sheet.pdf', 'application/pdf', 0, 1, 45, '2026-09-05 14:46:56', '2026-09-05 14:46:56'),
(8, 2, 1, 'CSS Flexbox Guide', 'Complete guide to CSS Flexbox layout.', '/resources/css-flexbox-guide.pdf', 'application/pdf', 0, 1, 38, '2026-09-05 14:46:56', '2026-09-05 14:46:56'),
(9, 3, 2, 'UI Design System Template', 'Figma template for consistent UI design.', '/resources/ui-design-system.fig', 'application/octet-stream', 0, 1, 22, '2026-09-05 14:46:56', '2026-09-05 14:46:56');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration` int(11) DEFAULT 60 COMMENT 'in minutes',
  `max_participants` int(11) DEFAULT 50,
  `meeting_link` varchar(255) DEFAULT NULL,
  `meeting_id` varchar(100) DEFAULT NULL,
  `meeting_password` varchar(50) DEFAULT NULL,
  `recording_url` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled') DEFAULT 'scheduled',
  `is_free` tinyint(1) DEFAULT 0,
  `price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `mentor_id`, `course_id`, `title`, `description`, `scheduled_at`, `duration`, `max_participants`, `meeting_link`, `meeting_id`, `meeting_password`, `recording_url`, `status`, `is_free`, `price`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'Introduction to Web Development', 'Live session covering web development.', '2026-09-07 20:28:16', 60, 20, 'https://meet.example.com/web-dev-intro', NULL, NULL, NULL, 'scheduled', 1, 0.00, '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(2, 3, 2, 'Advanced UI/UX Techniques', 'Deep dive into design principles.', '2026-09-10 20:28:16', 90, 10, 'https://meet.example.com/uiux-advanced', NULL, NULL, NULL, 'scheduled', 0, 19.99, '2026-09-05 14:43:16', '2026-09-05 14:43:16'),
(3, 2, 3, 'Python for Data Science', 'Hands-on Python session.', '2026-09-12 20:28:16', 120, 15, 'https://meet.example.com/python-datascience', NULL, NULL, NULL, 'scheduled', 0, 29.99, '2026-09-05 14:43:16', '2026-09-05 14:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) DEFAULT 'general',
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'SkillShare Hub', 'general', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(2, 'site_description', 'Learn from industry experts and accelerate your career', 'general', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(3, 'site_email', 'support@skillsharehub.com', 'general', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(4, 'site_phone', '+1 (555) 123-4567', 'general', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(5, 'site_address', '123 Learning Street, Education City, EC 12345', 'general', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(6, 'currency', 'USD', 'payments', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(7, 'currency_symbol', '$', 'payments', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(8, 'tax_rate', '0.00', 'payments', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(9, 'timezone', 'UTC', 'general', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(10, 'maintenance_mode', 'false', 'general', 0, '2026-09-04 18:09:35', '2026-09-04 18:09:35'),
(11, 'registration_enabled', '1', 'general', 0, '2026-09-05 14:46:28', '2026-09-05 14:46:28'),
(12, 'max_upload_size', '10485760', 'general', 0, '2026-09-05 14:46:28', '2026-09-05 14:46:28');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','mentor','fresher') DEFAULT 'fresher',
  `avatar` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `interests` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `is_verified` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `avatar`, `bio`, `title`, `skills`, `interests`, `phone`, `location`, `website`, `social_links`, `is_verified`, `is_active`, `reset_token`, `reset_token_expiry`, `remember_token`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Roshan Timalsina', 'timalsinaroshan345@gmail.com', '$2y$10$7OJyQKcLFU18xnTC2mw/AujRNuKPtT.V9wBx/ea71X1ae4f0hBGge', 'fresher', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, '2026-09-04 18:35:02', '2026-09-04 18:35:02'),
(2, 'Roshan Timalsina', 'roshan@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mentor', NULL, 'Experienced Full Stack Developer with 8+ years in web technologies.', 'Senior Full Stack Developer', 'PHP, JavaScript, React, Laravel, MySQL', NULL, NULL, 'Kathmandu', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, '2026-09-05 14:39:41', '2026-09-05 15:54:28'),
(3, 'Abiral Rai', 'abiral@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mentor', NULL, 'UI/UX Designer passionate about creating intuitive user experiences.', 'UI/UX Designer', 'UI/UX, Figma, Adobe XD, HTML/CSS', NULL, NULL, 'Pokhara', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, '2026-09-05 14:39:41', '2026-09-05 14:39:41'),
(4, 'Amit Sharma', 'amit@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fresher', NULL, 'Recent BCA graduate looking to learn web development.', 'BCA Graduate', 'HTML, CSS, JavaScript', NULL, NULL, 'Kathmandu', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, '2026-09-05 14:39:41', '2026-09-05 14:39:41'),
(5, 'Priya Patel', 'priya@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'fresher', NULL, 'BSc CSIT student interested in data science and AI.', 'BSc CSIT Student', 'Python, Data Analysis, Machine Learning', NULL, NULL, 'Lalitpur', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, '2026-09-05 14:39:41', '2026-09-05 14:39:41'),
(6, 'Admin User', 'admin@skillsharehub.com', '$2y$10$gPyCC4PKM6QaKE.mAwm5b.hk1/9EdHKn5OdEdWhmVsOrP017CJuLi', 'admin', NULL, 'System Administrator', 'Administrator', NULL, NULL, NULL, 'Kathmandu', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, '2026-09-05 15:52:09', '2026-09-05 15:52:28'),
(7, 'abiral rai', 'timalsinaroshan234@gmail.com', '$2y$10$Z.3bmY1jZILyeVgzrPrnC.ZpBn3NQKZ6bgqpfX8EyWgcsixqmxj1m', 'mentor', NULL, 'me as full stack developer', NULL, 'PHP, JavaScript, React, Laravel, MySQL, UI/UX, Figma, Adobe XD, HTML/CSS', 'web development gaming', '9768438873', 'banepa-4', NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, '2026-09-05 16:24:56', '2026-09-05 16:24:56');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `fresher_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_fields`
--
ALTER TABLE `academic_fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mentor` (`mentor_id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `graded_by` (`graded_by`),
  ADD KEY `idx_assignment` (`assignment_id`),
  ADD KEY `idx_student` (`fresher_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_post` (`post_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_published` (`is_published`),
  ADD KEY `idx_blog_posts_published` (`published_at`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_booking` (`fresher_id`,`session_id`),
  ADD KEY `idx_student` (`fresher_id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `certificate_code` (`certificate_code`),
  ADD UNIQUE KEY `unique_certificate` (`fresher_id`,`course_id`),
  ADD KEY `idx_student` (`fresher_id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_code` (`certificate_code`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_mentor` (`mentor_id`),
  ADD KEY `idx_field` (`field_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_courses_created` (`created_at`),
  ADD KEY `idx_courses_featured` (`featured`);

--
-- Indexes for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_module` (`module_id`),
  ADD KEY `idx_order` (`order_number`);

--
-- Indexes for table `course_modules`
--
ALTER TABLE `course_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_order` (`order_number`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`fresher_id`,`course_id`),
  ADD KEY `idx_student` (`fresher_id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_enrollments_status` (`status`);

--
-- Indexes for table `interview_questions`
--
ALTER TABLE `interview_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mentor` (`mentor_id`),
  ADD KEY `idx_field` (`field_id`),
  ADD KEY `idx_difficulty` (`difficulty`);

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_progress` (`fresher_id`,`lesson_id`),
  ADD KEY `idx_student` (`fresher_id`),
  ADD KEY `idx_lesson` (`lesson_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_receiver` (`receiver_id`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_parent` (`parent_id`),
  ADD KEY `idx_messages_created` (`created_at`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created` (`created_at`),
  ADD KEY `idx_notifications_created` (`created_at`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `idx_student` (`fresher_id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_transaction` (`transaction_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_payments_date` (`payment_date`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `idx_mentor` (`mentor_id`),
  ADD KEY `idx_student` (`fresher_id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_ratings_created` (`created_at`);

--
-- Indexes for table `research_applications`
--
ALTER TABLE `research_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`project_id`,`fresher_id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_student` (`fresher_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `research_projects`
--
ALTER TABLE `research_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mentor` (`mentor_id`),
  ADD KEY `idx_field` (`field_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mentor` (`mentor_id`),
  ADD KEY `idx_course` (`course_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mentor` (`mentor_id`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_scheduled` (`scheduled_at`),
  ADD KEY `idx_sessions_date` (`scheduled_at`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`),
  ADD KEY `idx_group` (`setting_group`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`is_active`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`fresher_id`,`course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `idx_student` (`fresher_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_fields`
--
ALTER TABLE `academic_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_comments`
--
ALTER TABLE `blog_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_modules`
--
ALTER TABLE `course_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `interview_questions`
--
ALTER TABLE `interview_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `research_applications`
--
ALTER TABLE `research_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `research_projects`
--
ALTER TABLE `research_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assignment_submissions`
--
ALTER TABLE `assignment_submissions`
  ADD CONSTRAINT `assignment_submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_2` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assignment_submissions_ibfk_3` FOREIGN KEY (`graded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_comments`
--
ALTER TABLE `blog_comments`
  ADD CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `blog_comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certificates`
--
ALTER TABLE `certificates`
  ADD CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `courses_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `academic_fields` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD CONSTRAINT `course_lessons_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `course_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_modules`
--
ALTER TABLE `course_modules`
  ADD CONSTRAINT `course_modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `interview_questions`
--
ALTER TABLE `interview_questions`
  ADD CONSTRAINT `interview_questions_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interview_questions_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `academic_fields` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD CONSTRAINT `lesson_progress_ibfk_1` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lesson_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `course_lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ratings_ibfk_4` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `research_applications`
--
ALTER TABLE `research_applications`
  ADD CONSTRAINT `research_applications_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `research_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `research_applications_ibfk_2` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `research_projects`
--
ALTER TABLE `research_projects`
  ADD CONSTRAINT `research_projects_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `research_projects_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `academic_fields` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resources_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`fresher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
--
-- Database: `skillswap`
--
CREATE DATABASE IF NOT EXISTS `skillswap` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skillswap`;

-- --------------------------------------------------------

--
-- Table structure for table `call_signals`
--

CREATE TABLE `call_signals` (
  `id` int(11) NOT NULL,
  `room_id` varchar(64) NOT NULL,
  `from_user` int(11) NOT NULL,
  `to_user` int(11) NOT NULL,
  `type` enum('offer','answer','ice','hangup','ring') NOT NULL,
  `payload` mediumtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `creator_id` int(11) NOT NULL,
  `location` varchar(255) NOT NULL,
  `date_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `creator_id`, `location`, `date_time`, `created_at`) VALUES
(2, 12, 'Copenhage', '2026-05-13 13:51:00', '2026-05-18 11:51:16'),
(3, 12, 'Copenhage', '2026-05-22 13:51:00', '2026-05-18 11:51:32'),
(4, 17, 'Copenhagen', '2026-05-30 15:50:00', '2026-05-29 08:47:50'),
(5, 17, 'ishoj', '2026-05-30 16:33:00', '2026-05-29 09:28:23');

-- --------------------------------------------------------

--
-- Table structure for table `event_participant`
--

CREATE TABLE `event_participant` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_participant`
--

INSERT INTO `event_participant` (`id`, `event_id`, `user_id`, `joined_at`) VALUES
(2, 4, 17, '2026-05-29 08:47:57'),
(3, 4, 18, '2026-05-29 08:52:51'),
(4, 5, 17, '2026-05-29 09:28:31');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`match_id`, `user1_id`, `user2_id`, `type`, `created_at`) VALUES
(3, 12, 14, 'skill_swap', '2026-05-25 02:03:23'),
(4, 16, 12, 'skill_swap', '2026-05-25 02:27:27'),
(5, 17, 18, 'skill_swap', '2026-05-29 11:03:25');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `MessageID` int(11) NOT NULL,
  `MessageText` varchar(255) NOT NULL,
  `IsRead` tinyint(1) DEFAULT 0,
  `Timestamp` datetime DEFAULT current_timestamp(),
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `FilePath` varchar(255) DEFAULT NULL,
  `IsEdited` tinyint(1) DEFAULT 0,
  `file_path` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `file_size` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`MessageID`, `MessageText`, `IsRead`, `Timestamp`, `sender_id`, `receiver_id`, `FilePath`, `IsEdited`, `file_path`, `file_name`, `file_type`, `file_size`) VALUES
(1, 'Hey, are you available?', 0, '2026-04-24 03:01:49', 1, 2, NULL, 0, NULL, NULL, NULL, NULL),
(2, 'Yes, what do you need?', 1, '2026-04-24 03:01:49', 2, 1, NULL, 0, NULL, NULL, NULL, NULL),
(3, 'Can we swap skills?', 0, '2026-04-24 03:01:49', 1, 2, NULL, 0, NULL, NULL, NULL, NULL),
(4, 'Sure, what skills do you have?', 1, '2026-04-24 03:01:49', 2, 1, NULL, 0, NULL, NULL, NULL, NULL),
(5, 'I know Python and MySQL', 0, '2026-04-24 03:01:49', 1, 2, NULL, 0, NULL, NULL, NULL, NULL),
(6, 'Hello this is a test message!', 0, '2026-05-05 13:08:51', 1, 2, NULL, 0, NULL, NULL, NULL, NULL),
(7, 'Hello this is a test message!', 0, '2026-05-05 13:11:11', 1, 2, NULL, 0, NULL, NULL, NULL, NULL),
(8, 'Hello this is a test message!', 0, '2026-05-05 13:12:55', 1, 2, NULL, 0, NULL, NULL, NULL, NULL),
(9, 'hi', 1, '2026-05-05 13:50:12', 3, 5, NULL, 0, NULL, NULL, NULL, NULL),
(10, 'how are you doing', 1, '2026-05-05 13:54:25', 5, 5, NULL, 0, NULL, NULL, NULL, NULL),
(11, 'hi', 0, '2026-05-05 14:08:34', 5, 4, NULL, 0, NULL, NULL, NULL, NULL),
(12, 'what are you doing', 0, '2026-05-05 14:22:39', 5, 4, NULL, 0, NULL, NULL, NULL, NULL),
(13, 'hi', 0, '2026-05-05 14:26:14', 5, 4, NULL, 1, NULL, NULL, NULL, NULL),
(14, 'hi', 0, '2026-05-05 14:28:11', 5, 3, NULL, 0, NULL, NULL, NULL, NULL),
(16, 'hi', 0, '2026-05-08 09:29:36', 5, 0, NULL, 0, NULL, NULL, NULL, NULL),
(17, 'hi', 0, '2026-05-08 09:29:43', 5, 0, NULL, 0, NULL, NULL, NULL, NULL),
(18, 'hi', 0, '2026-05-08 09:29:52', 5, 0, NULL, 0, NULL, NULL, NULL, NULL),
(19, 'hi', 0, '2026-05-08 09:39:51', 5, 0, NULL, 0, NULL, NULL, NULL, NULL),
(24, 'hello imran', 0, '2026-05-08 10:29:55', 6, 4, NULL, 0, NULL, NULL, NULL, NULL),
(25, 'hi', 0, '2026-05-08 10:32:43', 7, 6, NULL, 0, NULL, NULL, NULL, NULL),
(27, 'hi', 1, '2026-05-10 02:06:13', 8, 7, NULL, 0, NULL, NULL, NULL, NULL),
(28, 'hello', 1, '2026-05-10 02:06:27', 7, 8, NULL, 0, NULL, NULL, NULL, NULL),
(29, 'Screenshot 2026-03-27 011101.png', 1, '2026-05-10 02:33:14', 7, 8, NULL, 0, 'uploads/chat_files/cf_69ffd24a35f8e8.02778986.png', 'Screenshot 2026-03-27 011101.png', 'image/png', 94423),
(30, '1778152076_dashboard.php', 1, '2026-05-10 02:33:41', 8, 7, NULL, 0, 'uploads/chat_files/cf_69ffd265bb4e16.86012176.php', '1778152076_dashboard.php', 'application/octet-stream', 1233),
(31, 'gftdc', 1, '2026-05-12 13:38:56', 7, 8, NULL, 0, NULL, NULL, NULL, NULL),
(32, 'hi', 0, '2026-05-18 03:52:45', 11, 9, NULL, 0, NULL, NULL, NULL, NULL),
(33, 'Login Page.png', 0, '2026-05-18 04:06:25', 11, 9, NULL, 0, 'uploads/chat_files/cf_6a0a7421653710.17673243.png', 'Login Page.png', 'image/png', 1784393),
(34, 'Proposal_University of Greater Manchester.pdf', 0, '2026-05-18 04:11:05', 11, 9, NULL, 0, 'uploads/chat_files/cf_6a0a7539120cd2.89146682.pdf', 'Proposal_University of Greater Manchester.pdf', 'application/pdf', 158515),
(35, 'hi', 0, '2026-05-18 04:25:26', 11, 8, NULL, 0, NULL, NULL, NULL, NULL),
(37, 'hi', 0, '2026-05-18 14:23:58', 12, 9, NULL, 1, NULL, NULL, NULL, NULL),
(38, 'hi', 1, '2026-05-19 15:59:32', 13, 12, NULL, 0, NULL, NULL, NULL, NULL),
(39, 'hlw', 0, '2026-05-19 16:14:18', 12, 9, NULL, 0, NULL, NULL, NULL, NULL),
(40, 'hi', 0, '2026-05-25 02:03:41', 12, 14, NULL, 0, NULL, NULL, NULL, NULL),
(41, 'hi', 1, '2026-05-25 02:39:10', 12, 16, NULL, 0, NULL, NULL, NULL, NULL),
(43, 'hi', 1, '2026-05-25 03:07:00', 16, 12, NULL, 0, NULL, NULL, NULL, NULL),
(44, 'Screenshot 2026-03-27 011604.png', 1, '2026-05-25 03:07:20', 16, 12, NULL, 0, 'uploads/chat_files/cf_6a13a0c8bca4f4.81726150.png', 'Screenshot 2026-03-27 011604.png', 'image/png', 111063),
(45, 'hi', 1, '2026-05-29 11:03:32', 17, 18, NULL, 0, NULL, NULL, NULL, NULL),
(46, 'Screenshot 2024-09-28 212029.png', 1, '2026-05-29 11:26:44', 17, 18, NULL, 0, 'uploads/chat_files/cf_6a195bd4d9dd41.68798785.png', 'Screenshot 2024-09-28 212029.png', 'image/png', 308943);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `message` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `message_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`notification_id`, `user_id`, `type`, `created_at`, `message`, `is_read`, `message_text`) VALUES
(3, 14, NULL, '2026-05-25 02:03:23', 0, 0, NULL),
(4, 12, NULL, '2026-05-25 02:27:27', 0, 1, NULL),
(5, 12, NULL, '2026-05-25 02:38:23', 0, 1, NULL),
(6, 16, NULL, '2026-05-25 02:39:02', 0, 1, NULL),
(7, 14, NULL, '2026-05-25 02:44:11', 0, 0, NULL),
(8, 17, 'swap_request', '2026-05-29 10:53:54', NULL, 1, 'Radiah Anan sent you a swap request!'),
(9, 18, 'swap_request', '2026-05-29 11:03:25', NULL, 1, 'Jui Talukder accepted your swap request! You can now chat.');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(2, 17, '10165ecdcdf396df312100d7909f2ba920b5944891c1a92067e3031076753dc7', '2026-05-29 12:58:14', '2026-05-29 09:58:14');

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `rating_id` int(11) NOT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `reviewed_id` int(11) DEFAULT NULL,
  `stars` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rating`
--

INSERT INTO `rating` (`rating_id`, `reviewer_id`, `reviewed_id`, `stars`) VALUES
(1, 3, 1, 4),
(2, 3, 5, 4),
(3, 3, 7, 5),
(4, 3, 8, 2),
(5, 11, 8, 5),
(6, 12, 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `user1_id` int(11) NOT NULL,
  `user2_id` int(11) NOT NULL,
  `skill_offered` varchar(100) NOT NULL,
  `skill_requested` varchar(100) NOT NULL,
  `date_time` datetime NOT NULL,
  `status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `user1_id`, `user2_id`, `skill_offered`, `skill_requested`, `date_time`, `status`) VALUES
(3, 9, 10, 'hacking', 'curd', '2026-05-08 15:29:00', 'Accepted'),
(4, 13, 12, 'database', 'networking', '2026-05-08 13:53:00', 'Accepted'),
(5, 9, 10, 'coding', 'html', '2026-05-30 10:55:00', 'Rejected'),
(6, 13, 12, 'class diagram', 'UI', '2026-05-10 15:27:00', 'Pending'),
(8, 14, 10, 'frontend', 'java', '2026-05-13 15:30:00', 'Pending'),
(9, 12, 11, 'python', 'programming', '2026-05-04 15:34:00', 'Pending'),
(10, 10, 14, 'database', 'curd', '2026-05-12 15:35:00', 'Pending'),
(11, 17, 18, 'python', 'sql', '2026-11-12 01:02:00', 'Accepted'),
(12, 17, 18, 'HTML/CSS', 'sql', '2026-05-30 17:35:00', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skill_id`, `skill_name`) VALUES
(16, 'art'),
(17, 'dance'),
(9, 'Data Science'),
(14, 'English Writing'),
(11, 'Graphic Design'),
(5, 'HTML/CSS'),
(6, 'Java'),
(2, 'JavaScript'),
(18, 'latte art'),
(10, 'Machine Learning'),
(4, 'MySQL'),
(7, 'Networking'),
(13, 'Photography'),
(3, 'PHP'),
(15, 'Public Speaking'),
(1, 'Python'),
(8, 'UI/UX Design'),
(12, 'Video Editing');

-- --------------------------------------------------------

--
-- Table structure for table `skill_types`
--

CREATE TABLE `skill_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skill_types`
--

INSERT INTO `skill_types` (`type_id`, `type_name`) VALUES
(7, 'Graphic Design'),
(6, 'JavaScript'),
(2, 'Learn'),
(4, 'MySQL Databases'),
(3, 'PHP Web Development'),
(1, 'Teach'),
(5, 'UI/UX Design');

-- --------------------------------------------------------

--
-- Table structure for table `swaps`
--

CREATE TABLE `swaps` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `swaps`
--

INSERT INTO `swaps` (`id`, `sender_id`, `receiver_id`, `message`, `status`, `created_at`) VALUES
(1, 12, 13, NULL, 'accepted', '2026-05-19 13:16:25'),
(2, 14, 12, NULL, 'accepted', '2026-05-19 14:17:39'),
(4, 16, 12, NULL, 'accepted', '2026-05-25 00:38:23'),
(5, 18, 17, NULL, 'accepted', '2026-05-29 08:53:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `user_location` varchar(100) DEFAULT NULL,
  `rating_average` decimal(2,1) DEFAULT 0.0,
  `total_reviews` int(11) DEFAULT 0,
  `is_verified` tinyint(1) DEFAULT 0,
  `account_status` enum('active','suspended','deleted') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `username`, `first_name`, `last_name`, `bio`, `location`, `profile_picture`, `city`, `user_location`, `rating_average`, `total_reviews`, `is_verified`, `account_status`, `created_at`, `updated_at`, `country`) VALUES
(4, '', '', NULL, 'Alex', 'Morgan', 'Full-stack web developer specializing in PHP backend engines and modern CSS layout structures.', NULL, NULL, NULL, NULL, 4.8, 5, 0, 'active', '2026-05-18 01:39:24', '2026-05-18 01:39:24', NULL),
(8, 'ih0162445@gmail.com', '$2y$10$Bn/v4zHFKEfMxwCI/aEH4OyI4KYRiOmgmELBXtDYl560eCW7PQp1W', 'imran', 'Imran', 'Hossain', 'i am so lonely', NULL, NULL, 'Taastrup', NULL, 3.7, 3, 0, 'active', '2026-05-12 11:23:25', '2026-05-18 11:49:51', NULL),
(9, 'asikur@gamil.com', '$2y$10$oBc8FuEd6E3RiUuwkc2ivOg9OmE6GhS2JyGNNfg.LeGbQllImDcnK', 'asik', 'Asikur', 'Rahman', 'suuuuuuui', NULL, NULL, 'Albertslund', NULL, 0.0, 0, 0, 'active', '2026-05-12 11:27:07', '2026-05-12 11:28:57', NULL),
(10, 'krtikaasingh@gmail.com', '$2y$10$Pj76ZjXRNYm7pwjJd21YYuNImyiI./uG5CkTz7VpK8XBxGkpwECry', 'kritikaa285', 'Kritika ', 'Singh', 'Bye TTYL\r\n', NULL, '/skillswap/uploads/profile_pictures/user_10_1778678094.webp', 'Dhangadhi', NULL, 0.0, 0, 0, 'active', '2026-05-13 13:12:54', '2026-05-13 13:15:30', NULL),
(11, 'rahmanasikur@gmail.com', '$2y$10$/R8eafN/mXo0hnI2NxFm5uJS1ThOR.So82YpRxSN.sYq3xxDsB8A.', 'admin', 'Asikur', 'Rahman', '', '', NULL, 'Brondby Kommune', '', 0.0, 0, 0, 'active', '2026-05-18 01:47:13', '2026-05-18 02:20:45', NULL),
(12, 'rahmanasikur1@gmail.com', '$2y$10$KEYxEe2wf4HwnvNXnVje6elsyrISx2DDE2Ip2Te5pEjzl3hEJiLSq', 'asik12', 'asik', 'fhf', '', NULL, NULL, 'Brondby Kommune', '', 0.0, 0, 0, 'active', '2026-05-18 11:47:54', '2026-05-18 11:51:52', NULL),
(13, 'asasik201@gmail.com', '$2y$10$hLxLiSfKmOPo/aVpYpx9RegmY9775E3Cl.zsjf4VNmwK.WYpiqchy', 'asik1', 'Asikur', 'Rahman', '', NULL, NULL, 'Dhaka', '', 0.0, 0, 0, 'active', '2026-05-19 13:10:22', '2026-05-19 13:11:06', NULL),
(14, 'jhondoe@gmail.com', '$2y$10$avWdVTT.Mzr14o/HXcWMWuFVZKr.AALaNnqy.Mc/SEYURH5rrWa.K', 'jhon', 'Jhon', 'Doe', '', NULL, NULL, '', '', 0.0, 0, 0, 'active', '2026-05-19 14:16:32', '2026-05-19 14:17:28', NULL),
(15, 'riff199901@gmail.com', '$2y$10$n.f7gLdbTBmiEsEnChnpTeI5icE5gy20dKYfNWeQIZwV4TKco9XnS', 'Riff99', 'Rifat', 'Hosen', NULL, NULL, NULL, 'Dhaka', NULL, 0.0, 0, 0, 'active', '2026-05-25 00:23:39', '2026-05-25 00:23:39', NULL),
(16, 'riff9@gmail.com', '$2y$10$rqmYwtA.BNbj1eO5zE6unuzbF2fSUVhLHa2wLObQhRRKmppxBkLO2', 'Riff9', 'Rifat', 'Hosen', '', NULL, NULL, '', '', 0.0, 0, 0, 'active', '2026-05-25 00:24:54', '2026-05-25 00:25:24', NULL),
(17, 'juitalukder01@gmail.com', '$2y$10$A8HWF5KF0YLc..EBuoUdsultXJTv8esS2oNL13qKrdTey753yoaC6', 'Jui01', 'Jui', 'Talukder', '', NULL, NULL, 'Copenhagen', '', 0.0, 0, 0, 'active', '2026-05-29 08:20:54', '2026-05-29 08:40:51', NULL),
(18, 'radiah01@gmail.com', '$2y$10$NsfAgDyU4bVTzIAsX45xTejs6cHVKZo7m1aNL277NzMBwiKrWUiVK', 'radiah01', 'Radiah', 'Anan', '', NULL, NULL, 'ishoj', 'ishoj', 0.0, 0, 0, 'active', '2026-05-29 08:52:24', '2026-05-29 08:53:38', NULL),
(19, 'kritika01@gmail.com', '$2y$10$Hd3m6pOrC8fo8Zum9kHqM.kqz5fj7M48CUnlmVUV5OtAZ0kT0Uv.W', 'kritika01', 'Kritika', 'Singh', NULL, NULL, NULL, 'Alberslund', NULL, 0.0, 0, 0, 'active', '2026-05-29 08:55:30', '2026-05-29 08:55:30', NULL),
(20, 'imran01@gmail.com', '$2y$10$pX7JM7y8JUuFV9yqTjM6vOKwaYdeWKj.Bb0ubtaQhq4B8D8ndL11u', 'imran01', 'Imran', 'Hossen', NULL, NULL, NULL, 'Greve', NULL, 0.0, 0, 0, 'active', '2026-05-29 08:56:14', '2026-05-29 08:56:14', NULL),
(21, 'asikur01@gmail.com', '$2y$10$0p6WZlGZaXfrv.D6s/QMw.zdqoC55u3h0O/4b3miIkW2FvguiE3hC', 'asikur01', 'Asikur', 'Rahman', NULL, NULL, NULL, 'Vesterbro', NULL, 0.0, 0, 0, 'active', '2026-05-29 08:57:12', '2026-05-29 08:57:12', NULL),
(22, 'ih12@gmail.com', '$2y$10$CtbOQ1R8ojsUAsb50pRQj.yEKbyF8dM7.7aLpV.rdGJee4A5sxbme', 'admi', 'imran', 'hossain', NULL, NULL, NULL, 'di', NULL, 0.0, 0, 0, 'active', '2026-05-29 09:18:52', '2026-05-29 09:18:52', NULL),
(23, 'harithapa123@gmail.com', '$2y$10$ewDGCl/2ox6Wsu4fHo1vsO17NL6IqFNNcmtpC/F3nHaVRXGYiqbhK', 'harithapa1', 'hari', 'thapa', NULL, NULL, NULL, 'banepa-5', NULL, 0.0, 0, 0, 'active', '2026-08-30 15:46:16', '2026-08-30 15:46:16', NULL),
(24, 'timalsinaroshan345@gmail.com', '$2y$10$YJz/cikEVBRhlTEQ3lDEhO10H7oqxLwQp2HDZdQ9tYzJAYkSfTW.O', 'roshan123', 'Roshan', 'Timalsina', NULL, NULL, NULL, 'banepa-5', NULL, 0.0, 0, 0, 'active', '2026-08-30 15:47:25', '2026-08-30 15:47:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_skills`
--

CREATE TABLE `user_skills` (
  `user_skill_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `skill_id` int(11) DEFAULT NULL,
  `level_name` varchar(50) DEFAULT NULL,
  `type_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_skills`
--

INSERT INTO `user_skills` (`user_skill_id`, `user_id`, `skill_id`, `level_name`, `type_name`) VALUES
(3, 2, 3, 'Advanced', 'Teach'),
(4, 2, 1, 'Beginner', 'Learn'),
(6, 3, 2, 'Intermediate', 'Teach'),
(7, 2, 3, 'Beginner', 'Learn'),
(11, 1, 1, 'Expert', 'Teach'),
(13, 1, 7, 'Advanced', 'Learn'),
(15, 1, 8, 'Advanced', 'Learn'),
(16, 1, 3, 'Beginner', 'Learn'),
(18, 1, 6, 'Advanced', 'Teach'),
(19, 4, 2, 'Intermediate', 'learn'),
(20, 4, 1, 'Expert', 'Teach'),
(21, 4, 2, 'Intermediate', 'Teach'),
(22, 4, 4, 'Beginner', 'Learn'),
(31, 11, 9, 'Beginner', 'Teach'),
(32, 11, 11, 'Beginner', 'Learn'),
(47, 13, 14, 'Beginner', 'Teach'),
(48, 13, 9, 'Beginner', 'Learn'),
(58, 14, 6, 'Beginner', 'Teach'),
(59, 14, 14, 'Beginner', 'Learn'),
(64, 12, 6, 'Beginner', 'Teach'),
(65, 12, 4, 'Beginner', 'Learn'),
(66, 16, 4, 'Beginner', 'Teach'),
(67, 16, 6, 'Beginner', 'Learn'),
(71, 18, 11, 'Beginner', 'Teach'),
(72, 18, 5, 'Beginner', 'Learn'),
(73, 17, 5, 'Beginner', 'Teach'),
(74, 17, 11, 'Beginner', 'Learn'),
(75, 17, 17, 'Beginner', 'Teach'),
(76, 17, 18, 'Beginner', 'Learn');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `call_signals`
--
ALTER TABLE `call_signals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_room` (`room_id`),
  ADD KEY `idx_touser` (`to_user`,`created_at`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Indexes for table `event_participant`
--
ALTER TABLE `event_participant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_join` (`event_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `user1_id` (`user1_id`),
  ADD KEY `user2_id` (`user2_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`MessageID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewed_id` (`reviewed_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user1_id` (`user1_id`),
  ADD KEY `user2_id` (`user2_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skill_id`),
  ADD UNIQUE KEY `skill_name` (`skill_name`);

--
-- Indexes for table `skill_types`
--
ALTER TABLE `skill_types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- Indexes for table `swaps`
--
ALTER TABLE `swaps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD PRIMARY KEY (`user_skill_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `level_name` (`level_name`),
  ADD KEY `type_name` (`type_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `call_signals`
--
ALTER TABLE `call_signals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `event_participant`
--
ALTER TABLE `event_participant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `skill_types`
--
ALTER TABLE `skill_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `swaps`
--
ALTER TABLE `swaps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_skills`
--
ALTER TABLE `user_skills`
  MODIFY `user_skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_participant`
--
ALTER TABLE `event_participant`
  ADD CONSTRAINT `event_participant_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_participant_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `pr_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
--
-- Database: `skilshopdb`
--
CREATE DATABASE IF NOT EXISTS `skilshopdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skilshopdb`;

-- --------------------------------------------------------

--
-- Table structure for table `admin_actions`
--

CREATE TABLE `admin_actions` (
  `action_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `target_user_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_actions`
--

INSERT INTO `admin_actions` (`action_id`, `admin_id`, `action_type`, `target_user_id`, `description`, `created_at`) VALUES
(1, 3, 'session_review', NULL, 'Reviewed and approved new session submissions', '2024-12-27 08:15:00'),
(2, 3, 'category_added', NULL, 'Added new category: Crafts', '2024-12-26 05:15:00'),
(3, 3, 'user_verified', 4, 'Verified instructor credentials for Michael Chen', '2024-12-25 03:15:00'),
(4, 3, 'impact_updated', NULL, 'Updated CO2 impact factors for all categories', '2024-12-24 10:15:00'),
(5, 3, 'booking_resolved', 8, 'Resolved booking dispute for user', '2024-12-23 07:45:00'),
(6, 3, 'session_canceled', NULL, 'Canceled session due to instructor unavailability', '2024-12-22 04:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL COMMENT 'Dot notation key (e.g., user.theme, booking.currency)',
  `setting_value` text NOT NULL COMMENT 'Setting value stored as text',
  `setting_type` enum('string','int','float','bool','json') NOT NULL DEFAULT 'string' COMMENT 'Data type for type casting',
  `setting_group` varchar(50) NOT NULL COMMENT 'Group name (e.g., security, booking, ui)',
  `description` text DEFAULT NULL COMMENT 'Human-readable description',
  `is_public` tinyint(1) DEFAULT 0 COMMENT 'Can be accessed by frontend?',
  `user_id` int(11) DEFAULT NULL COMMENT 'User ID for user-specific settings, NULL for global',
  `validation_rules` text DEFAULT NULL COMMENT 'JSON validation rules',
  `default_value` text DEFAULT NULL COMMENT 'Default value for this setting',
  `updated_by` int(11) DEFAULT NULL COMMENT 'User ID who last updated this setting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `learner_id` int(11) NOT NULL,
  `num_seats` int(11) NOT NULL DEFAULT 1 CHECK (`num_seats` > 0),
  `status` enum('pending','accepted','declined','canceled') NOT NULL DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `session_id`, `learner_id`, `num_seats`, `status`, `requested_at`, `responded_at`) VALUES
(1, 11, 1, 1, 'accepted', '2024-12-10 04:15:00', '2024-12-10 05:15:00'),
(2, 11, 7, 1, 'accepted', '2024-12-11 08:15:00', '2024-12-11 09:15:00'),
(3, 11, 9, 1, 'accepted', '2024-12-12 03:15:00', '2024-12-12 04:15:00'),
(4, 11, 11, 1, 'accepted', '2024-12-13 10:15:00', '2024-12-13 11:15:00'),
(5, 12, 1, 1, 'accepted', '2024-12-14 02:15:00', '2024-12-14 03:15:00'),
(6, 12, 8, 1, 'accepted', '2024-12-14 04:15:00', '2024-12-14 05:15:00'),
(7, 12, 10, 1, 'accepted', '2024-12-15 06:15:00', '2024-12-15 07:15:00'),
(8, 13, 7, 1, 'accepted', '2024-12-16 08:15:00', '2024-12-16 09:15:00'),
(9, 13, 9, 1, 'accepted', '2024-12-17 05:15:00', '2024-12-17 06:15:00'),
(10, 13, 12, 1, 'accepted', '2024-12-18 03:15:00', '2024-12-18 04:15:00'),
(11, 13, 13, 1, 'accepted', '2024-12-18 09:15:00', '2024-12-18 10:15:00'),
(12, 14, 8, 1, 'accepted', '2024-12-19 04:15:00', '2024-12-19 05:15:00'),
(13, 14, 11, 1, 'accepted', '2024-12-19 07:15:00', '2024-12-19 08:15:00'),
(14, 14, 14, 1, 'accepted', '2024-12-20 02:15:00', '2024-12-20 03:15:00'),
(15, 15, 1, 1, 'accepted', '2024-12-05 06:15:00', '2024-12-05 07:15:00'),
(16, 15, 10, 1, 'accepted', '2024-12-06 04:15:00', '2024-12-06 05:15:00'),
(17, 15, 13, 1, 'accepted', '2024-12-07 08:15:00', '2024-12-07 09:15:00'),
(18, 1, 7, 1, 'accepted', '2025-01-05 04:15:00', '2025-01-05 05:15:00'),
(19, 1, 8, 1, 'accepted', '2025-01-06 08:15:00', '2025-01-06 09:15:00'),
(20, 1, 9, 1, 'pending', '2025-01-08 03:15:00', NULL),
(21, 1, 10, 1, 'pending', '2025-01-09 05:15:00', NULL),
(22, 2, 1, 1, 'accepted', '2025-01-07 06:15:00', '2025-01-07 07:15:00'),
(23, 2, 11, 1, 'accepted', '2025-01-08 09:15:00', '2025-01-08 10:15:00'),
(24, 2, 12, 1, 'pending', '2025-01-10 04:15:00', NULL),
(25, 3, 13, 1, 'accepted', '2025-01-09 02:15:00', '2025-01-09 03:15:00'),
(26, 3, 14, 1, 'pending', '2025-01-11 07:15:00', NULL),
(27, 4, 7, 1, 'accepted', '2025-01-10 05:15:00', '2025-01-10 06:15:00'),
(28, 4, 8, 1, 'accepted', '2025-01-11 08:15:00', '2025-01-11 09:15:00'),
(29, 4, 1, 1, 'declined', '2025-01-12 03:15:00', '2025-01-12 04:15:00'),
(30, 5, 9, 1, 'accepted', '2025-01-12 09:15:00', '2025-01-12 10:15:00'),
(31, 5, 10, 1, 'pending', '2025-01-13 04:15:00', NULL),
(32, 6, 11, 1, 'accepted', '2025-01-08 07:15:00', '2025-01-08 08:15:00'),
(33, 6, 12, 1, 'accepted', '2025-01-09 10:15:00', '2025-01-09 11:15:00'),
(34, 6, 1, 1, 'canceled', '2025-01-07 04:15:00', '2025-01-07 05:15:00'),
(35, 10, 13, 1, 'accepted', '2025-01-14 06:15:00', '2025-01-14 07:15:00'),
(36, 10, 14, 1, 'accepted', '2025-01-15 03:15:00', '2025-01-15 04:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'Web Development', 'Learn modern web development skills including HTML, CSS, JavaScript, and frameworks'),
(2, 'Data Science', 'Master data analysis, machine learning, and statistical methods'),
(3, 'Design', 'UI/UX design, graphic design, and creative skills'),
(4, 'Business', 'Entrepreneurship, marketing, and business management skills'),
(5, 'Photography', 'Digital photography, editing, and visual storytelling'),
(6, 'Cooking', 'Culinary arts, baking, and food preparation techniques'),
(7, 'Fitness', 'Personal training, yoga, and wellness coaching'),
(8, 'Music', 'Instrument lessons, music theory, and production'),
(9, 'Languages', 'Learn new languages and improve communication skills'),
(10, 'Crafts', 'DIY projects, woodworking, and handmade crafts');

-- --------------------------------------------------------

--
-- Table structure for table `impact_factors`
--

CREATE TABLE `impact_factors` (
  `id` int(11) NOT NULL,
  `skill_category` varchar(100) NOT NULL COMMENT 'Skill category name (e.g., Cooking, Programming)',
  `co2_saved_per_participant_kg` decimal(8,2) NOT NULL DEFAULT 0.00 COMMENT 'Estimated CO2 saved per participant in kg',
  `source_note` text DEFAULT NULL COMMENT 'Citation or methodology note',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Is this factor currently being used?',
  `updated_by` int(11) DEFAULT NULL COMMENT 'User ID who last updated this factor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `impact_factors`
--

INSERT INTO `impact_factors` (`id`, `skill_category`, `co2_saved_per_participant_kg`, `source_note`, `is_active`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, '1', 2.50, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(2, '2', 3.00, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(3, '3', 1.80, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(4, '4', 2.00, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(5, '5', 1.50, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(6, '6', 2.20, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(7, '7', 1.90, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(8, '8', 1.70, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(9, '9', 2.10, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(10, '10', 1.60, NULL, 1, NULL, '2026-08-27 16:55:41', '2026-08-27 16:55:41');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `learner_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `session_id`, `learner_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 11, 1, 5, 'Excellent introduction to web development! Very clear explanations.', '2024-12-16 04:15:00', '2026-08-27 16:55:42'),
(2, 11, 7, 4, 'Great session, learned a lot. Would love more advanced topics.', '2024-12-16 08:15:00', '2026-08-27 16:55:42'),
(3, 11, 9, 5, 'Perfect for beginners. Instructor was very patient and helpful.', '2024-12-17 03:15:00', '2026-08-27 16:55:42'),
(4, 12, 1, 5, 'Outstanding workshop! Practical examples were very useful.', '2024-12-19 04:15:00', '2026-08-27 16:55:42'),
(5, 12, 8, 4, 'Good content, but could use more time for hands-on practice.', '2024-12-19 09:15:00', '2026-08-27 16:55:42'),
(6, 12, 10, 5, 'Loved it! Now I can create beautiful charts for my projects.', '2024-12-20 05:15:00', '2026-08-27 16:55:42'),
(7, 13, 7, 5, 'Amazing instructor! Learned so much about design principles.', '2024-12-21 07:15:00', '2026-08-27 16:55:42'),
(8, 13, 9, 4, 'Very informative session. Would recommend to anyone interested in design.', '2024-12-21 10:15:00', '2026-08-27 16:55:42'),
(9, 13, 12, 5, 'Best design workshop I have attended. Highly practical!', '2024-12-22 04:15:00', '2026-08-27 16:55:42'),
(10, 14, 8, 4, 'Useful strategies for growing social media presence.', '2024-12-23 06:15:00', '2026-08-27 16:55:42'),
(11, 14, 11, 5, 'Excellent! Already implementing what I learned for my business.', '2024-12-23 09:15:00', '2026-08-27 16:55:42'),
(12, 14, 14, 4, 'Good session with actionable tips.', '2024-12-24 03:15:00', '2026-08-27 16:55:42'),
(13, 15, 1, 5, 'My photos have improved dramatically! Thank you!', '2024-12-11 08:15:00', '2026-08-27 16:55:42'),
(14, 15, 10, 5, 'Fantastic workshop. Learned the rule of thirds and much more.', '2024-12-12 04:15:00', '2026-08-27 16:55:42'),
(15, 15, 13, 4, 'Great techniques for better composition. Highly recommended.', '2024-12-13 05:15:00', '2026-08-27 16:55:42');

-- --------------------------------------------------------

--
-- Table structure for table `skill_sessions`
--

CREATE TABLE `skill_sessions` (
  `session_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL,
  `fee_type` enum('free','paid') NOT NULL DEFAULT 'free',
  `fee_amount` decimal(10,2) NOT NULL DEFAULT 0.00 CHECK (`fee_amount` >= 0),
  `location_type` enum('online','in-person') NOT NULL,
  `city` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `online_link` text DEFAULT NULL,
  `photo_url` text DEFAULT NULL,
  `event_datetime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total_capacity` int(11) NOT NULL CHECK (`total_capacity` > 0),
  `capacity_remaining` int(11) NOT NULL CHECK (`capacity_remaining` >= 0 and `capacity_remaining` <= `total_capacity`),
  `status` enum('upcoming','completed','canceled') NOT NULL DEFAULT 'upcoming',
  `sustainability_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_sessions`
--

INSERT INTO `skill_sessions` (`session_id`, `instructor_id`, `category_id`, `title`, `description`, `duration_minutes`, `fee_type`, `fee_amount`, `location_type`, `city`, `address`, `online_link`, `photo_url`, `event_datetime`, `total_capacity`, `capacity_remaining`, `status`, `sustainability_description`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'React.js Fundamentals', 'Learn the basics of React and build your first component-based application', 120, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/abc-defg-hij', NULL, '2025-01-15 12:15:00', 25, 15, 'upcoming', 'Online learning reduces travel emissions and promotes sustainable education', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(2, 2, 1, 'React.js Fundamentals', 'Learn the basics of React and build your first component-based application', 120, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/abc-defg-hij', NULL, '2025-01-15 12:15:00', 25, 15, 'upcoming', 'Online learning reduces travel emissions and promotes sustainable education', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(3, 4, 1, 'Full-Stack JavaScript Workshop', 'Build a complete web application using Node.js, Express, and MongoDB', 180, 'paid', 45.00, 'in-person', 'Toronto', '123 Tech Street, Toronto', NULL, NULL, '2025-01-20 08:15:00', 15, 10, 'upcoming', 'Local in-person sessions reduce long-distance travel impact', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(4, 5, 1, 'CSS Grid & Flexbox Mastery', 'Master modern CSS layout techniques for responsive design', 90, 'free', 0.00, 'online', NULL, NULL, 'https://zoom.us/j/123456789', NULL, '2025-01-18 13:15:00', 30, 25, 'upcoming', 'Virtual workshops eliminate commute emissions', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(5, 4, 2, 'Introduction to Python for Data Science', 'Get started with Python programming for data analysis', 150, 'paid', 35.00, 'in-person', 'Vancouver', '456 Data Ave, Vancouver', NULL, NULL, '2025-01-22 07:15:00', 20, 12, 'upcoming', 'Community-based learning reduces carbon footprint', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(6, 5, 2, 'Machine Learning Basics', 'Understand fundamental ML algorithms and their applications', 120, 'free', 0.00, 'online', NULL, NULL, 'https://teams.microsoft.com/xyz', NULL, '2025-01-25 11:15:00', 40, 35, 'upcoming', 'Digital learning platform saves energy and resources', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(7, 6, 3, 'UI/UX Design Principles', 'Learn user-centered design and create beautiful interfaces', 120, 'paid', 40.00, 'in-person', 'Montreal', '789 Creative Blvd, Montreal', NULL, NULL, '2025-01-17 09:15:00', 12, 8, 'upcoming', 'Local workshops minimize transportation emissions', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(8, 7, 3, 'Figma for Beginners', 'Master the basics of Figma for UI design and prototyping', 90, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/design-123', NULL, '2025-01-19 12:45:00', 25, 20, 'upcoming', 'Remote learning reduces environmental impact', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(9, 8, 4, 'Digital Marketing Fundamentals', 'Learn effective online marketing strategies for small businesses', 120, 'paid', 50.00, 'in-person', 'Calgary', '321 Business Park, Calgary', NULL, NULL, '2025-01-23 10:15:00', 18, 15, 'upcoming', 'Local skill-sharing builds sustainable community networks', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(10, 9, 4, 'Start Your Own Business', 'Essential steps to launch and grow a successful startup', 180, 'free', 0.00, 'online', NULL, NULL, 'https://zoom.us/j/business101', NULL, '2025-01-21 08:15:00', 50, 45, 'upcoming', 'Online education platform reduces travel and facility energy use', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(11, 10, 5, 'Portrait Photography Basics', 'Learn techniques for capturing stunning portraits', 120, 'paid', 55.00, 'in-person', 'Toronto', '555 Photo Studio, Toronto', NULL, NULL, '2025-01-24 05:15:00', 10, 6, 'upcoming', 'Small local classes reduce per-capita environmental impact', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(12, 2, 1, 'HTML & CSS Crash Course', 'Quick introduction to web development fundamentals', 90, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/html-101', NULL, '2024-12-15 12:15:00', 30, 0, 'completed', 'Online learning eliminates commute emissions', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(13, 4, 2, 'Data Visualization with Python', 'Create compelling data visualizations using Matplotlib and Seaborn', 120, 'paid', 40.00, 'in-person', 'Toronto', '123 Data Street, Toronto', NULL, NULL, '2024-12-18 08:15:00', 15, 0, 'completed', 'Local workshops reduce travel carbon footprint', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(14, 6, 3, 'Graphic Design Essentials', 'Master the principles of effective graphic design', 150, 'free', 0.00, 'online', NULL, NULL, 'https://zoom.us/j/design-basics', NULL, '2024-12-20 10:15:00', 25, 0, 'completed', 'Virtual sessions save energy and resources', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(15, 8, 4, 'Social Media Marketing', 'Build an effective social media strategy for your business', 120, 'paid', 45.00, 'in-person', 'Vancouver', '789 Marketing Ave, Vancouver', NULL, NULL, '2024-12-22 07:15:00', 20, 0, 'completed', 'Community learning reduces environmental impact', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(16, 10, 5, 'Photography Composition', 'Learn the art of composing beautiful photographs', 90, 'free', 0.00, 'online', NULL, NULL, 'https://meet.google.com/photo-comp', NULL, '2024-12-10 13:15:00', 35, 0, 'completed', 'Online education platform minimizes carbon emissions', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(17, 7, 6, 'Italian Cooking Masterclass', 'Learn to cook authentic Italian dishes', 150, 'paid', 60.00, 'in-person', 'Toronto', '100 Culinary St, Toronto', NULL, NULL, '2025-01-26 11:15:00', 12, 8, 'upcoming', 'Local food education promotes sustainable eating habits', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(18, 9, 6, 'Vegan Baking Workshop', 'Delicious plant-based baking techniques', 120, 'free', 0.00, 'online', NULL, NULL, 'https://zoom.us/j/vegan-bake', NULL, '2025-01-28 09:15:00', 40, 38, 'upcoming', 'Virtual cooking classes reduce travel and promote sustainability', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(19, 5, 7, 'Yoga for Beginners', 'Introduction to yoga poses and breathing techniques', 60, 'free', 0.00, 'in-person', 'Montreal', '200 Wellness Center, Montreal', NULL, NULL, '2025-01-27 03:15:00', 15, 10, 'upcoming', 'Local fitness classes build healthy, sustainable communities', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(20, 2, 1, 'Advanced JavaScript Patterns', 'Deep dive into advanced JS concepts', 120, 'paid', 50.00, 'online', NULL, NULL, 'https://meet.google.com/js-advanced', NULL, '2025-01-16 12:15:00', 20, 20, 'canceled', 'Session canceled due to instructor availability', '2026-08-27 16:55:41', '2026-08-27 16:55:41'),
(21, 27, 1, 'git and github', 'hhhjhweusEDFEFQ', 60, 'free', 0.00, 'in-person', 'banepa-5', 'banepa-5', NULL, NULL, '2026-08-28 03:10:00', 14, 14, 'upcoming', 'AER fstrtb kgrgtg', '2026-08-27 17:07:22', '2026-08-27 17:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('instructor','learner','admin') NOT NULL,
  `city` varchar(50) DEFAULT NULL,
  `bio` varchar(500) DEFAULT NULL,
  `avatar_path` varchar(255) DEFAULT NULL,
  `is_suspended` tinyint(1) DEFAULT 0,
  `suspended_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `role`, `city`, `bio`, `avatar_path`, `is_suspended`, `suspended_reason`, `created_at`, `updated_at`) VALUES
(1, 'Sarah Martinez', 'sarah.martinez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Toronto', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(2, 'Michael Chen', 'michael.chen@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Vancouver', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(3, 'Emily Johnson', 'emily.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Montreal', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(4, 'David Kim', 'david.kim@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Calgary', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(5, 'Jessica Brown', 'jessica.brown@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'instructor', 'Toronto', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(6, 'Alex Thompson', 'alex.thompson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(7, 'Rachel Lee', 'rachel.lee@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Vancouver', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(8, 'James Wilson', 'james.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Montreal', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(9, 'Maria Garcia', 'maria.garcia@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Calgary', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(10, 'Tom Anderson', 'tom.anderson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(11, 'Lisa Wang', 'lisa.wang@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Vancouver', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(12, 'Robert Miller', 'robert.miller@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Montreal', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(13, 'Sophie Turner', 'sophie.turner@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(14, 'Kevin Patel', 'kevin.patel@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Calgary', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(15, 'Nina Rodriguez', 'nina.rodriguez@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Vancouver', NULL, NULL, 0, NULL, '2026-08-27 16:55:40', '2026-08-27 16:55:40'),
(16, 'John Suspended', 'suspended@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'learner', 'Toronto', NULL, NULL, 0, NULL, '2026-08-27 16:55:41', '2026-08-27 17:21:47'),
(17, 'Test Learner', 'learner@test.com', '$2y$10$rH9mPF5qWkjFQr1CMzGAbu1L7WGbA2G0o3NE3qYiEyeWjYChQJsvu', 'learner', 'Toronto', NULL, NULL, 0, NULL, '2026-08-27 16:56:05', '2026-08-27 16:56:05'),
(18, 'Test Instructor', 'instructor@test.com', '$2y$10$RNWGMHheflfxUTfL5vIXQ.U/.hvZBkwUGOjUu.pxLvqhXDL59bfCm', 'instructor', 'Vancouver', NULL, NULL, 0, NULL, '2026-08-27 16:56:05', '2026-08-27 16:56:05'),
(19, 'Test Admin', 'admin@test.com', '$2y$10$L3.vNTgl1R4fzoUrFQR3o.J.AA7yQzOIkBX3ZIwDMl.1WZRK7JJ5e', 'admin', 'Montreal', NULL, NULL, 0, NULL, '2026-08-27 16:56:05', '2026-08-27 16:56:05'),
(26, 'Roshan Timalsina', 'timalsinaroshan345@gmail.com', '$2y$10$4xQu6aIetRH74dwxY0hdBuStes/ty5wxD0569oeYCSiA0IroQOegW', 'learner', 'banepa-5', NULL, NULL, 0, NULL, '2026-08-27 17:01:32', '2026-08-27 17:01:32'),
(27, 'Roshan Timalsina', 'instructor@skillshare.local', '$2y$10$mgV8F6jUPyBuSkircTazW.pY0VIepPq0KMP4x.yQFH9KBaIJaLgEe', 'instructor', 'banepa-5', NULL, NULL, 0, NULL, '2026-08-27 17:04:46', '2026-08-27 17:04:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE `user_settings` (
  `user_id` int(11) NOT NULL,
  `notify_email` tinyint(1) NOT NULL DEFAULT 1,
  `notify_inapp` tinyint(1) NOT NULL DEFAULT 1,
  `notify_push` tinyint(1) NOT NULL DEFAULT 0,
  `notify_events` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT json_object('new_message',1,'booking_update',1,'session_update',1) CHECK (json_valid(`notify_events`)),
  `theme` enum('light','dark','system') NOT NULL DEFAULT 'light',
  `font_size` tinyint(3) UNSIGNED NOT NULL DEFAULT 16 CHECK (`font_size` between 12 and 24),
  `line_height` decimal(3,2) NOT NULL DEFAULT 1.50,
  `contrast_mode` enum('normal','high') NOT NULL DEFAULT 'normal',
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `timezone` varchar(64) NOT NULL DEFAULT 'Asia/Nicosia',
  `currency` varchar(3) NOT NULL DEFAULT 'GBP' COMMENT 'ISO 4217 currency code',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_settings`
--

INSERT INTO `user_settings` (`user_id`, `notify_email`, `notify_inapp`, `notify_push`, `notify_events`, `theme`, `font_size`, `line_height`, `contrast_mode`, `language`, `timezone`, `currency`, `created_at`, `updated_at`) VALUES
(26, 1, 1, 0, '{\"new_message\": 1, \"booking_update\": 1, \"session_update\": 1}', 'light', 16, 1.50, 'normal', 'en', 'Asia/Nicosia', 'GBP', '2026-08-27 17:02:53', '2026-08-27 17:02:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_actions`
--
ALTER TABLE `admin_actions`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `idx_admin` (`admin_id`),
  ADD KEY `idx_target` (`target_user_id`),
  ADD KEY `idx_action_type` (`action_type`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `unique_setting` (`setting_key`,`user_id`) COMMENT 'Ensure one value per user per setting',
  ADD KEY `idx_setting_key` (`setting_key`),
  ADD KEY `idx_setting_group` (`setting_group`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_public` (`is_public`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `unique_active_booking` (`session_id`,`learner_id`,`status`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_learner` (`learner_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `impact_factors`
--
ALTER TABLE `impact_factors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `skill_category` (`skill_category`),
  ADD KEY `idx_skill_category` (`skill_category`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `unique_rating_per_session` (`session_id`,`learner_id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_learner` (`learner_id`);

--
-- Indexes for table `skill_sessions`
--
ALTER TABLE `skill_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_instructor` (`instructor_id`),
  ADD KEY `idx_category` (`category_id`),
  ADD KEY `idx_event_datetime` (`event_datetime`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_city` (`city`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_city` (`city`);

--
-- Indexes for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_theme` (`theme`),
  ADD KEY `idx_language` (`language`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_actions`
--
ALTER TABLE `admin_actions`
  MODIFY `action_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `impact_factors`
--
ALTER TABLE `impact_factors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `skill_sessions`
--
ALTER TABLE `skill_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_actions`
--
ALTER TABLE `admin_actions`
  ADD CONSTRAINT `admin_actions_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_actions_ibfk_2` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD CONSTRAINT `app_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `app_settings_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `skill_sessions` (`session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`learner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `impact_factors`
--
ALTER TABLE `impact_factors`
  ADD CONSTRAINT `impact_factors_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `skill_sessions` (`session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`learner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `skill_sessions`
--
ALTER TABLE `skill_sessions`
  ADD CONSTRAINT `skill_sessions_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `skill_sessions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
--
-- Database: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

