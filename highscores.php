<?php
include 'config.php';

echo "<h1>High Scores</h1>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High Scores</title>
    <link rel="stylesheet" href="css/styles.css"> 
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Score</th>
                <th>Attempts</th>
                <th>Time Spent (seconds)</th>
                <th>Show Session Info</th>
                <th>Played Date</th> <!-- Add a table header for the played date -->
            </tr>
        </thead>
        <tbody>
            <?php
           try {
            $stmt = $conn->prepare("SELECT username, ROUND(score, 2) AS rounded_score, num_tries, time_spent, show_session, played_date FROM high_scores ORDER BY rounded_score DESC LIMIT 10");
            $stmt->execute();
            $highScores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            foreach ($highScores as $score) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($score['username'] ?? '') . "</td>"; // Use the null coalescing operator to handle null values
                echo "<td>" . htmlspecialchars($score['rounded_score'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($score['num_tries'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($score['time_spent'] ?? '') . "</td>";
                echo "<td>" . ($score['show_session'] ? 'Yes' : 'No') . "</td>";
                echo "<td>" . htmlspecialchars($score['played_date'] ?? '') . "</td>"; // Ensure played_date is properly fetched
                echo "</tr>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
            ?>
        </tbody>
    </table>

    <a href="index.php" class="button">Play Again</a>
</body>
</html>