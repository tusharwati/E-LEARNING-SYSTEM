CREATE TABLE IF NOT EXISTS categories (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY categories_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS modules (
    id INT NOT NULL AUTO_INCREMENT,
    course_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    position INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY modules_course_idx (course_id),
    CONSTRAINT modules_course_fk FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_categories (
    course_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (course_id, category_id),
    CONSTRAINT course_categories_course_fk FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    CONSTRAINT course_categories_category_fk FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS course_enrollments (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY course_enrollment_unique (user_id, course_id),
    KEY enrollments_course_idx (course_id),
    CONSTRAINT enrollments_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT enrollments_course_fk FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS lesson_progress (
    user_id INT NOT NULL,
    lesson_id INT NOT NULL,
    completed_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (user_id, lesson_id),
    CONSTRAINT lesson_progress_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT lesson_progress_lesson_fk FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO categories (name, slug) VALUES
    ('Web development', 'web-development'),
    ('Programming', 'programming'),
    ('Interview preparation', 'interview-preparation');

INSERT IGNORE INTO modules (course_id, title, position)
SELECT c.id, 'Course lessons', 1
FROM courses c;

INSERT IGNORE INTO course_categories (course_id, category_id)
SELECT c.id, CASE WHEN c.id = 1 THEN 1 ELSE 2 END
FROM courses c;

ALTER TABLE lessons ADD COLUMN module_id INT NULL AFTER course_id;
ALTER TABLE lessons ADD COLUMN position INT NOT NULL DEFAULT 1 AFTER title;

UPDATE lessons l
JOIN modules m ON m.course_id = l.course_id
SET l.module_id = m.id
WHERE l.module_id IS NULL;

UPDATE lessons l
SET l.position = l.id
WHERE l.position = 1;

ALTER TABLE lessons ADD KEY lessons_module_idx (module_id);
ALTER TABLE lessons ADD CONSTRAINT lessons_module_fk
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE SET NULL;
