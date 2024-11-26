<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Guess The Number</title>
<link rel="stylesheet" href="css/styles.css">
</head>
<body>
<nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="highscores.php">High Scores</a></li>
        </ul>
    </nav>
<h1>Guess The Number</h1>
<form id="guessForm" action="game.php" method="POST">
    Your Name: <input type="text" name="username" required><br>
    Set minimum: <input type="number" id="minRange" name="min_range" required><br>
    Set maximum: <input type="number" id="maxRange" name="max_range" required><br>
    Max Number Of Tries: <input type="number" id="maxTries" name="max_tries" required><br>
    Max Number Of Seconds: <input type="number" id="maxTime" name="max_time" required><br>
    Show Session info? <input type="checkbox" name="show_session" value="1"><br>
    <button type="button" onclick="setDefaultSettings()">Set Default Settings</button>
    <input type="submit" value="Start Guessing">
</form>

<script>
function setDefaultSettings() {
    document.getElementById("minRange").value = 1;
    document.getElementById("maxRange").value = 10;
    document.getElementById("maxTries").value = 5;
    document.getElementById("maxTime").value = 10;
}
</script>
</body>
</html>
