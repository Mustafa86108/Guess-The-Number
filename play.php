<?php
session_start();
include 'config.php'; 
include 'functions.php';

// Function to save the high score
function saveHighScore($conn) {
    $timeSpent = time() - $_SESSION['start_time'];
    $stmt = $conn->prepare("INSERT INTO high_scores (username, score, num_tries, time_spent) VALUES (:username, :score, :num_tries, :time_spent)");
    $stmt->bindParam(':username', $_SESSION['username']);
    $stmt->bindValue(':score', 1000 / ($_SESSION['num_tries'] + $timeSpent)); // Example scoring function
    $stmt->bindParam(':num_tries', $_SESSION['num_tries']);
    $stmt->bindParam(':time_spent', $timeSpent);
    $stmt->execute();
}

if (!isset($_SESSION['secret_number'])) {
    header('Location: index.php');
    exit;
}

$gameOver = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guess'])) {
    $guess = filter_input(INPUT_POST, 'guess', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['num_tries']++;
    
    if (!isset($_SESSION['previous_guesses'])) {
        $_SESSION['previous_guesses'] = [];
    }

    $_SESSION['previous_guesses'][] = $guess;

    if ($guess == $_SESSION['secret_number']) {
        $message = "Congratulations, {$_SESSION['username']}! You guessed the right number!";
        $gameOver = true;
        saveHighScore($conn);
    } elseif ($_SESSION['num_tries'] >= $_SESSION['max_tries']) {
        $message = "Sorry, you've used all your tries. The number was {$_SESSION['secret_number']}.";
        $gameOver = true;
    } elseif ((time() - $_SESSION['start_time']) > $_SESSION['max_time']) {
        $message = "Time's up! The number was {$_SESSION['secret_number']}.";
        $gameOver = true;
    } elseif ($guess < $_SESSION['secret_number']) {
        $message = "Your guess is too low.";
    } else {
        $message = "Your guess is too high.";
    }
    
    if ($gameOver) {
        session_destroy();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guess The Number</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        // JavaScript function to update the timer every second
        function updateTimer(seconds) {
            document.getElementById('timer').innerHTML = 'Time left: ' +  Math.max(0, seconds) + ' seconds';
            if (seconds > 0) {
                setTimeout(function() {
                    updateTimer(seconds - 1);
                }, 1000);
            } else {
                // If time is up, you can perform any additional actions here
                // For example, submit the form or end the game
                document.getElementById('guessForm').submit();
            }
        }

        // Call the JavaScript function to update the timer
        updateTimer(<?php echo max(0, $_SESSION['max_time'] - (time() - $_SESSION['start_time'])); ?>);
    </script>
</head>
<body>
    <h1>Guess The Number Game</h1>

    <?php if (isset($message) && strpos($message, 'Congratulations') !== false): ?>
        <p style="color: green; font-weight: bold;"><?php echo $message; ?></p>
    <?php elseif (isset($message) && !strpos($message, 'Congratulations')): ?>
        <p style="color: red; font-weight: bold;"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if (!$gameOver): ?>
        <form method="post" id="guessForm">
            <label for="guess">Enter your guess:</label>
            <input type="number" id="guess" name="guess" required>
            <button type="submit">Submit</button>
        </form>
        <p>Attempts: <span><?php echo $_SESSION['num_tries']; ?></span> | Time left: <span id="countdown"><?php echo $_SESSION['max_time'] - (time() - $_SESSION['start_time']); ?></span> seconds</p>

        <?php if (isset($_SESSION['previous_guesses'])): ?>
            <p>Previous guesses: <?php echo implode(", ", $_SESSION['previous_guesses']); ?></p>
        <?php endif; ?>

        <?php if ($_SESSION['show_session']): ?>
            <div style="margin-top: 20px; border: 1px solid #ccc; padding: 10px;">
                <h2>Session Information</h2>
                <p>Secret Number: <?php echo $_SESSION['secret_number']; ?></p>
                <p>Min Range: <?php echo $_SESSION['min_range']; ?></p>
                <p>Max Range: <?php echo $_SESSION['max_range']; ?></p>
                <p>Max Tries: <?php echo $_SESSION['max_tries']; ?></p>
                <p>Max Time: <?php echo $_SESSION['max_time']; ?> seconds</p>
            </div>
        <?php endif; ?>

        <!-- Add JavaScript for countdown timer -->
        <script>
            var countdownElement = document.getElementById('countdown');
            var remainingTime = <?php echo $_SESSION['max_time'] - (time() - $_SESSION['start_time']); ?>;

            function updateCountdown() {
                countdownElement.textContent = remainingTime;
                remainingTime--;

                if (remainingTime < 0) {
                    clearInterval(countdownInterval);
                    // Optional: Add logic to handle time-up scenario
                }
            }

        var countdownInterval = setInterval(updateCountdown, 1000);
    </script>

    <form method="post" action="index.php">
        <button type="submit">Reset</button>
    </form>

<?php else: ?>
    <a href="index.php">Play Again</a> | <a href="highscores.php">High Scores</a>

    <!-- Display High Scores -->
    <table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Score</th>
        </tr>
    </thead>
    <tbody>

    <?php
    try {
        $stmt = $conn->prepare("SELECT username, ROUND(score, 2) AS rounded_score FROM high_scores ORDER BY rounded_score DESC LIMIT 20");
        $stmt->execute();
        $highScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($highScores as $score) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($score['username']) . "</td>";
            echo "<td>" . htmlspecialchars($score['rounded_score']) . "</td>";
            echo "</tr>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
    </tbody>
</table>
<?php endif; ?>
</body>
</html>
