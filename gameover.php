<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">  
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="CS4640">
        <meta name="description" content="CS4640 Trivia Login Page">  
        <title>Wordle Game Over</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous"> 
    </head>
    <body>
        <div class="container" style="margin-top: 15px;">
            <div class="row col-xs-8">
                <h1>CS4640 Word Game - Game Over</h1>
                <p> Thank you for playing!</p>
            </div>

            <div class="row col-xs-8">
                <h1>The word was: <?=$user["answer"]?></h1>
                <h2><?=$user["name"]?>'s Scoreboard:<h2>
                <h3>Score: <?=$user["score"]?></h3>
                <h3>Current number of guesses: <?=$user["num_guesses"]?></h3>
                <h4>Email: <?php print($user["email"])?></h4>

            <form action="?command=question" method="post">
                <div class="text-center">  
                    <button type="submit" class="btn btn-primary">Play Again</button>
                    <a href="?command=logout" class="btn btn-danger">Log Out</a>
                </div>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
    </body>
</html>