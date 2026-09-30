-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 01:40 PM
-- Server version: 8.0.35
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_learning`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` varchar(255) DEFAULT 'default.jpg',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE = utf8mb4_unicode_ci; --utf8mb4_0900_ai_ci;

CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(1, 'Web development', 'web-development'),
(2, 'Programming', 'programming'),
(3, 'Interview preparation', 'interview-preparation');

CREATE TABLE `modules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `position` int NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `modules_course_idx` (`course_id`),
  CONSTRAINT `modules_course_fk` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `course_categories` (
  `course_id` int NOT NULL,
  `category_id` int NOT NULL,
  PRIMARY KEY (`course_id`,`category_id`),
  CONSTRAINT `course_categories_course_fk` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_categories_category_fk` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `thumbnail`) VALUES
(1, 'Introduction to HTML', 'Learn the basics of HTML.', 'html.jpg\r\n'),
(2, 'C language', 'Learn C Language', 'c.jpg'),
(3, 'Pthon Basics', 'Start coding in python', 'python.jpg'),
(4, 'Core Java', 'Java Tutorial for Beginners', 'java.jpg');

INSERT INTO `course_categories` (`course_id`, `category_id`) VALUES
(1, 1), (2, 2), (3, 2), (4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `module_id` int DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `position` int NOT NULL DEFAULT 1,
  `content` text NOT NULL,
  `video_url` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lessons_course_idx` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `title`, `content`, `video_url`, `file_path`) VALUES
(1, 1, 'Introduction to HTML', 'Learn the basics of HTML.', 'https://www.youtube.com/embed/qz0aGYrrlhU', 'notes\\html\\file1.pdf'),
(2, 1, 'HTML Elements', 'Understand different HTML elements.', 'https://www.youtube.com/embed/vIoO52MdZFE?list=PLP9IO4UYNF0VdAajP_5pYG-jG2JRrG72s', 'notes\\html\\file2.pdf'),
(3, 1, 'HTML Attributes', 'Learn HTML attributes', 'https://www.youtube.com/embed/yMX901oVtn8?list=PLP9IO4UYNF0VdAajP_5pYG-jG2JRrG72s', 'notes\\html\\file3.pdf'),
(4, 1, 'HTML Headings', 'Learn HTML', 'https://www.youtube.com/embed/yMX901oVtn8?list=PLP9IO4UYNF0VdAajP_5pYG-jG2JRrG72s\r\n', 'notes\\html\\file4.pdf'),
(5, 1, 'HTML Classes and div', 'Learn HTML Classes and div', 'https://www.youtube.com/embed/tWIkDOJo0Ts?list=PLP9IO4UYNF0VdAajP_5pYG-jG2JRrG72s', 'notes\\html\\file5.pdf'),
(6, 1, 'HTML Lists', 'Learn HTML Lists', 'https://www.youtube.com/embed/-QuK8taGLCs?list=PLP9IO4UYNF0VdAajP_5pYG-jG2JRrG72s', 'notes\\html\\file6.pdf'),
(7, 1, 'HTML Tables', 'Learn HTML Tables', 'https://www.youtube.com/embed/e62D-aayveY?list=PLP9IO4UYNF0VdAajP_5pYG-jG2JRrG72s', 'notes\\html\\file7.pdf'),
(8, 1, 'HTML Iframe', 'Learn HTML Iframe', 'https://www.youtube.com/embed/qP23O70ve7k?list=PLP9IO4UYNF0VdAajP_5pYG-jG2JRrG72s', 'notes\\html\\file8.pdf'),
(9, 2, 'C Basics', 'Learn C Language', 'https://www.youtube.com/embed/rQoqCP7LX60', 'notes/c/all_in_one.pdf'),
(10, 3, 'Python in oneshot', 'Learn Python', 'https://www.youtube.com/embed/ERCMXc8x7mc', 'notes\\python\\Python_Complete_Notes.pdf'),
(11, 4, 'Core java', 'Java Tutorial for Beginners', 'https://www.youtube.com/embed/UmnCZ7-9yDY', 'notes\\java\\Javanotes.pdf');

INSERT INTO `modules` (`id`, `course_id`, `title`, `position`) VALUES
(1, 1, 'Course lessons', 1),
(2, 2, 'Course lessons', 1),
(3, 3, 'Course lessons', 1),
(4, 4, 'Course lessons', 1);

UPDATE `lessons` SET `module_id` = `course_id`, `position` = `id`;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `question` text NOT NULL,
  `answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `quiz_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `legacy_quiz_id` int DEFAULT NULL,
  `question` text NOT NULL,
  `explanation` text DEFAULT NULL,
  `topic` varchar(100) DEFAULT NULL,
  `position` int NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quiz_questions_legacy_unique` (`legacy_quiz_id`),
  KEY `quiz_questions_course_idx` (`course_id`),
  CONSTRAINT `quiz_questions_course_fk` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `quiz_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_id` int NOT NULL,
  `option_text` varchar(500) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `position` int NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `quiz_options_question_idx` (`question_id`),
  CONSTRAINT `quiz_options_question_fk` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `quiz_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `score` int NOT NULL DEFAULT 0,
  `total_questions` int NOT NULL DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `quiz_attempts_user_idx` (`user_id`,`submitted_at`),
  CONSTRAINT `quiz_attempts_course_fk` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `quiz_attempt_answers` (
  `attempt_id` int NOT NULL,
  `question_id` int NOT NULL,
  `selected_option_id` int DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`attempt_id`,`question_id`),
  CONSTRAINT `quiz_attempt_answers_attempt_fk` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_attempt_answers_question_fk` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_attempt_answers_option_fk` FOREIGN KEY (`selected_option_id`) REFERENCES `quiz_options` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `practice_topics` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `practice_topics_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `practice_problems` (
  `id` int NOT NULL AUTO_INCREMENT,
  `topic_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `difficulty` enum('Easy','Medium','Hard') NOT NULL DEFAULT 'Easy',
  `statement` text NOT NULL,
  `examples` text DEFAULT NULL,
  `constraints_text` text DEFAULT NULL,
  `hint` text DEFAULT NULL,
  `editorial` text DEFAULT NULL,
  `expected_answer` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `practice_problems_topic_idx` (`topic_id`),
  CONSTRAINT `practice_problems_topic_fk` FOREIGN KEY (`topic_id`) REFERENCES `practice_topics` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `practice_tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `practice_tags_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `practice_problem_tags` (
  `problem_id` int NOT NULL,
  `tag_id` int NOT NULL,
  PRIMARY KEY (`problem_id`,`tag_id`),
  CONSTRAINT `practice_problem_tags_problem_fk` FOREIGN KEY (`problem_id`) REFERENCES `practice_problems` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practice_problem_tags_tag_fk` FOREIGN KEY (`tag_id`) REFERENCES `practice_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `practice_submissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `problem_id` int NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `practice_submissions_user_idx` (`user_id`,`problem_id`,`submitted_at`),
  CONSTRAINT `practice_submissions_problem_fk` FOREIGN KEY (`problem_id`) REFERENCES `practice_problems` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `practice_topics` (`id`, `name`, `slug`) VALUES
(1, 'Arrays', 'arrays'),
(2, 'Strings', 'strings'),
(3, 'SQL', 'sql'),
(4, 'Interview fundamentals', 'interview-fundamentals');

INSERT INTO `practice_problems`
  (`id`, `topic_id`, `title`, `difficulty`, `statement`, `examples`, `constraints_text`, `hint`, `editorial`, `expected_answer`)
VALUES
(1, 1, 'Find the largest value', 'Easy',
 'Given the numbers 4, 9, 2, and 7, submit the largest value.',
 'Example: 4, 9, 2, 7 -> 9',
 'Use a single pass while tracking the current maximum.',
 'Keep the largest value seen so far.',
 'Compare each value with the current maximum and keep the larger value.',
 '9'),
(2, 3, 'Count SQL rows', 'Easy',
 'Which SQL aggregate function returns the number of rows in a result set?',
 'Example answer: COUNT',
 'Think about aggregate functions used with SELECT.',
 'Use COUNT(*) when you need the total number of rows.',
 'COUNT returns the number of rows or non-null values depending on the expression.',
 'COUNT');

INSERT INTO `practice_tags` (`id`, `name`, `slug`) VALUES
(1, 'Arrays', 'arrays'),
(2, 'Strings', 'strings'),
(3, 'SQL', 'sql'),
(4, 'Beginner', 'beginner');

INSERT INTO `practice_problem_tags` (`problem_id`, `tag_id`) VALUES
(1, 1), (1, 4), (2, 3), (2, 4);

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `question`, `answer`) VALUES
(1, 1, 'What does HTML stand for?', 'HyperText Markup Language'),
(2, 1, 'Which HTML tag is used to define an unordered list?', '<ul>'),
(3, 1, 'What is the correct HTML tag for inserting a line break?', '<br>'),
(4, 1, 'Which attribute is used to specify that an input field must be filled out?', 'required'),
(5, 1, 'What is the default file extension for an HTML document?', '.html'),
(6, 1, 'Which HTML tag is used to create a hyperlink?', '<a>'),
(7, 1, 'Which HTML element is used to define important text?', '<strong>'),
(8, 1, 'Which tag is used to define a table in HTML?', '<table>'),
(9, 1, 'Which HTML attribute specifies an alternate text for an image, if the image cannot be displayed?', 'alt'),
(10, 1, 'Which HTML tag is used to create a form?', '<form>'),
(11, 2, 'What is the correct syntax to output \"Hello World\" in C?', 'printf(\"Hello World\");'),
(12, 2, 'Which data type is used to store a single character?', 'char'),
(13, 2, 'What is the size of int in C?', '4'),
(14, 2, 'What symbol is used to include header files?', '#'),
(15, 2, 'Which keyword is used to define a function in C?', 'void'),
(16, 2, 'Which loop is guaranteed to execute at least once?', 'do-while'),
(17, 2, 'What is the file extension of a C program?', '.c'),
(18, 2, 'Which operator is used for addition in C?', '+'),
(19, 2, 'What is the output of 5 % 2 in C?', '1'),
(20, 2, 'Which keyword is used to return a value from a function?', 'return'),
(21, 3, 'How do you output text in Python?', 'print()'),
(22, 3, 'Which keyword is used to define a function in Python?', 'def'),
(23, 3, 'What is the correct extension of Python files?', '.py'),
(24, 3, 'Which data type is used to store True or False?', 'bool'),
(25, 3, 'Which loop in Python can iterate over a list?', 'for'),
(26, 3, 'What is the output of len(\"Hello\")?', '5'),
(27, 3, 'Which symbol is used for comments in Python?', '#'),
(28, 3, 'How do you insert an element in a list?', 'append()'),
(29, 3, 'What does the input() function do?', 'Takes input from user'),
(30, 3, 'What keyword is used to import modules?', 'import'),
(31, 4, 'What is the main method signature in Java?', 'public static void main(String[] args)'),
(32, 4, 'Which keyword is used to create a class in Java?', 'class'),
(33, 4, 'What is the extension of Java files?', '.java'),
(34, 4, 'What is used to print text in Java?', 'System.out.println()'),
(35, 4, 'Which keyword is used for inheritance?', 'extends'),
(36, 4, 'Which data type is used to store decimal numbers?', 'float'),
(37, 4, 'What is the output of 10 % 3 in Java?', '1'),
(38, 4, 'Which loop repeats a block while a condition is true?', 'while'),
(39, 4, 'What keyword is used to handle exceptions?', 'try'),
(40, 4, 'Which keyword creates an object in Java?', 'new');

INSERT INTO `quiz_questions` (`id`, `course_id`, `legacy_quiz_id`, `question`, `explanation`, `topic`, `position`)
SELECT `id`, `course_id`, `id`, `question`, CONCAT('Review the course material for: ', `question`), 'Core concepts', `id`
FROM `quizzes`;

INSERT INTO `quiz_options` (`question_id`, `option_text`, `is_correct`, `position`)
SELECT `id`, `answer`, 1, 1 FROM `quizzes`;

INSERT INTO `quiz_options` (`question_id`, `option_text`, `is_correct`, `position`)
SELECT `id`, CONCAT('Not ', LEFT(answer, 120)), 0, 2 FROM `quizzes`;

INSERT INTO `quiz_options` (`question_id`, `option_text`, `is_correct`, `position`)
SELECT `id`, 'None of the above', 0, 3 FROM `quizzes`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('student','admin') NOT NULL DEFAULT 'student',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `course_enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `course_id` int NOT NULL,
  `enrolled_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_enrollment_unique` (`user_id`,`course_id`),
  CONSTRAINT `enrollments_course_fk` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lesson_progress` (
  `user_id` int NOT NULL,
  `lesson_id` int NOT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`lesson_id`),
  CONSTRAINT `lesson_progress_lesson_fk` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`) VALUES
(9, 'John Doe', 'john@gmail.com', 'John@123'),
(10, 'Alice Smith', 'alice@example.com', 'password123'),
(11, 'rahul', 'rahul@gmail.com', 'Rahul@123'),
(12, 'Aman', 'Aman@gmail.com', 'Aman@123'),
(14, 'sundar Madavi', 'sundar@gmail.com', 'Sundar@123'),
(16, 'Tushar Watti', 'tushar@gmail.com', 'Tushar@123');

CREATE TABLE `daily_challenges` (
  `id` int NOT NULL AUTO_INCREMENT,
  `challenge_date` date NOT NULL,
  `question` text NOT NULL,
  `explanation` text NOT NULL,
  `xp_reward` int NOT NULL DEFAULT 20,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_challenges_date_unique` (`challenge_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `daily_challenge_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `challenge_id` int NOT NULL,
  `option_text` varchar(500) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `position` int NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `daily_challenge_options_challenge_idx` (`challenge_id`),
  CONSTRAINT `daily_challenge_options_challenge_fk` FOREIGN KEY (`challenge_id`) REFERENCES `daily_challenges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `daily_challenge_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `challenge_id` int NOT NULL,
  `selected_option_id` int DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `xp_awarded` int NOT NULL DEFAULT 0,
  `completed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_challenge_attempt_unique` (`user_id`,`challenge_id`),
  KEY `daily_challenge_attempts_user_idx` (`user_id`,`completed_at`),
  CONSTRAINT `daily_challenge_attempts_challenge_fk` FOREIGN KEY (`challenge_id`) REFERENCES `daily_challenges` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_challenge_attempts_option_fk` FOREIGN KEY (`selected_option_id`) REFERENCES `daily_challenge_options` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `xp_transactions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `source_type` varchar(50) NOT NULL,
  `source_id` int NOT NULL,
  `amount` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `xp_transaction_source_unique` (`user_id`,`source_type`,`source_id`),
  KEY `xp_transactions_user_idx` (`user_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_learning_stats` (
  `user_id` int NOT NULL,
  `total_xp` int NOT NULL DEFAULT 0,
  `level` int NOT NULL DEFAULT 1,
  `current_streak` int NOT NULL DEFAULT 0,
  `longest_streak` int NOT NULL DEFAULT 0,
  `last_challenge_date` date DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `daily_challenges` (`challenge_date`, `question`, `explanation`, `xp_reward`)
VALUES (CURRENT_DATE, 'Which data structure follows the first-in, first-out (FIFO) principle?', 'A queue removes items in the same order in which they were added, which is FIFO.', 20);

SET @daily_challenge_id = (SELECT id FROM daily_challenges WHERE challenge_date = CURRENT_DATE);
INSERT INTO `daily_challenge_options` (`challenge_id`, `option_text`, `is_correct`, `position`) VALUES
(@daily_challenge_id, 'Queue', 1, 1),
(@daily_challenge_id, 'Stack', 0, 2),
(@daily_challenge_id, 'Tree', 0, 3);

ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `enrollments_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `lesson_progress`
  ADD CONSTRAINT `lesson_progress_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `practice_submissions`
  ADD CONSTRAINT `practice_submissions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `daily_challenge_attempts`
  ADD CONSTRAINT `daily_challenge_attempts_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `xp_transactions`
  ADD CONSTRAINT `xp_transactions_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_learning_stats`
  ADD CONSTRAINT `user_learning_stats_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT,
  ADD KEY `lessons_module_idx` (`module_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_module_fk` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
