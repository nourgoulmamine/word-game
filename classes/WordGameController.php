<?php
class WordGameController
{

    private $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    public function run()
    {
        switch ($this->command) {
            case "question":
                $this->question();
                break;
            case "gameOver":
                $this->gameOver();
                break;
            case "logout":
                $this->logout();
            case "login":
                //            default:
                $this->login();
                break;
        }
    }

    public function logout()
    {
        session_destroy();
    }

    // Display the login page (and handle login logic)
    public function login()
    {
        if (isset($_POST["email"]) && !empty($_POST["email"])) {
            $_SESSION["name"] = $_POST["name"];
            $_SESSION["email"] = $_POST["email"];
            $_SESSION["wordList"] = array();
            $_SESSION["guesses"] = 0;
            $_SESSION["answer"]  = $this->loadQuestion();
            if ($_SESSION["answer"] == null) {
                die("No questions available");
            }
            header("Location: ?command=question");
            return;
        }
        include "templates/login.php";
    }

    private function gameOver()
    {
        include "templates/gameOver.php";
        $_SESSION["wordList"] = array();
        $_SESSION["guesses"] = 0;
        $_SESSION["answer"]  = $this->loadQuestion();
        if ($_SESSION["answer"] == null) {
            die("No questions available");
        }
    }


    // Load word ?
    private function loadQuestion()
    {

        $file = file_get_contents("https://www.cs.virginia.edu/~jh2jf/courses/cs4640/spring2022/wordlist.txt", false);
        $words = explode("\n", file_get_contents("https://www.cs.virginia.edu/~jh2jf/courses/cs4640/spring2022/wordlist.txt", false));

        $rand = rand(0, count($words));
        return $words[$rand]; // return random word
    }


    // Display the question template (and handle question logic)
    public function question()
    {
        // set user information for the page from the cookie
        $user = [
            "name" => $_SESSION["name"],
            "email" => $_SESSION["email"],
            "guesses" => $_SESSION["guesses"],
            "wordList" => json_encode($_SESSION["wordList"])
        ];

        // load the question
        $question = $_SESSION["answer"];
        $message = "";

        // if the user submitted an answer, check it
        if (isset($_POST["answer"])) {
            $answer = $_POST["answer"]; //retrieve user's answer

            $user["guesses"] += 1; //update number of guesses
            $_SESSION["guesses"] = $user["guesses"];
            array_push($_SESSION["wordList"], $answer); // list of guessed words
            $user["wordList"] = json_encode($_SESSION["wordList"]);

            //see if user's guess is the answer
            if ($_SESSION["answer"] == strtolower($answer)) {
                // user answered correctly -- perhaps we should also be better about how we
                // verify their answers, perhaps use strtolower() to compare lower case only.
                // $message = "<div class='alert alert-success'><b>$answer</b> was correct!</div>";
                header("Location: ?command=gameOver");
            } else {
                // How many letters you got right
                $common_letters = similar_text($_SESSION["answer"], $answer);

                // How many letters were in the correct location
                $real_answer_length = strlen($_SESSION["answer"]);
                $letters = 0; // amount of letters you got right
                for ($i = 0; $i < $real_answer_length; $i++) {
                    if (strcasecmp($answer[$i], $_SESSION["answer"][$i]) == 0) {
                        $letters += 1;
                    }
                }
 // added
                // If the guessed word was too long or short
                $length = "";
                $user_answer = strlen($answer);
                if ($user_answer > $real_answer_length) {
                    $length = "too long";
                } elseif ($user_answer < $real_answer_length) {
                    $length = "too short";
                } else {
                    $length = "the same length";
                }

                $message = "<div class='alert alert-danger'>There were <b>$common_letters</b> characters in the word. There were <b>$letters</b> in the correct place. Your answer was <b>$length</b>.
                </div>";
            }
        }

        // update the question information in cookies

        $_SESSION["answer"] = $question;

        include("templates/question.php");
    }
}