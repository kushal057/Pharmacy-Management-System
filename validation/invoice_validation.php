<?php
session_start();
include "../classes/dbh.classes.php";

$verify = json_decode(file_get_contents("php://input"), true);

if(isset($verify["customer"])) {
    $conn = new Dbh();
    $customerName = explode(" ",$verify["customer"]);
    $firstname = $customerName[0];
    $lastname = $customerName[count($customerName) - 1];
    $customer = $conn->connect()->prepare("SELECT * FROM customer WHERE firstname = ? AND lastname = ? AND users_id = ?");
    $customer->execute(array($firstname, $lastname, $_SESSION["users_id"]));
    $customerCount = $customer->rowCount();
    $customerData = $customer->fetch();
    if(!$customerCount > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("result" => "customerError"));
        exit();
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("customerName" => $verify["customer"],
        "address" => $customerData["municipality"] . ", " . $customerData["district"],
        "phone" =>"(+977)-" . $customerData["phone"],
        "email" => $customerData["email"]));
        exit();
    }
    
    
}

if(isset($verify["medicine"])) {
    $conn = new Dbh();
    $medicineName = $verify["medicine"];
    $medicine = $conn->connect()->prepare("SELECT * FROM medicine WHERE medicine_name = ? AND users_id = ?");
    $medicine->execute(array($medicineName, $_SESSION["users_id"]));
    $medicineCount = $medicine->rowCount();
    $medicineData = $medicine->fetch();
    if(!$medicineCount > 0) {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(array("result" => "medicineError"));
        exit();
    } else {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array("medicineName" => $medicineData["medicine_name"],
        "medicineType" => $medicineData["medicine_type"],
        "expiryDate" => $medicineData["year"] . "-" . $medicineData["month"] . "-" . $medicineData["date"]));
        exit();
    }
}

?>