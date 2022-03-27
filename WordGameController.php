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
        $wordVar = file_get_contents("wordlist.txt");
        $wordBank = explode("\n",$wordVar);
        $randIndex = rand(0, count($wordBank));

        $toGuess = $wordBank[$randIndex];
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
            "name" => $_COOKIE["name"],
            "email" => $_COOKIE["email"],
            "score" => $_COOKIE["score"]
        ];

        // load the question
        $question = $this->loadQuestion();

        $guess;

        
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
            $answer = strtolower($_POST["answer"]);
            $answer_original = $_POST["answer"];
            $arr[] = $answer_original;
            $guess = implode(', ', $arr);
            
            if ($_COOKIE["answer"] === $answer) {
                // user answered correctly -- perhaps we should also be better about how we
                // verify their answers, perhaps use strtolower() to compare lower case only.
                $message = "<div class='alert alert-success'><b>$answer_original</b> was correct!</div>";

                // Update the score
                $user["score"] += 10;  
                // Update the cookie: won't be available until next page load (stored on client)
                setcookie("score", $_COOKIE["score"] + 10, time() + 3600);
            } else { 

                $count_correct_position = 0;
                $in_word = false;
                $in_right_place = false;
                $count_in_word = 0;
                $count_placement = 0;

                // say how many characters in their guess were in the correct position
                for ($i = 0; $i < strlen($answer); $i++) { // iterate through user guess
                    for ($j = 0; $j < strlen($_COOKIE["answer"]); $j++) {  // iterate through correct word
                        if ($answer[$i] === ($_COOKIE["answer"])[$j]) {
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
                if (strlen($_COOKIE["answer"]) === strlen($answer)) {
                    $length = "correct";
                }
                elseif (strlen($_COOKIE["answer"]) > strlen($answer)) {
                    $length = "too short";
                }
                elseif (strlen($_COOKIE["answer"]) < strlen($answer)) {
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
                $message = "<div class='alert alert-danger'><b>$answer_original</b> was incorrect!
                <b>$count_in_word</b> characters in your guess were in the target word.
                <b>$count_correct_position</b> characters in your guess were in the correct position.
                Your word length is <b>$length</b>.</div>";
                // The answer was: {$_COOKIE["answer"]}
            }
            setcookie("correct", "", time() - 3600);
        }

        // update the question information in cookies
        setcookie("answer", $question["correct_answer"], time() + 3600);

        include("question.php");
    }
}