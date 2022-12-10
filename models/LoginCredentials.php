<?php

include_once 'helpers/headers.php';
include_once 'helpers/token.php';

class LoginCredentials {
    private $email;
    private $password;

    public function __construct($data) {
        $this->setEmail($data->email);
        $this->setPassword($data->password);
    }

    private function setEmail($email) {
        $emailExists = $GLOBALS['dbLink']->query("SELECT email FROM users WHERE email = '$email'")->fetch_assoc();
        if (is_null($emailExists)) {
            setHTTPStatus(400, "No user with email '$email'.");
            exit;
        }
        $this->email = $email;
    }

    private function setPassword($password) {
        $password = hash('sha1', $password);
        $passwordExists = $GLOBALS['dbLink']->query("SELECT password FROM users WHERE email = '$this->email'")->fetch_assoc();
        if (is_null($passwordExists)) {
            setHTTPStatus(400, "Login failed.");
            exit;
        }

        if ($passwordExists['password'] != $password) {
            setHTTPStatus(400, "Wrong password.");
            exit;
        }

        $this->password = $password;
    }

    public function login() {
        $jwt = getToken($this->email);
        setHTTPStatus(200, $jwt);
    }
}
?>