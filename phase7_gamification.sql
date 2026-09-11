CREATE TABLE IF NOT EXISTS daily_challenges (
    id INT NOT NULL AUTO_INCREMENT,
    challenge_date DATE NOT NULL,
    question TEXT NOT NULL,
    explanation TEXT NOT NULL,
    xp_reward INT NOT NULL DEFAULT 20,
    PRIMARY KEY (id),
    UNIQUE KEY daily_challenges_date_unique (challenge_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS daily_challenge_options (
    id INT NOT NULL AUTO_INCREMENT,
    challenge_id INT NOT NULL,
    option_text VARCHAR(500) NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    position INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    KEY daily_challenge_options_challenge_idx (challenge_id),
    CONSTRAINT daily_challenge_options_challenge_fk
        FOREIGN KEY (challenge_id) REFERENCES daily_challenges(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS daily_challenge_attempts (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    challenge_id INT NOT NULL,
    selected_option_id INT DEFAULT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    xp_awarded INT NOT NULL DEFAULT 0,
    completed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY daily_challenge_attempt_unique (user_id, challenge_id),
    KEY daily_challenge_attempts_user_idx (user_id, completed_at),
    CONSTRAINT daily_challenge_attempts_user_fk
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT daily_challenge_attempts_challenge_fk
        FOREIGN KEY (challenge_id) REFERENCES daily_challenges(id) ON DELETE CASCADE,
    CONSTRAINT daily_challenge_attempts_option_fk
        FOREIGN KEY (selected_option_id) REFERENCES daily_challenge_options(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS xp_transactions (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    source_type VARCHAR(50) NOT NULL,
    source_id INT NOT NULL,
    amount INT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY xp_transaction_source_unique (user_id, source_type, source_id),
    KEY xp_transactions_user_idx (user_id, created_at),
    CONSTRAINT xp_transactions_user_fk
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_learning_stats (
    user_id INT NOT NULL,
    total_xp INT NOT NULL DEFAULT 0,
    level INT NOT NULL DEFAULT 1,
    current_streak INT NOT NULL DEFAULT 0,
    longest_streak INT NOT NULL DEFAULT 0,
    last_challenge_date DATE DEFAULT NULL,
    PRIMARY KEY (user_id),
    CONSTRAINT user_learning_stats_user_fk
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO daily_challenges (challenge_date, question, explanation, xp_reward)
VALUES (CURRENT_DATE, 'Which data structure follows the first-in, first-out (FIFO) principle?',
        'A queue removes items in the same order in which they were added, which is FIFO.',
        20)
ON DUPLICATE KEY UPDATE question = VALUES(question);

SET @daily_challenge_id = (
    SELECT id FROM daily_challenges WHERE challenge_date = CURRENT_DATE
);

INSERT INTO daily_challenge_options (challenge_id, option_text, is_correct, position)
SELECT @daily_challenge_id, 'Queue', 1, 1
WHERE NOT EXISTS (
    SELECT 1 FROM daily_challenge_options WHERE challenge_id = @daily_challenge_id
)
UNION ALL
SELECT @daily_challenge_id, 'Stack', 0, 2
WHERE NOT EXISTS (
    SELECT 1 FROM daily_challenge_options WHERE challenge_id = @daily_challenge_id
)
UNION ALL
SELECT @daily_challenge_id, 'Tree', 0, 3
WHERE NOT EXISTS (
    SELECT 1 FROM daily_challenge_options WHERE challenge_id = @daily_challenge_id
);
