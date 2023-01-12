<?php
    $errors = [];
    if (isset($_POST["submit"])) {
        $username = $_POST["username"] ?? "";
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";
        $password2 = $_POST["password2"] ?? "";

        if ($username == "") {
            array_push($errors, "nincs felhasználónév!");
        }
        if ($email == "") {
            array_push($errors, "nincs email!");
        }
        if ($password == "") {
            array_push($errors, "nincs jelszó!");
        }
        if ($password2 == "") {
            array_push($errors, "nincs jelszóellenőrzés!");
        }
        if ($password != $password2) {
            array_push($errors, "nem egyezik a két jelszó!");
        }

        if (count($errors) == 0) {
            $users = json_decode(file_get_contents("users.json"), true);
            $new_user = [
                "uid" => end($users)["uid"] + 1,
                "username" => $username,
                "email" => $email,
                "password" => $password,
                "isAdmin" => false
            ];
            array_push($users, $new_user);
            file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));
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
    <title>POLLS | Register</title>
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
                <span class="logreg-title">Új felhasználó</span>
                <form class="form-wrapper" action="register.php" method="post">
                    <div class="entry-wrapper">
                        <label class="input-label" for="username">felhasználónév</label>
                        <input class="text-input" type="text" name="username" id="username">
                    </div>
                    <div class="entry-wrapper">
                        <label class="input-label" for="email">e-mail</label>
                        <input class="text-input" type="text" name="email" id="email">
                    </div>
                    <div class="entry-wrapper">
                        <label class="input-label" for="password">jelszó</label>
                        <input class="text-input" type="password" name="password" id="password">
                    </div>
                    <div class="entry-wrapper">
                        <label class="input-label" for="password2">jelszó ismét</label>
                        <input class="text-input" type="password" name="password2" id="password2">
                    </div>
                    <div class="error-text-wrapper">
                        <?php foreach($errors as $error): ?>
                            <span class="error-text">- <?=$error?></span>
                        <?php endforeach?>
                    </div>
                    <input class="submit-button" type="submit" name="submit" value="Regisztráció">
                </form>
            </div>
        </div>
    </main>
</body>
</html>