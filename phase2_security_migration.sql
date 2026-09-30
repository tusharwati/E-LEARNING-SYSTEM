ALTER TABLE users
    ADD COLUMN role ENUM('student', 'admin') NOT NULL DEFAULT 'student' AFTER password;

UPDATE users
SET role = 'student'
WHERE role IS NULL OR role = '';
