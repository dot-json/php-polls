<?php
    session_start();
    $uid = $_SESSION["uid"] ?? 0;

    function date_compare($element1, $element2) {
        $datetime1 = strtotime($element1['createdAt']);
        $datetime2 = strtotime($element2['createdAt']);
        return $datetime2 - $datetime1;
    }

    $all_polls = json_decode(file_get_contents("data/polls.json"), true);
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

    function sort_answears($poll) {
        $array = $poll["answers"];
        uasort($array, function($a, $b) {
            return $b <=> $a;
        });
        return $array;
    }
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
    <nav class="navbar">
        <a href="index.php" class="nav-title-wrapper">
            <div class="nav-icon-wrapper"><img src="resources/poll.svg" alt="poll icon"></div>
            <span class="nav-title" >POLLS</span>
        </a>
        <div class="nav-action-wrapper">
            <?php if (!isset($_SESSION["username"])): ?>
                <a class="nav-action" href="login.php">Log In</a>
                <a class="nav-action" href="register.php">Register</a>
            <?php else: ?>
                <span class="nav-user"><?=$_SESSION["username"]?></span>
                <a class="nav-action" href="logout.php">Log Out</a>
            <?php endif?>
        </div>     
    </nav>
    <main class="pagewrapper">
        <h1 class="title">Szavazások</h1>
        <h2 class="undertitle"><i>"Ezen az oldalon szavazunk dolgokról..."</i></h2>
        <?php if (isset($_SESSION["username"]) && $_SESSION["isAdmin"] == true): ?>
            <a class="new-poll-button" href="new-poll.php">+ Új szavazás</a>
        <?php endif?> 
        <div class="poll-container">
            <?php foreach($active_polls as $poll): ?>
                <div class="poll-box">
                    <span class="poll-title"><span>#<?=$poll["id"]?> - </span><?=$poll["question"]?></span>
                    <a class="poll-button" href=<?php if (isset($_SESSION["username"])) { echo 'poll.php?pollid=' . $poll["id"]; } else { echo "login.php"; }?>><?php if ($uid != 0 && array_key_exists("voted", $poll) && array_key_exists($uid, $poll["voted"])) { echo 'Szavazat frissítése'; } else { echo "Szavazás"; }?></a>
                    <?php if (isset($_SESSION["username"]) && $_SESSION["isAdmin"] == true): ?>
                        <div class="poll-modifiers-wrapper">
                            <a class="poll-modify-button" href="modify-poll.php?pollid=<?=$poll["id"]?>">Módosítás</a>
                            <a class="poll-delete-button" href="delete-poll.php?pollid=<?=$poll["id"]?>">Törlés</a>
                        </div>
                    <?php endif?>                 
                    <div class="poll-date-container">
                        <span class="poll-date">Létrehozva: <?=$poll["createdAt"]?></span>
                        <span class="poll-date">Határidő: <?=$poll["deadlineAt"]?></span>
                    </div>               
                </div>            
            <?php endforeach?>
            <div class="divider">
                <span class="divider-text">régebbiek</span>
                <hr width="100%" color="#01283d"/>
            </div>
            <?php foreach($past_polls as $poll): ?>
                <div class="poll-box">
                    <span class="poll-title"><span>#<?=$poll["id"]?> - </span><?=$poll["question"]?></span>
                    <span class="poll-result-title">Eredmény</span>
                    <div class="poll-result-container">
                        <ol class="poll-result-list" type="I">
                            <?php foreach(sort_answears($poll) as $key => $val): ?>
                                <li class="poll-result-entry"><?=$key?> <span style="font-weight: 400;">|</span> <span style="font-weight: 500;"><?=$val?> szavazattal</span></li>
                            <?php endforeach?>
                        </ol>
                    </div>   
                    <?php if (isset($_SESSION["username"]) && $_SESSION["isAdmin"] == true): ?>
                        <a style="width: 100%;" class="poll-delete-button" href="delete-poll.php?pollid=<?=$poll["id"]?>">Törlés</a>
                    <?php endif?>                 
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