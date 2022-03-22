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
            default:
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
            $answer = $_POST["answer"];
            $user["guesses"]++; // # of guesses
            $_SESSION["guesses"] = $user["guesses"];
            array_push($_SESSION["wordList"], $answer); // list of guessed words
            $user["wordList"] = json_encode($_SESSION["wordList"]);

            //see if user's guess is the answer
            if ($_SESSION["answer"] == strtolower($answer)) {
                header("Location: ?command=gameOver");
            } else {
                // How many letters you got right
                $common = array();
                for($i=0; $i<strlen($_SESSION["answer"]); $i++){
                    if(strpos($answer, $_SESSION["answer"][$i]) !== false){
                        array_push($common, $_SESSION["answer"][$i]);
                    }
                }
                $common = array_unique($common);
                $common_letters = count($common);


                // How many letters were in the correct location
                $answer_length = min(strlen($_SESSION["answer"]), strlen($answer));
                $letters = 0; // amount of letters you got right
                for ($i = 0; $i < $answer_length; $i++) {
                    if (strcasecmp($answer[$i], $_SESSION["answer"][$i]) == 0) {
                        $letters += 1;
                    }
                }

                // If the guessed word was too long or short
                $length = "";
                $real_length = strlen($_SESSION["answer"]);
                $user_answer = strlen($answer);
                if ($user_answer > $real_length) {
                    $length = "too long";
                } elseif ($user_answer < $real_length) {
                    $length = "too short";
                } else {
                    $length = "the same length";
                }

                $message = "<div class='alert alert-danger'>There were <b>$common_letters</b> characters in the word. There were <b>$letters</b> in the correct place. Your answer was <b>$length</b>.
                </div>";
            }
        }

        $_SESSION["answer"] = $question;
        include("templates/question.php");
    }
}
