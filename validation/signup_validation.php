<?php

session_start();

include "../classes/dbh.classes.php";

$checkDuplicate = json_decode(file_get_contents('php://input'), true);

if(isset($checkDuplicate["input"])) {
    $dbh = new Dbh();
    $stmt = $dbh->connect()->prepare("SELECT COUNT(*) FROM users WHERE users_email=? AND users_id=?");
    $stmt->execute(array($checkDuplicate["input"], $_SESSION["users_id"]));
    $result = $stmt->fetchColumn();
    if($result > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo  json_encode(array("result" => "true"));
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("result" => "false"));
    }
}

?>