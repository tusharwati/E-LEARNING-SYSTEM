<?php

function get_weak_topics($conn, $user_id, $limit = 5)
{
    $scores = [];
    $quiz_stmt = $conn->prepare(
        "SELECT COALESCE(NULLIF(q.topic, ''), 'Core concepts') AS topic, COUNT(*) AS misses
         FROM quiz_attempt_answers a
         JOIN quiz_attempts at ON at.id = a.attempt_id
         JOIN quiz_questions q ON q.id = a.question_id
         WHERE at.user_id = ? AND a.is_correct = 0
         GROUP BY topic"
    );
    $quiz_stmt->bind_param("i", $user_id);
    $quiz_stmt->execute();
    $quiz_result = $quiz_stmt->get_result();
    while ($row = $quiz_result->fetch_assoc()) {
        $scores[$row['topic']] = ($scores[$row['topic']] ?? 0) + (int) $row['misses'];
    }

    $practice_stmt = $conn->prepare(
        "SELECT t.name AS topic, COUNT(*) AS misses
         FROM practice_submissions s
         JOIN practice_problems p ON p.id = s.problem_id
         JOIN practice_topics t ON t.id = p.topic_id
         WHERE s.user_id = ? AND s.is_correct = 0
         GROUP BY t.id, t.name"
    );
    $practice_stmt->bind_param("i", $user_id);
    $practice_stmt->execute();
    $practice_result = $practice_stmt->get_result();
    while ($row = $practice_result->fetch_assoc()) {
        $scores[$row['topic']] = ($scores[$row['topic']] ?? 0) + (int) $row['misses'];
    }

    arsort($scores);
    $topics = [];
    foreach (array_slice($scores, 0, $limit, true) as $topic => $misses) {
        $topics[] = ['name' => $topic, 'misses' => $misses];
    }
    return $topics;
}

function get_recommended_problems($conn, $user_id, $topics, $limit = 4)
{
    if (!$topics) {
        return [];
    }

    $topic_names = array_column($topics, 'name');
    $placeholders = implode(',', array_fill(0, count($topic_names), '?'));
    $types = str_repeat('s', count($topic_names)) . 'i';
    $sql = "SELECT p.id, p.title, p.difficulty, t.name AS topic
            FROM practice_problems p
            JOIN practice_topics t ON t.id = p.topic_id
            WHERE t.name IN ($placeholders)
              AND NOT EXISTS (
                  SELECT 1 FROM practice_submissions s
                  WHERE s.problem_id = p.id AND s.user_id = ? AND s.is_correct = 1
              )
            ORDER BY p.id
            LIMIT 4";
    $params = array_merge($topic_names, [$user_id]);
    $stmt = $conn->prepare($sql);
    $bind_values = [$types];
    foreach ($params as $key => $value) {
        $bind_values[] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_values);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
