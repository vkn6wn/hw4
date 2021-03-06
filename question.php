<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">  
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Katie Nguyen (vkn6wn) and Sharon Chong (ssc2sht)">
        <meta name="description" content="This is our version of the Wordle game for HW4 for CS4640.">  
        <title>Wordle Game</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"> 
    </head>
    <body>
        <div class="container" style="margin-top: 15px;">
            <div class="row col-xs-8">
                <h1>CS4640 Wordle Game</h1>
                <h3>Hello, <?=$user["name"]?>! Your score: <?=$user["score"]?></h3>
                <h4>Current number of guesses: <?=$user["num_guesses"]?></h3>
                <h4>Email: <?=$user["email"]?></h3>
                <p>Instructions: A random word has been chosen. Make a guess as to what it is below and use the feedback provided in order to guess the word correctly. Goodluck!</p>
            </div>
            <div class="row">
                <div class="col-xs-8 mx-auto">
                <form action="?command=question" method="post">
                    <div class="h-100 p-5 bg-light border rounded-3">
                    <h2>What is the word?</h2>
                    <p> <?= $_SESSION["question"] ?> </p>
                    <!-- <p>Random Word: ___________</p> -->
                    <input type="hidden" name="questionid" value="<?=$question["id"]?> "/>
                    <h2>My Guesses</h2> 
                    <p><?=$guess?></p>
                    </div>
                    <?=$message?>
                    <div class="h-10 p-5 mb-3">
                        <input type="text" class="form-control" id="answer" name="answer" placeholder="Enter your word guess here">
                    </div>
                    <div class="text-center">                
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="?command=logout" class="btn btn-danger">End Game</a>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    </body>
</html>