<?php

function displayHighScores($conn) {
    try {
        $stmt = $conn->prepare("SELECT username, ROUND(score, 2) AS rounded_score, num_tries, time_spent, show_session, played_date FROM high_scores ORDER BY rounded_score DESC LIMIT 10");
        $stmt->execute();
        $highScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Username</th>";
        echo "<th>Score</th>";
        echo "<th>Attempts</th>";
        echo "<th>Time Spent (seconds)</th>";
        echo "<th>Show Session Info</th>";
        echo "<th>Played Date</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        foreach ($highScores as $score) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($score['username']) . "</td>";
            echo "<td>" . htmlspecialchars($score['rounded_score']) . "</td>";
            echo "<td>" . htmlspecialchars($score['num_tries']) . "</td>";
            echo "<td>" . htmlspecialchars($score['time_spent']) . "</td>";
            echo "<td>" . ($score['show_session'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . htmlspecialchars($score['played_date']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
