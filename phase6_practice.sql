CREATE TABLE IF NOT EXISTS practice_topics (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY practice_topics_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS practice_problems (
    id INT NOT NULL AUTO_INCREMENT,
    topic_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    difficulty ENUM('Easy', 'Medium', 'Hard') NOT NULL DEFAULT 'Easy',
    statement TEXT NOT NULL,
    examples TEXT NULL,
    constraints_text TEXT NULL,
    hint TEXT NULL,
    editorial TEXT NULL,
    expected_answer VARCHAR(500) NOT NULL,
    PRIMARY KEY (id),
    KEY practice_problems_topic_idx (topic_id),
    CONSTRAINT practice_problems_topic_fk FOREIGN KEY (topic_id) REFERENCES practice_topics(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS practice_tags (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY practice_tags_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS practice_problem_tags (
    problem_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (problem_id, tag_id),
    CONSTRAINT practice_problem_tags_problem_fk FOREIGN KEY (problem_id) REFERENCES practice_problems(id) ON DELETE CASCADE,
    CONSTRAINT practice_problem_tags_tag_fk FOREIGN KEY (tag_id) REFERENCES practice_tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS practice_submissions (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    problem_id INT NOT NULL,
    answer_text TEXT NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY practice_submissions_user_idx (user_id, problem_id, submitted_at),
    CONSTRAINT practice_submissions_user_fk FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT practice_submissions_problem_fk FOREIGN KEY (problem_id) REFERENCES practice_problems(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO practice_topics (name, slug) VALUES
    ('Arrays', 'arrays'),
    ('Strings', 'strings'),
    ('SQL', 'sql'),
    ('Interview fundamentals', 'interview-fundamentals');

INSERT IGNORE INTO practice_tags (name, slug) VALUES
    ('Arrays', 'arrays'),
    ('Strings', 'strings'),
    ('SQL', 'sql'),
    ('Beginner', 'beginner');

INSERT INTO practice_problems
    (topic_id, title, difficulty, statement, examples, constraints_text, hint, editorial, expected_answer)
SELECT t.id, 'Find the largest value', 'Easy',
    'Given the numbers 4, 9, 2, and 7, submit the largest value.',
    'Example: 4, 9, 2, 7 -> 9',
    'Use a single pass while tracking the current maximum.',
    'Keep the largest value seen so far.',
    'Compare each value with the current maximum and keep the larger value.',
    '9'
FROM practice_topics t
WHERE t.slug = 'arrays'
  AND NOT EXISTS (SELECT 1 FROM practice_problems WHERE title = 'Find the largest value');

INSERT INTO practice_problems
    (topic_id, title, difficulty, statement, examples, constraints_text, hint, editorial, expected_answer)
SELECT t.id, 'Count SQL rows', 'Easy',
    'Which SQL aggregate function returns the number of rows in a result set?',
    'Example answer: COUNT',
    'Think about aggregate functions used with SELECT.',
    'Use COUNT(*) when you need the total number of rows.',
    'COUNT returns the number of rows or non-null values depending on the expression.',
    'COUNT'
FROM practice_topics t
WHERE t.slug = 'sql'
  AND NOT EXISTS (SELECT 1 FROM practice_problems WHERE title = 'Count SQL rows');

INSERT IGNORE INTO practice_problem_tags (problem_id, tag_id)
SELECT p.id, t.id
FROM practice_problems p
JOIN practice_tags t ON t.slug IN ('arrays', 'beginner')
WHERE p.title = 'Find the largest value';

INSERT IGNORE INTO practice_problem_tags (problem_id, tag_id)
SELECT p.id, t.id
FROM practice_problems p
JOIN practice_tags t ON t.slug IN ('sql', 'beginner')
WHERE p.title = 'Count SQL rows';
