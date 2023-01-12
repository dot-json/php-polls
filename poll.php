<?php
    $id = $_GET["pollid"] ?? '';

    if ($id == ""){
        header('Location: index.php');
    }

    $polls = json_decode(file_get_contents("polls.json"), true);
    $current;

    foreach($polls as $poll){
        if ($poll["id"] == $id){
            $current = $poll;
            break;
        }
    }

    $multi_result = [];
    $single_result = "";
    $submit_result = "";
    $submit_result_color = "";
    if (isset($_GET['submit'])) {     
        if ($current["isMultiple"]){
            foreach($current["options"] as $option){
                if (isset($_GET[str_replace(' ', '', $option)])){
                    array_push($multi_result, $option);
                }
            }
            if (count($multi_result) == 0){
                $submit_result = "nincs kijelölt opció!";
                $submit_result_color = "#b8151c";
            } else {
                $submit_result = "sikeres szavazás!";
                $submit_result_color = "#00ab36";
            }
        } else {
            if (isset($_GET["result"])){
                $single_result = $_GET["result"];
                $submit_result = "sikeres szavazás!";
                $submit_result_color = "#00ab36";
            } else {
                $submit_result = "nincs kijelölt opció!";
                $submit_result_color = "#b8151c";
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
    <link rel="stylesheet" href="styles/poll.css">
    <title>POLLS | <?=$current["question"]?></title>
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
        <div class="poll-container">
            <div class="poll-box">
                <span class="poll-title"><span>#<?=$poll["id"]?> - </span><?=$current["question"]?></span>
                <form class="poll-action-container" method="get" action="<?=$_SERVER['PHP_SELF'];?>" novalidate>
                    <input type="hidden" name="pollid" value=<?=$current["id"]?>>
                    <?php if($current["isMultiple"]): ?>
                        <span class="poll-description">*több opció választható</span>
                        <?php foreach($current["options"] as $option): ?>
                            <div class="poll-option-container">
                                <input type="checkbox" name=<?=str_replace(' ', '', $option)?> id=<?=str_replace(' ', '', $option)?> value="checked">
                                <label class="poll-option-label" for=<?=str_replace(' ', '', $option)?>><?=$option?></label>
                            </div>
                        <?php endforeach?>
                    <?php else: ?>
                        <span class="poll-description">*egy opció választható</span>
                        <?php foreach($current["options"] as $option): ?>
                            <div class="poll-option-container">
                                <input type="radio" name=result id=<?=str_replace(' ', '', $option)?> value=<?=str_replace(' ', '', $option)?>>
                                <label class="poll-option-label" for=<?=str_replace(' ', '', $option)?>><?=$option?></label>
                            </div>
                        <?php endforeach?>
                    <?php endif?>
                    <div class="poll-submit-wrapper">
                        <input class="poll-button" type="submit" name="submit" value="Szavazás">
                        <span class="poll-submit-result" style='color: <?=$submit_result_color?>'><?=$submit_result?></span>
                    </div>                   
                </form>
                <div class="poll-date-container">
                    <span class="poll-date">Létrehozva: <?=$current["createdAt"]?></span>
                    <span class="poll-date">Határidő: <?=$current["deadlineAt"]?></span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>