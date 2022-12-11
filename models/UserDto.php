<?php

include_once 'helpers/token.php';
include_once 'helpers/headers.php';

class UserDto {
    private $id;
    private $fullName;
    private $birthDate;
    private $gender;
    private $address;
    private $email;
    private $phoneNumber;

    public function __construct($token) {
        $userEmail = getEmailFromToken($token);

        $info = $GLOBALS['dbLink']->query(
            "SELECT id, fullName, birthDate, gender, address, email, phoneNumber 
            FROM users 
            WHERE email = '$userEmail'"
        )->fetch_assoc();

        $this->id = $info['id'];
        $this->fullName = $info['fullName'];
        $this->birthDate = $info['birthDate'];
        $this->gender = $info['gender'];
        $this->address = $info['address'];
        $this->email = $info['email'];
        $this->phoneNumber = $info['phoneNumber'];
    }

    public function getUserInfo() {
        
        setHTTPStatus(200);
        echo json_encode([
            'id' => $this->id,
            'fullName' => $this->fullName,
            'birthDate' => $this->birthDate,
            'gender' => $this->gender,
            'address' => $this->address,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber
        ]);
    }

}
?>