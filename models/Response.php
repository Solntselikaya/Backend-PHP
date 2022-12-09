<?php

class Response {
    private $status;
    private $message;
    private $errors;

    public function __construct($responseBody) {
        $this->status = isset($responseBody->status) ? $responseBody->status : null;
        $this->message = isset($responseBody->message) ? $responseBody->message : null;
        $this->message = isset($responseBody->errors) ? $responseBody->errors : null;
    }
}

?>