<?php
    session_start();
    $id = $_GET["pollid"] ?? '';

    if ($id == ""){
        header('Location: index.php');
    }

    $polls = json_decode(file_get_contents("data/polls.json"), true);

    $current_poll;
    foreach($polls as $p) {
        if ($p["id"] == $id){
            $current_poll = $p;
        }
    }

    $current_id = array_search($current_poll, $polls);
    $poll = $polls[$current_id];

    $multi_result = [];
    $single_result = "";
    $submit_result = "";
    $submit_result_color = "";
    if (isset($_GET['submit'])) {
        if (isset($_SESSION["username"])) {
            if ($poll["isMultiple"]){
                foreach($poll["options"] as $option){
                    if (isset($_GET[str_replace(' ', '', $option)])){
                        array_push($multi_result, $option);
                    }
                }
                if (count($multi_result) == 0){
                    $submit_result = "nincs kijelölt opció!";
                    $submit_result_color = "#a70000";
                } else {
                    if (array_key_exists("voted", $poll)){
                        if (array_key_exists($_SESSION["uid"], $poll["voted"])){
                            $prev_results = $poll["voted"][$_SESSION["uid"]];
                            foreach($prev_results as $pres) {
                                $poll["answers"][$pres] -= 1;
                            }
                            $poll["voted"][$_SESSION["uid"]] = $multi_result;
                            foreach($multi_result as $res) {
                                $poll["answers"][$res] += 1;
                            } 
                        } else {
                            $poll["voted"][$_SESSION["uid"]] = $multi_result;
                            foreach($multi_result as $res) {
                                $poll["answers"][$res] += 1;
                            } 
                        }
                    } else {
                        $poll["voted"][$_SESSION["uid"]] = $multi_result;
                        foreach($multi_result as $res) {
                            $poll["answers"][$res] += 1;
                        } 
                    }
                    $polls[$current_id] = $poll;
                    file_put_contents("data/polls.json", json_encode($polls, JSON_PRETTY_PRINT));

                    $submit_result = "sikeres szavazás!";
                    $submit_result_color = "#00ab36";
                }
            } else {
                if (isset($_GET["result"])){
                    $single_result = $_GET["result"];
                    
                    if (array_key_exists("voted", $poll)){
                        if (array_key_exists($_SESSION["uid"], $poll["voted"])){
                            if ($poll["voted"][$_SESSION["uid"]] != $single_result) {
                                $poll["answers"][$poll["voted"][$_SESSION["uid"]]] -= 1;
                                $poll["voted"][$_SESSION["uid"]] = $single_result;
                                $poll["answers"][$single_result] += 1;
                            }
                        } else {
                            $poll["voted"][$_SESSION["uid"]] = $single_result;
                            $poll["answers"][$single_result] += 1;
                        }
                    } else {
                        $poll["voted"][$_SESSION["uid"]] = $single_result;
                        $poll["answers"][$single_result] += 1;
                    }
                    $polls[$current_id] = $poll;
                    file_put_contents("data/polls.json", json_encode($polls, JSON_PRETTY_PRINT));

                    $submit_result = "sikeres szavazás!";
                    $submit_result_color = "#00ab36";
                } else {
                    $submit_result = "nincs kijelölt opció!";
                    $submit_result_color = "#a70000";
                }
            } 
        } else {
            $submit_result = "a szavazáshoz fiók szükséges!";
            $submit_result_color = "#a70000";
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
    <title>POLLS | <?=$poll["question"]?></title>
</head>
<body>
    <main class="pagewrapper">
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
        <div class="poll-container">
            <div class="poll-box">
                <span class="poll-title"><span>#<?=$poll["id"]?> - </span><?=$poll["question"]?></span>
                <form class="poll-action-container" method="get" action="<?=$_SERVER['PHP_SELF'];?>" novalidate>
                    <input type="hidden" name="pollid" value=<?=$poll["id"]?>>
                    <?php if($poll["isMultiple"]): ?>
                        <span class="poll-description">*több opció választható</span>
                        <?php foreach($poll["options"] as $option): ?>
                            <div class="poll-option-container">
                                <input type="checkbox" name='<?=$option?>' id='<?=$option?>' value="checked">
                                <label class="poll-option-label" for='<?=$option?>'><?=$option?></label>
                            </div>
                        <?php endforeach?>
                    <?php else: ?>
                        <span class="poll-description">*egy opció választható</span>
                        <?php foreach($poll["options"] as $option): ?>
                            <div class="poll-option-container">
                                <input type="radio" name=result id='<?=$option?>' value='<?=$option?>'>
                                <label class="poll-option-label" for='<?=$option?>'><?=$option?></label>
                            </div>
                        <?php endforeach?>
                    <?php endif?>
                    <div class="poll-submit-wrapper">
                        <input class="poll-button" type="submit" name="submit" value="Szavazás">
                        <span class="poll-submit-result" style='color: <?=$submit_result_color?>'><?=$submit_result?></span>
                    </div>                   
                </form>
                <div class="poll-date-container">
                    <span class="poll-date">Létrehozva: <?=$poll["createdAt"]?></span>
                    <span class="poll-date">Határidő: <?=$poll["deadlineAt"]?></span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>