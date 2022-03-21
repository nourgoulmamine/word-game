<?php
class WordGameController {

    private $command;

    public function __construct($command) {
        $this->command = $command;
    }

    public function run() {
        switch($this->command) {
            case "question":
                $this->word();
                break;
            case "logout":
                $this->destroyCookies();
            case "login":
            default:
                $this->login();
                break;
        }
    }

    // Clear all the cookies that we've set
    private function destroyCookies() {          
        setcookie("correct", "", time() - 3600);
        setcookie("name", "", time() - 3600);
        setcookie("email", "", time() - 3600);
        setcookie("score", "", time() - 3600);
    }
    

    // Display the login page (and handle login logic)
    public function login() {
        if (isset($_POST["email"]) && !empty($_POST["email"])) { /// validate the email coming in
            setcookie("name", $_POST["name"], time() + 3600);
            setcookie("email", $_POST["email"], time() + 3600);
            setcookie("score", 0, time() + 3600);
            header("Location: ?command=question");
            return;
        }

        include "templates/login.php";
    }


    // Load word ?
    private function loadWord() {
        $wordData = file_get_contents("http://www.cs.virginia.edu/~jh2jf/courses/cs4640/spring2022/wordlist.txt

", true);
        // NEED TO PUT WORD LIST INTO ARRAY
        return $wordData[0]; // return random word
    }


    // Display the question template (and handle question logic)
    public function word() {
        // set user information for the page from the cookie
        $user = [
            "name" => $_COOKIE["name"],
            "email" => $_COOKIE["email"],
            "score" => $_COOKIE["score"]
        ];

        // load the question
        $word = $this->loadWord();
        if ($word == null) {
            die("No words available");
        }

        // if the user submitted an answer, check it
        if (isset($_POST["answer"])) {
            $answer = $_POST["answer"];

            if ($_COOKIE["answer"] == $answer) {
                // user answered correctly -- perhaps we should also be better about how we
                // verify their answers, perhaps use strtolower() to compare lower case only.
                $message = "<div class='alert alert-success'><b>$answer</b> was correct!</div>";

                // Update the score
                $user["score"] += 10;  
                // Update the cookie: won't be available until next page load (stored on client)
                setcookie("score", $_COOKIE["score"] + 10, time() + 3600);
            } else { 
                $letters = 0; // amount of letters you got right
                // How many letters you got right
                $common_letters = similar_text($_COOKIE["answer"], $answer);

                // How many letters were in the correct location
                $real_answer_length = strlen($_COOKIE["answer"]);

// foreach letters in real_answer_length
                for ($i = 0; $i < $real_answer_length; $i++) {
                    if ($answer[i] === $real_answer_length[i]) {
                        $letters += 1; // increment amount of letters you got in the correct location.
                    }
                }

                // If the guessed word was too long or short
                $length = "";
                $user_answer = strlen($answer);
                if ($user_answer > $real_answer_length) {
                    $length = "too long";
                }
                elseif ($user_answer < $real_answer_length) {
                    $length = "too short";
                } else {
                    $length = "the same length";
                }
                $message = "<div class='alert alert-danger'>There were <b>$common_letters</b> characters in the word. There were <b>$letters</b> in the correct place. Your answer was <b>$length</b>.
                </div>";
            }
            setcookie("correct", "", time() - 3600);
        }

        // update the question information in cookies
        setcookie("answer", $word["correct_answer"], time() + 3600);

        include("templates/question.php");
    }
}