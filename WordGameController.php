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
                $this->destroySession();
            case "login":
            default:
                $this->login();
                break;
        }
    }

    // Clear all the cookies that we've set
    private function destroySession() {       
        session_unset();
        session_destroy();   
        // setcookie("correct", "", time() - 3600);
        // setcookie("name", "", time() - 3600);
        // setcookie("email", "", time() - 3600);
        // setcookie("score", "", time() - 3600);
    }
    

    // Display the login page (and handle login logic)
    public function login() {
        if (isset($_POST["email"]) && !empty($_POST["email"])) { /// validate the email coming in
            $_SESSION["name"] = $_POST["name"];
            $_SESSION["email"] = $_POST["email"];
            $_SESSION["score"] = 0;
            $wordVar = file_get_contents("wordlist.txt");
            $wordBank = explode("\n",$wordVar);
            $randIndex = rand(0, count($wordBank));
            $_SESSION["question_id"] = $randIndex;
            $_SESSION["wordbank"] = $wordBank;
            // setcookie("name", $_POST["name"], time() + 3600);
            // setcookie("email", $_POST["email"], time() + 3600);
            // setcookie("score", 0, time() + 3600);
            header("Location: ?command=question");
            return;
        }

        include "login.php";
    }

    // Load a question from the API
    private function loadQuestion() {
        // $wordVar = file_get_contents("wordlist.txt");
        // $wordBank = explode("\n",$wordVar);
        // $randIndex = rand(0, count($wordBank));
        $wb = $_SESSION["wordbank"];
        $toGuess = $wb[$_SESSION["question_id"]];
        $progress = "________";
        // for ($i = 0; $i <= strlen($toGuess); $i++)
        //     $progress .= "-";
        $question = [
            "question" => $progress,
            "correct_answer" => $toGuess
        ];
        
        return $question;
        // $triviaData = json_decode(
        //     file_get_contents("https://opentdb.com/api.php?amount=1&category=26&difficulty=easy&type=multiple")
        //     , true);
        // // Return the question
        // return $triviaData["results"][0];
    }

    // Display the question template (and handle question logic)
    public function question() {
        // set user information for the page from the cookie
        global $user;
        if(!isset($user)){
            $user = 'Variable name is not set';
            }
        $user = [
            "name" => $_SESSION["name"],
            "email" => $_SESSION["email"],
            "score" => $_SESSION["score"]
        ];

        // load the question
        $question = $this->loadQuestion();
        $_SESSION["question"] = $question["question"];
        $_SESSION["answer"] = $question["correct_answer"];

        $guess = "";

        
        if ($question == null) {
            die("No questions available");
        }

        $message = "";
        $arr = [];
        $present = [];

        // if the user submitted an answer, check it
        if (isset($_POST["answer"])) {
            $answer = $_POST["answer"];
            $arr[] = $answer;
            $guess = implode(', ', $arr);
            echo $_SESSION["answer"];
            echo $answer;
            
            if ($_SESSION["answer"] === $answer) {
                // user answered correctly -- perhaps we should also be better about how we
                // verify their answers, perhaps use strtolower() to compare lower case only.
                $message = "<div class='alert alert-success'><b>$answer</b> was correct!</div>";

                // Update the score
                $user["score"] += 10;  
                // // Update the cookie: won't be available until next page load (stored on client)
                // setcookie("score", $_SESSION["score"] + 10, time() + 3600);
                
            } else { 

                $correct_position = 0;
                $in_word = false;
                $count_in_word = 0;
                $present = [];
                $present_implode = implode(", ", $present);

                // // say how many characters in their guess were in the correct position
                // for ($i = 0; $i <= strlen($answer); $i++) { // iterate through correct word
                //     for ($j = 0; $j <= strlen($_COOKIE["answer"]); $j++) {  // iterate through user guess
                        if ($answer[1] === $_SESSION["answer"][1]) {
                            $in_word = true;
                        }
                    // }
                    if ($in_word) {
                        $count_in_word += 1;
                        $present[] = $answer[1];
                    }
                // }

                // compare guess character length to answer
                $length = "";
                if (strlen($_SESSION["answer"]) === strlen($answer)) {
                    $length = "correct";
                }
                elseif (strlen($_SESSION["answer"]) > strlen($answer)) {
                    $length = "too short";
                }
                elseif (strlen($_SESSION["answer"]) < strlen($answer)) {
                    $length = "too long";
                }

                
                // $message = "<div class='alert alert-danger'><b>$answer</b> was incorrect! Your word length is <b>$length</b>!</div>";
                $message = "<div class='alert alert-danger'><b>$answer</b> was incorrect! The <b>$count_in_word</b> letters <b>$present_implode</b> in your guess are present in the correct answer! Your word length is <b>$length</b>!</div>";
                // The answer was: {$_COOKIE["answer"]}
            }
            // setcookie("correct", "", time() - 3600);
        }

        // update the question information in cookies
        // setcookie("answer", $question["correct_answer"], time() + 3600);

        include("question.php");
    }
}