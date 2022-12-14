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
        if (empty($email)) {
            $response = new Response(400,"The Email field is required");
            setHTTPStatus(400, $response);
            exit;
        }

        $emailExists = $GLOBALS['dbLink']->query("SELECT email FROM users WHERE email = '$email'")->fetch_assoc();
        if (is_null($emailExists)) {
            $response = new Response(400,"No user with email '$email'");
            setHTTPStatus(400, $response);
            exit;
        }

        $this->email = $email;
    }

    private function setPassword($password) {
        if (empty($password)) {
            $response = new Response(400,"The Password field is required");
            setHTTPStatus(400, $response);
            exit;
        }

        $password = hash('sha1', $password);
        $passwordExists = $GLOBALS['dbLink']->query("SELECT password FROM users WHERE email = '$this->email'")->fetch_assoc();
        if (is_null($passwordExists)) {
            $response = new Response(400, "Login failed");
            setHTTPStatus(400, $response);
            exit;
        }

        if ($passwordExists['password'] != $password) {
            $response = new Response(400, "Wrong password");
            setHTTPStatus(400, $response);
            exit;
        }

        $this->password = $password;
    }

    public function login() {
        $jwt = getToken($this->email);
        $response = new TokenResponse($jwt);
        setHTTPStatus(200, $response);
    }
}
?>