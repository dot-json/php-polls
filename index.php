<?php
    function date_compare($element1, $element2) {
        $datetime1 = strtotime($element1['createdAt']);
        $datetime2 = strtotime($element2['createdAt']);
        return $datetime2 - $datetime1;
    }

    $all_polls = json_decode(file_get_contents("polls.json"), true);
    $active_polls = [];
    $past_polls = [];

    foreach($all_polls as $poll){
        if((time()-(60*60*24)) < strtotime($poll["deadlineAt"])){
            array_push($active_polls, $poll);
        } else {
            array_push($past_polls, $poll);
        }
    }

    usort($active_polls, 'date_compare');
    usort($past_polls, 'date_compare');
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/common.css">
    <link rel="stylesheet" href="styles/home.css">
    <title>POLLS</title>
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
        <h1 class="title">Szavazások</h1>
        <h2 class="undertitle"><i>"Ezen az oldalon szavazunk dolgokról..."</i></h2>
        <div class="poll-container">
            <?php foreach($active_polls as $poll): ?>
                <div class="poll-box">
                    <span class="poll-title"><span>#<?=$poll["id"]?> - </span><?=$poll["question"]?></span>
                    <form class="poll-form" action="poll.php" method="get" novalidate>
                        <input type="hidden" name="pollid" value=<?=$poll["id"]?>>
                        <input class="poll-button" type="submit" value="Szavazás" > 
                    </form>                
                    <div class="poll-date-container">
                        <span class="poll-date">Létrehozva: <?=$poll["createdAt"]?></span>
                        <span class="poll-date">Határidő: <?=$poll["deadlineAt"]?></span>
                    </div>               
                </div>            
            <?php endforeach?>
            <?php foreach($past_polls as $poll): ?>
                <div class="poll-box">
                    <span class="poll-title"><span>#<?=$poll["id"]?> - </span><?=$poll["question"]?></span>
                    <form class="poll-form" action="poll.php" method="get" novalidate>
                        <input type="hidden" name="pollid" value=<?=$poll["id"]?>>
                        <input class="poll-button" type="submit" value="Lejárt" disabled> 
                    </form>                
                    <div class="poll-date-container">
                        <span class="poll-date">Létrehozva: <?=$poll["createdAt"]?></span>
                        <span class="poll-date">Határidő: <?=$poll["deadlineAt"]?></span>
                    </div>               
                </div>            
            <?php endforeach?>
        </div>  
    </main>
</body>
</html>