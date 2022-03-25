<?php
// Register the autoloader
spl_autoload_register(function($classname) {
    include "classes/$classname.php";
});

// Parse the query string for command
$command = "login";
if (isset($_GET["command"]))
    $command = $_GET["command"];

// If the user's email is not set in the cookies, then it's not
// a valid session (they didn't get here from the login page),
// so we should send them over to log in first before doing
// anything else!
if (!isset($_COOKIE["email"])) {
    // they need to see the login
    $command = "login";
}

// Instantiate the controller and run
$trivia = new WordGameController($command);
$trivia->run();