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


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2026 at 06:20 PM
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
-- Database: `skillshare_hub`
--

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
(2, 4, 'message', 'New Message', 'You have received a new message from Roshan Timalsina.', 'fresher/message/chat.php?user_id=2', 'fa-envelope', 0, NULL, NULL, '2026-09-05 14:39:41'),
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

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `update_session_status` ON SCHEDULE EVERY 1 MINUTE STARTS '2026-09-04 23:54:35' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE sessions 
    SET status = 'ongoing' 
    WHERE scheduled_at <= NOW() 
    AND scheduled_at > DATE_SUB(NOW(), INTERVAL duration MINUTE)
    AND status = 'scheduled';
    
    UPDATE sessions 
    SET status = 'completed' 
    WHERE scheduled_at < DATE_SUB(NOW(), INTERVAL duration MINUTE)
    AND status IN ('scheduled', 'ongoing');
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
