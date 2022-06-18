<?php

class LoginController extends Login {

    private $username;
    private $password;

    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }

    public function loginUser() {
        if($this->emptyInput() == false) {
            // echo "Empty Input!";
            header("location: ../index.php?error=emptyinput");
            exit();
        }

        $this->getUser($this->username,$this->password);
    }

    private function emptyInput() {
        $result = true;
        if(empty($this->username) || empty($this->password)){
            $result = false;
        }
        return $result;
    }
}

?>