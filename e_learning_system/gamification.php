<?php

function learning_level_from_xp($xp)
{
    return max(1, (int) floor((int) $xp / 100) + 1);
}

function ensure_learning_stats($conn, $user_id)
{
    $stmt = $conn->prepare(
        "INSERT IGNORE INTO user_learning_stats (user_id, level) VALUES (?, 1)"
    );
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

function ensure_daily_challenge($conn)
{
    $challenge_stmt = $conn->prepare(
        "INSERT IGNORE INTO daily_challenges
         (challenge_date, question, explanation, xp_reward)
         VALUES (CURRENT_DATE, ?, ?, 20)"
    );
    $question = 'Which data structure follows the first-in, first-out (FIFO) principle?';
    $explanation = 'A queue removes items in the same order in which they were added, which is FIFO.';
    $challenge_stmt->bind_param("ss", $question, $explanation);
    $challenge_stmt->execute();

    $id_stmt = $conn->prepare(
        "SELECT id FROM daily_challenges WHERE challenge_date = CURRENT_DATE"
    );
    $id_stmt->execute();
    $challenge_id = (int) $id_stmt->get_result()->fetch_assoc()['id'];
    $count_stmt = $conn->prepare(
        "SELECT COUNT(*) AS total FROM daily_challenge_options WHERE challenge_id = ?"
    );
    $count_stmt->bind_param("i", $challenge_id);
    $count_stmt->execute();
    $option_count = (int) $count_stmt->get_result()->fetch_assoc()['total'];
    if ($option_count > 0) {
        return;
    }
    $option_stmt = $conn->prepare(
        "INSERT INTO daily_challenge_options (challenge_id, option_text, is_correct, position)
         VALUES (?, ?, ?, ?)"
    );
    $options = [
        ['Queue', 1, 1],
        ['Stack', 0, 2],
        ['Tree', 0, 3],
    ];
    foreach ($options as $option) {
        $option_text = $option[0];
        $is_correct = $option[1];
        $position = $option[2];
        $option_stmt->bind_param(
            "isiii",
            $challenge_id,
            $option_text,
            $is_correct,
            $position
        );
        $option_stmt->execute();
    }
}

function award_xp($conn, $user_id, $source_type, $source_id, $amount, $challenge_date = null, $manage_transaction = true)
{
    if ($amount < 1) {
        return false;
    }

    if ($manage_transaction) {
        $conn->begin_transaction();
    }
    try {
        $transaction_stmt = $conn->prepare(
            "INSERT IGNORE INTO xp_transactions (user_id, source_type, source_id, amount)
             VALUES (?, ?, ?, ?)"
        );
        $transaction_stmt->bind_param("isii", $user_id, $source_type, $source_id, $amount);
        $transaction_stmt->execute();
        if ($transaction_stmt->affected_rows !== 1) {
            if ($manage_transaction) {
                $conn->rollback();
            }
            return false;
        }

        ensure_learning_stats($conn, $user_id);
        $stats_stmt = $conn->prepare(
            "SELECT total_xp, current_streak, longest_streak, last_challenge_date
             FROM user_learning_stats WHERE user_id = ? FOR UPDATE"
        );
        $stats_stmt->bind_param("i", $user_id);
        $stats_stmt->execute();
        $stats = $stats_stmt->get_result()->fetch_assoc();
        $new_streak = (int) $stats['current_streak'];
        $longest_streak = (int) $stats['longest_streak'];
        $last_date = $stats['last_challenge_date'];

        if ($challenge_date !== null) {
            if ($last_date === null) {
                $new_streak = 1;
            } elseif ($last_date === date('Y-m-d', strtotime($challenge_date . ' -1 day'))) {
                $new_streak++;
            } elseif ($last_date !== $challenge_date) {
                $new_streak = 1;
            }
            $longest_streak = max($longest_streak, $new_streak);
        }

        $total_xp = (int) $stats['total_xp'] + $amount;
        $level = learning_level_from_xp($total_xp);
        $update_stmt = $conn->prepare(
            "UPDATE user_learning_stats
             SET total_xp = ?, level = ?, current_streak = ?, longest_streak = ?,
                 last_challenge_date = COALESCE(?, last_challenge_date)
             WHERE user_id = ?"
        );
        $update_stmt->bind_param(
            "iiiisi",
            $total_xp,
            $level,
            $new_streak,
            $longest_streak,
            $challenge_date,
            $user_id
        );
        $update_stmt->execute();
        if ($manage_transaction) {
            $conn->commit();
        }
        return true;
    } catch (Throwable $error) {
        if ($manage_transaction) {
            $conn->rollback();
        }
        throw $error;
    }
}
