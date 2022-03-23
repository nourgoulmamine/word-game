<?php
// PUBLISHED: https://cs4640.cs.virginia.edu/ng9sc/hw4/
// Sources used: https://cs4640.cs.virginia.edu
// https://www.php.net/manual/en/function.strcmp.php
// https://www.w3schools.com/php/php_looping_for.asp
// https://stackoverflow.com/questions/51731048/php-7-2-7-warning-use-of-undefined-constant
// https://stackoverflow.com/questions/48236765/undefined-constant-error-in-php-7-2
// https://www.w3schools.com/tags/att_input_required.asp
// Partnered with Selena Johnson (Scj4ve)

// Start session
session_start();

// Register the autoloader
spl_autoload_register(function ($classname) {
    include "classes/$classname.php";
});

// Parse the query string for command
$command = "login";

if (isset($_GET["command"]))
    $command = $_GET["command"];

if (!isset($_SESSION["email"]) || !isset($_SESSION["name"])) {
    $command = "login";
}

// Instantiate the controller and run
$game = new WordGameController($command);
$game->run();
