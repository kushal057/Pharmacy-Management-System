<?php
session_start();
include "../classes/dbh.classes.php";

$verify = json_decode(file_get_contents("php://input"), true);

if(isset($verify["input"])) {
    $conn = new Dbh();
    $firstname = $verify["input"];
    $customer = $conn->connect()->prepare("SELECT * FROM customer WHERE firstname = ? AND users_id = ?");
    $customer->execute(array($firstname, $_SESSION["users_id"]));
    $customerCount = $customer->rowCount();
    if(!$customerCount > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("result" => "notFound"));
        exit();
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("result" => "found"));
        exit();
    }
}
?>