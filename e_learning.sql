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
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `thumbnail` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `thumbnail`) VALUES
(1, 'Introduction to HTML', 'Learn the basics of HTML.', 'html.jpg\r\n'),
(2, 'C language', 'Learn C Language', 'c.jpg'),
(3, 'Pthon Basics', 'Start coding in python', 'python.jpg'),
(4, 'Core Java', 'Java Tutorial for Beginners', 'java.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `video_url` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int NOT NULL,
  `course_id` int NOT NULL,
  `question` text NOT NULL,
  `answer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
