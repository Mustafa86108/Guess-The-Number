<?php
session_start();
include 'config.php'; 

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $_SESSION['username'] = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $_SESSION['min_range'] = filter_input(INPUT_POST, 'min_range', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['max_range'] = filter_input(INPUT_POST, 'max_range', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['max_tries'] = filter_input(INPUT_POST, 'max_tries', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['max_time'] = filter_input(INPUT_POST, 'max_time', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['show_session'] = isset($_POST['show_session']) && $_POST['show_session'] == 1 ? 1 : 0;

    // Generate a random number and store it in the session
    $_SESSION['secret_number'] = rand($_SESSION['min_range'], $_SESSION['max_range']);

    // Initialize other necessary session variables
    $_SESSION['num_tries'] = 0;
    $_SESSION['start_time'] = time();

    // Redirect to start the game
    header('Location: play.php'); // Create this file for the game interface
    exit; // Always exit after a header redirect
}
?>
