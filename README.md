E-Learning Platform (Free Courses Platform)
Project Overview
A full-stack, learner-centric E-Learning Platform to deliver accessible and structured digital education through a completely free learning model. The platform integrates course discovery, multimedia-based learning, assessment, progress tracking, gamification, and rule-based personalization into a unified learning ecosystem.

The system is designed to provide a seamless learning workflow—from public course discovery and authenticated enrollment to lesson consumption, assessment, progress monitoring, and continuous skill development.

Objectives
  Provide 100% free educational content
  Create a simple and attractive learning platform
  Help students learn at their own pace
  Encourage self-learning and skill development


Responsive design for mobile and desktop
Secure authentication, password hashing, sessions, and CSRF protection

Technology Stack

Frontend: HTML5, CSS3, JavaScript, jQuery, AJAX
Backend: PHP
Database: MySQL
Development Environment: XAMPP / WAMP
Version Control: Git & GitHub

How to Run the Project :
  Clone the repository
  git clone https://github.com/tusharwati/E-LEARNING-SYSTEM.git
  Move the project folder to the htdocs folder (XAMPP)
  Import e_learning.sql into MySQL as the e_learning database. The dump is compatible with the XAMPP MySQL version used by this project.
  The included configuration uses the default XAMPP root account without a password.
  If your MySQL root account has a password, update $password in e_learning_system/db_config.php
  Start Apache and MySQL from the XAMPP Control Panel
  Open the application folder in your browser:
  http://localhost/E-LEARNING-SYSTEM/e_learning_system/

Visitors can browse the homepage, course catalog, course previews, and preparation categories without an account. Signup or login is requested only when starting a lesson, attempting a quiz, or opening the dashboard/profile. After authentication, the visitor is returned to the page they originally requested.
OUTPUTS :
VERSION 1 :
<img width="917" height="581" alt="Screenshot (208)" src="https://github.com/user-attachments/assets/e53b4cf0-0ae4-4fee-a142-1176975f8c8c" />




VERSION 2 :
<img width="400" height="497" alt="e_learning_version_2" src="https://github.com/user-attachments/assets/49ceb15b-6120-4ba6-b509-6733b163b566" />


Future Enhancements

Admin dashboard for managing learning content
Certificate generation
Discussion forum

This project reflects my ongoing journey in building practical, learner-focused web applications. I’m excited to keep improving it!

License
This project is open-source and free to use for educational purposes.
