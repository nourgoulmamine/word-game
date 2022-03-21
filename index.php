<?php
// questions: is it ok where i have session info
//make refresh not count as a guess?
//every time user needs a new word, refresh
//case sensitive comparing
//store wordList as a class so we can have the comparison info (len, correct chars, in the right place)

session_start();
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
if (!isset($_SESSION["email"])) {
    // they need to see the login
    $command = "login";
}else{
    // echo "<script>console.log('Debug Objects: " . $_COOKIE["email"] . "' );</script>";
    // echo "<script>console.log('Debug Objects: " . $_COOKIE["name"] . "' );</script>";

    echo "<script>console.log('Debug Objects: " . $_SESSION["email"] . "' );</script>";
}

// Instantiate the controller and run
$trivia = new WordGameController($command);
$trivia->run();
