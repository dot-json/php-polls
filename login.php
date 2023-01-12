<?php

    $errors = [];
    if (isset($_POST["submit"])) {
        $username = $_POST["username"] ?? "";
        $password = $_POST["password"] ?? "";

        if ($username == "") {
            array_push($errors, "nincs felhasználónév!");
        }
        if ($password == "") {
            array_push($errors, "nincs jelszó!");
        }

        if (count($errors) == 0) {
            $users = json_decode(file_get_contents("users.json"), true);
            foreach($users as $user){
                if ($user["username"] == $username && $user["password"] == $password){
                    //good auth
                }
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/logreg.css">
    <title>POLLS | Log In</title>
</head>
<body>
    <main class="pagewrapper">
        <nav class="navbar">
            <a href="index.php" class="nav-title-wrapper">
                <div class="nav-icon-wrapper"><img src="resources/poll.svg" alt="poll icon"></div>
                <span class="nav-title" >POLLS</span>
            </a>
            <div class="nav-action-wrapper">
                <a class="nav-action" href="login.php">Log In</a>
                <a class="nav-action" href="register.php">Register</a>
            </div>     
        </nav>
        <div class="logreg-container">
            <div class="logreg-box">
                <span class="logreg-title">Bejelentkezés</span>
                <form class="form-wrapper" action="login.php" method="post">
                    <div class="entry-wrapper">
                        <label class="input-label" for="username">felhasználónév</label>
                        <input class="text-input" type="text" name="username" id="username">
                    </div>
                    <div class="entry-wrapper">
                        <label class="input-label" for="password">jelszó</label>
                        <input class="text-input" type="password" name="password" id="password">
                    </div>
                    <div class="error-text-wrapper">
                        <?php foreach($errors as $error): ?>
                            <span class="error-text">- <?=$error?></span>
                        <?php endforeach?>
                    </div>
                    <input class="submit-button" name="submit" type="submit" value="Belépés">
                </form>
            </div>
        </div>
    </main>
</body>
</html>