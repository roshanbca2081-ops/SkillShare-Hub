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
