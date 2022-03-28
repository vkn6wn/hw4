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
            $_SESSION["num_guesses"] = 0;
            $_SESSION["guesses"] = [];
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
            "score" => $_SESSION["score"],
            "num_guesses" => $_SESSION["num_guesses"],
            "guesses" => $_SESSION["guesses"]
        ];

        // load the question
        $question = $this->loadQuestion();
        $_SESSION["question"] = $question["question"];
        $_SESSION["answer"] = $question["correct_answer"];

        $guess = "";
        $feedback = "";

        
        if ($question == null) {
            die("No questions available");
        }

        $message = "";
        $arr = [];
        $present = [];
        $green_letters = [];
        $test = [];

        // if the user submitted an answer, check it
        if (isset($_POST["answer"])) {
            $_SESSION["num_guesses"] += 1;
            $user["num_guesses"] = $_SESSION["num_guesses"]; 
            $answer = strtolower($_POST["answer"]);
            $answer_original = $_POST["answer"];

            $_SESSION["guesses"][] = $answer_original;
            $user["guesses"] = $_SESSION["guesses"]; 
            $guess = implode(', ', $_SESSION["guesses"]);
            
            if ($_SESSION["answer"] === $answer) {
                // user answered correctly -- perhaps we should also be better about how we
                // verify their answers, perhaps use strtolower() to compare lower case only.
                $message = "<div class='alert alert-success'><b>$answer_original</b> was correct!</div>";

                // Update the score
                $_SESSION["score"] += 10;
                $user["score"] = $_SESSION["score"];  
                // // Update the cookie: won't be available until next page load (stored on client)
                // setcookie("score", $_SESSION["score"] + 10, time() + 3600);
              
            } else { 

                $count_correct_position = 0;
                $in_word = false;
                $in_right_place = false;
                $count_in_word = 0;
                $count_placement = 0;

                // say how many characters in their guess were in the correct position
                for ($i = 0; $i < strlen($answer); $i++) { // iterate through user guess
                    for ($j = 0; $j < strlen($_SESSION["answer"]); $j++) {  // iterate through correct word
                        if ($answer[$i] === ($_SESSION["answer"])[$j]) {
                            $in_word = true;
                            if ($i === $j) {
                                $in_right_place = true;
                                $test[] = $i;
                            }
                        }
                    }
                    if ($in_word) {
                        $present[] = $answer[$i];
                        $in_word = false;
                    }
                    if ($in_right_place) {
                        $green_letters[] = $answer[$i];
                        $in_right_place = false;
                    }
                }

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

                $present = array_unique($present, SORT_STRING);
                $count_in_word = count($present);
                $present_implode = implode(", ", $present);

                $green_letters = array_unique($green_letters, SORT_STRING);
                $count_correct_position = count($green_letters);
                $green_letters_implode = implode(", ", $green_letters);
                $test_implode = implode(", ", $test);
                // $message = "<div class='alert alert-danger'><b>$answer</b> was incorrect! Your word length is <b>$length</b>!</div>";
                
                $feedback = "(Feedback: <b>$count_in_word</b> characters [<b>$present_implode</b>] in your guess were in the target word.
                <b>$count_correct_position</b> characters [<b>$green_letters_implode</b>] in your guess were in the correct position.
                Your word length is <b>$length</b>.)";

                $message = "<div class='alert alert-danger'><b>$answer_original</b> was incorrect!</div>";
                // The answer was: {$_COOKIE["answer"]}
            }
            // setcookie("correct", "", time() - 3600);
        }

        // update the question information in cookies
        // setcookie("answer", $question["correct_answer"], time() + 3600);

        include("question.php");
    }

    public function gameOver() {
        
    }
}