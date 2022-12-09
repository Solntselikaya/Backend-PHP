<?php

class TokenResponse {
    private $token;

    public function __construct($responseBody) {
        $this->token = isset($responseBody) ? $responseBody : null;
    }
}

?>