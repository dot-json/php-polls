<?php
    $id = $_POST["pollid"];
    $all_polls = json_decode(file_get_contents("data/polls.json"), true);

    $new_array = [];
    foreach($all_polls as $poll) {
        if ($poll["id"] != $id) {
            array_push($new_array, $poll);
        }
    }
    file_put_contents("data/polls.json", json_encode($new_array, JSON_PRETTY_PRINT));
    header("location: index.php");
?>