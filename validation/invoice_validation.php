<?php

include "../classes/dbh.classes.php";

$verify = json_decode(file_get_contents("php://input"), true);

if(isset($verify["customer"]) && isset($verify["medicine"])) {
    $conn = new Dbh();
    $customerName = explode(" ",$verify["customer"]);
    $firstname = $customerName[0];
    $lastname = $customerName[count($customerName) - 1];
    $customer = $conn->connect()->prepare("SELECT * FROM customer WHERE firstname = ? AND lastname = ?");
    $customer->execute(array($firstname, $lastname));
    $customerCount = $customer->rowCount();
    $medicineName = $verify["medicine"];
    $medicine = $conn->connect()->prepare("SELECT * FROM customer WHERE medicine_name = ?");
    $medicine->execute(array($medicineName));
    
    if(!$customerCount > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("result" => "true"));
    }
    
    if(!customerCount > 0) {}
}

?>