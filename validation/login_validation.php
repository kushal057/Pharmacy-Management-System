<?php
session_start();

include "../classes/dbh.classes.php";

$check = json_decode(file_get_contents('php://input'), true);

if(isset($check["email"]) && isset($check["password"])) {
    $dbh = new Dbh();
    $stmt = $dbh->connect()->prepare("SELECT * FROM users WHERE users_email=?");
    $stmt->execute(array($check["email"]));
    $result = $stmt->fetch();
    $count = $stmt->rowCount();
    if($count > 0 && password_verify($check["password"],$result["users_password"])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("result" => "true"));
        $_SESSION["users_id"] = $result["users_id"];
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("result" => "false"));
    }
}

?>