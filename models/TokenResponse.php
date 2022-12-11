<?php

class TokenResponse {
    private $token;

    public function __construct($token = null) {
        $this->token = $token;
    }

    public function echoContents() {
        echo json_encode([
            'token' => $this->token
        ]);
    }
}

?>