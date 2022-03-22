<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Nour Goulmamine">
    <meta name="description" content="CS4640 Trivia Login Page">
    <title>Wordle Clone: Game Over</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
</head>

<body>
    <div class="row">
        <div class="container" style="margin-top: 15px;">
            <div class="row col-xs-8">
                <h1>Wordle Clone: Game Over</h1>
                <h3>Thanks for playing!</h3>
            </div>
            <div class="h-40 p-5 bg-light border rounded-3">
            <?php
                if (!empty($error_msg)) {
                    echo "<div class='alert alert-danger'>$error_msg</div>";
                }
                ?>
                <h3>Number of guesses: <?= $_SESSION["guesses"] ?>.</h3>
                <h3>The word was <?= $_SESSION["answer"] ?>.</h3>
                <div class="text-center">
                    <a href="?command=question" class="btn btn-primary">Play Again</a>
                    <a href="?command=logout" class="btn btn-danger">End Game</a>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
</body>

</html>
