<?php

session_start();

if(!isSet($_SESSION["users_id"])) {
    echo "<script>window.location = 'login.php'</script>";
    exit();
}

include "classes/dbh.classes.php";

$input = json_decode(file_get_contents('php://input'), true);

if(isset($input["input"])){

    $dbh = new Dbh();
    $query = $dbh->connect()->prepare("SELECT medicine_name FROM medicine WHERE medicine_name LIKE ? AND users_id = ?");
    $query->execute(array($input["input"].'%', $_SESSION["users_id"]));
    $arr = $query->fetchAll();
    echo json_encode($arr);
}

?>