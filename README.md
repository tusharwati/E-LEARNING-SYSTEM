E-Learning Platform (Free Courses Platform)
Project Overview

This E-Learning System is a web-based platform designed to provide completely free online courses for learners.
The goal of this project is to make quality education accessible to everyone without any paid subscriptions or hidden charges.

The system allows users to explore courses, watch learning content, download study materials, they can give quizes also and improve their skills in an easy and user-friendly environment.

🎯 Objectives
  Provide 100% free educational content
  Create a simple and attractive learning platform
  Help students learn at their own pace
  Encourage self-learning and skill development

Key Features

1. Free courses (no payment required)
2. Public course browsing with protected learning actions
3. User registration and secure login
4. Video-based lessons and downloadable notes / PDFs
5. Course enrollment and lesson progress
6. Normalized MCQ quizzes with attempt history
7. Practice problems with safe, non-executing answer checks
8. Daily challenges, XP, levels, streaks, and leaderboard
9. Rule-based personalized topic and practice recommendations

Responsive design for mobile and desktop
Secure authentication, password hashing, sessions, and CSRF protection

🛠️ Technologies Used
Frontend :
  1.HTML
  2.CSS
  3.JavaScript
  4.jQuery / AJAX

Backend :
  1.PHP
  2.Database
  3.MySQL

Tools:
  1.VS Code
  2.XAMPP / WAMP

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
<img width="917" height="581" alt="Screenshot (208)" src="https://github.com/user-attachments/assets/e53b4cf0-0ae4-4fee-a142-1176975f8c8c" />
<img width="1327" height="604" alt="Screenshot (209)" src="https://github.com/user-attachments/assets/dc8894a0-6eb3-4b5b-a942-cb033c58adc9" />
<img width="1344" height="599" alt="Screenshot (210)" src="https://github.com/user-attachments/assets/0dfd3d51-f094-45e5-821a-763aab9bb744" />
<img width="1318" height="597" alt="Screenshot (211)" src="https://github.com/user-attachments/assets/6553b853-5864-4cbe-9014-2b45c8cb1e0b" />


👥 User Roles
Student :
  1.Register & login
  2.Browse free courses
  3.Watch videos
  4.Download notes

Admin role groundwork is present in the schema. The admin management interface is planned for a later phase.
  
Future Enhancements

Admin dashboard for managing learning content
Certificate generation
Discussion forum

❤️ Why Free Courses?

Education should be accessible to everyone.
This project focuses on learning without financial barriers, helping students grow their knowledge and skills freely.

📜 License
This project is open-source and free to use for educational purposes.
