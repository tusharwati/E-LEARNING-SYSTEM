CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT NOT NULL AUTO_INCREMENT,
    course_id INT NOT NULL,
    legacy_quiz_id INT NULL,
    question TEXT NOT NULL,
    explanation TEXT NULL,
    topic VARCHAR(100) NULL,
    position INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY quiz_questions_legacy_unique (legacy_quiz_id),
    KEY quiz_questions_course_idx (course_id),
    CONSTRAINT quiz_questions_course_fk FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiz_options (
    id INT NOT NULL AUTO_INCREMENT,
    question_id INT NOT NULL,
    option_text VARCHAR(500) NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    position INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY quiz_options_question_idx (question_id),
    CONSTRAINT quiz_options_question_fk FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    score INT NOT NULL DEFAULT 0,
    total_questions INT NOT NULL DEFAULT 0,
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY quiz_attempts_user_idx (user_id, submitted_at),
    CONSTRAINT quiz_attempts_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT quiz_attempts_course_fk FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS quiz_attempt_answers (
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option_id INT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (attempt_id, question_id),
    CONSTRAINT quiz_attempt_answers_attempt_fk FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    CONSTRAINT quiz_attempt_answers_question_fk FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE,
    CONSTRAINT quiz_attempt_answers_option_fk FOREIGN KEY (selected_option_id) REFERENCES quiz_options(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO quiz_questions (course_id, legacy_quiz_id, question, explanation, topic, position)
SELECT course_id, id, question, CONCAT('Review the course material for: ', question), 'Core concepts', id
FROM quizzes;

INSERT INTO quiz_options (question_id, option_text, is_correct, position)
SELECT qq.id, q.answer, 1, 1
FROM quizzes q
JOIN quiz_questions qq ON qq.legacy_quiz_id = q.id
WHERE NOT EXISTS (
    SELECT 1 FROM quiz_options qo WHERE qo.question_id = qq.id
);

INSERT INTO quiz_options (question_id, option_text, is_correct, position)
SELECT qq.id, CONCAT('Not ', LEFT(q.answer, 120)), 0, 2
FROM quizzes q
JOIN quiz_questions qq ON qq.legacy_quiz_id = q.id
WHERE NOT EXISTS (
    SELECT 1 FROM quiz_options qo WHERE qo.question_id = qq.id AND qo.position = 2
);

INSERT INTO quiz_options (question_id, option_text, is_correct, position)
SELECT qq.id, 'None of the above', 0, 3
FROM quizzes q
JOIN quiz_questions qq ON qq.legacy_quiz_id = q.id
WHERE NOT EXISTS (
    SELECT 1 FROM quiz_options qo WHERE qo.question_id = qq.id AND qo.position = 3
);
