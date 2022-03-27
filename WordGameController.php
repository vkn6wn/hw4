<?php
$user = [];
class WordGameController {
    
    private $command;

    public function __construct($command) {
        $this->command = $command;
    }

    public function run() {
        switch($this->command) {
            case "question":
                $this->question();
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

        include "login.php";
    }

    // Load a question from the API
    private function loadQuestion() {
        $triviaData = json_decode(
            file_get_contents("https://opentdb.com/api.php?amount=1&category=26&difficulty=easy&type=multiple")
            , true);
        // Return the question
        return $triviaData["results"][0];
    }

    // Display the question template (and handle question logic)
    public function question() {
        // set user information for the page from the cookie
        global $user;
        if(!isset($user)){
            $user = 'Variable name is not set';
            }
        $user = [
            "name" => $_COOKIE["name"],
            "email" => $_COOKIE["email"],
            "score" => $_COOKIE["score"]
        ];

        // load the question
        $wordVar = file_get_contents("wordlist.txt");
        $wordBank = explode("\n",$wordVar);
        $randIndex = rand(0, strlen());

        $toGuess = $wordBank[$randIndex];
        $progress = "-----";
        $guess;

        // $question = $this->loadQuestion();
        // if ($question == null) {
        //     die("No questions available");
        // }

        $message = "";

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
                $message = "<div class='alert alert-danger'><b>$answer</b> was incorrect! The answer was: {$_COOKIE["answer"]}</div>";
            }
            setcookie("correct", "", time() - 3600);
        }

        // update the question information in cookies
        setcookie("answer", $question["correct_answer"], time() + 3600);

        include("question.php");
    }
}