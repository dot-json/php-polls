<?php
    session_start();
    if (!isset($_SESSION["username"]) || $_SESSION["isAdmin"] == false) {
        header("location: index.php");
    }

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

    $submit_result = "";
    $submit_result_color = "";

    $options = [];
    if (isset($_GET['submit'])) {
       if (!isset($_GET["question"])) {
            $submit_result = "sikertelen módosítás!";
            $submit_result_color = "#a70000";
       } else {
            $question = $_GET["question"] ?? "";
            $isMultiple = isset($_GET["isMultiple"]);
            $o1 = $_GET["option1"] ?? "";
            $o2 = $_GET["option2"] ?? "";
            $o3 = $_GET["option3"] ?? "";
            $o4 = $_GET["option4"] ?? "";
            $o5 = $_GET["option5"] ?? "";
            $deadline = $_GET["deadline"] ?? "";

            if ($o1 != "") { array_push($options, $o1); }
            if ($o2 != "") { array_push($options, $o2); }
            if ($o3 != "") { array_push($options, $o3); }
            if ($o4 != "") { array_push($options, $o4); }
            if ($o5 != "") { array_push($options, $o5); }

            if (count($options) >= 2 && count(array_unique($options)) == count($options) && $deadline != "") {

                $new_poll = [
                    "id" => $current_poll["id"],
                    "question" => $question,
                    "options" => $options,
                    "isMultiple" => $isMultiple,
                    "createdAt" => $current_poll["createdAt"],
                    "deadlineAt" => $deadline,
                    "answers" => $current_poll["answers"]
                ];
                $polls[array_search($current_poll, $polls)] = $new_poll;
                file_put_contents("data/polls.json", json_encode($polls, JSON_PRETTY_PRINT));

                $submit_result = "sikeres módosítás!";
                $submit_result_color = "#00ab36";
                header("location: index.php");    
            } else {
                $submit_result = "sikertelen módosítás!";
                $submit_result_color = "#a70000";
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
    <title>POLLS | Edit Poll</title>
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
            <div class="newpoll-box">      
                <form class="newpoll-form" method="get" action="<?=$_SERVER['PHP_SELF'];?>" novalidate>
                    <div class="newpoll-question-wrapper">
                        <span class="newpoll-id">#<?=$current_poll["id"]?> - </span>
                        <div class="newpoll-entry-wrapper">
                            <label class="newpoll-input-label" for="question">kérdés</label>
                            <input class="newpoll-question-input" type="text" name="question" id="question" value='<?=$current_poll["question"]?>'>
                        </div>
                    </div>
                    <input type="hidden" name="pollid" value=<?=$current_poll["id"]?>>
                    <div class="newpoll-inputs-wrapper">
                        <div class="poll-option-container">
                            <input type="checkbox" name=isMultiple id="isMultiple" value="yes" <?php if ($current_poll["isMultiple"]) { echo "checked"; }?>>
                            <label class="poll-option-label" for="isMultiple">Több választásos?</label>
                        </div>
                        <span class="poll-description">*választási lehetőségek (max 5)</span>
                        <div class="newpoll-options-wrapper">
                            <div class="newpoll-entry-wrapper">
                                <label class="newpoll-input-label" for="option1">1. opció</label>
                                <input class="newpoll-question-input" type="text" name="option1" id="option1" value='<?=$current_poll["options"][0]?>'>
                            </div>
                            <div class="newpoll-entry-wrapper">
                                <label class="newpoll-input-label" for="option2">2. opció</label>
                                <input class="newpoll-question-input" type="text" name="option2" id="option2" value='<?=$current_poll["options"][1]?>'>
                            </div>
                            <div class="newpoll-entry-wrapper">
                                <label class="newpoll-input-label" for="option3">3. opció</label>
                                <input class="newpoll-question-input" type="text" name="option3" id="option3" value='<?=$current_poll["options"][2]?>'>
                            </div>
                            <div class="newpoll-entry-wrapper">
                                <label class="newpoll-input-label" for="option4">4. opció</label>
                                <input class="newpoll-question-input" type="text" name="option4" id="option4" value='<?=$current_poll["options"][3]?>'>
                            </div>
                            <div class="newpoll-entry-wrapper">
                                <label class="newpoll-input-label" for="option5">5. opció</label>
                                <input class="newpoll-question-input" type="text" name="option5" id="option5" value='<?=$current_poll["options"][4]?>'>
                            </div>
                            <div class="newpoll-entry-wrapper">
                                <label class="newpoll-input-label" for="deadline">határidő</label>
                                <input type="date" id="deadline" name="deadline" pattern="\d{4}-\d{2}-\d{2}" value='<?=$current_poll["deadlineAt"]?>'>
                            </div>
                        </div>
                    </div>                   
                    <div class="poll-submit-wrapper">
                        <input class="poll-button" type="submit" name="submit" value="Módosítás">
                        <span class="poll-submit-result" style='color: <?=$submit_result_color?>'><?=$submit_result?></span>
                    </div>                   
                </form>
            </div>
        </div>
    </main>
</body>
</html>