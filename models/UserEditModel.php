<?php

include_once 'helpers/token.php';
include_once 'models/Gender.php';

class UserEditModel {
    private $fullName;
    private $birthDate;
    private $gender;
    private $address;
    private $phoneNumber;
    private $errors = array();

    public function __construct($data) {
        $this->setName($data->fullName);
        $this->birthDate = isset($data->birthDate) ? $this->setBirthDate($data->birthDate) : null;
        $this->setGender($data->gender);
        $this->address = isset($data->address) ? $data->address : null;
        $this->phoneNumber = isset($data->phoneNumber) ? $this->setPhoneNumber($data->phoneNumber) : null;

        if($this->errors) {
            $response = new Response(400, "One or more validation errors occured", $this->errors);
            setHTTPStatus(400, $response);
            exit;
        }
    }

    private function setName($name) {
        if (strlen($name) < 1) {
            $this->errors['Name'] = "Name is too short.";
        }
        $this->fullName = $name;
    }

    private function setBirthDate($birthDate): string {
        $formattedBirthDate = DateTime::createFromFormat('Y-m-d', $birthDate);
        if (!$formattedBirthDate) {
            $this->errors['BirthDate'] = "Invalid birthDate";
            return null;
        }
        else {
            return $formattedBirthDate->format('Y-m-d');
        }
    }

    private function setGender($gender) {
        if (!Gender::checkGender($gender)) {
            $this->errors['Gender'] = "Wrong gender.";
        }
        $this->gender = $gender;
    }

    private function setPhoneNumber($phoneNumber): string {
        $pattern = '/^\+[7]\ \([0-9]{3}\)\ [0-9]{3}-[0-9]{2}-[0-9]{2}$/';
        if (!preg_match($pattern, $phoneNumber)) {
            $this->errors['PhoneNumber'] = "The PhoneNumber field is not a valid phone number.";
            return null;
        }
        else {
            return $phoneNumber;
        }
    }

    public function saveChanges($token) {
        $userEmail = getEmailFromToken($token);

        $GLOBALS['dbLink']->query(
            "UPDATE users 
            SET 
            fullName = '$this->fullName', 
            birthDate = '$this->birthDate',
            gender = '$this->gender',
            address = '$this->address', 
            phoneNumber = '$this->phoneNumber'
            WHERE email = '$userEmail'"
        );

        $response = new Response(null, "Succesfully updated");
        setHTTPStatus(200, $response);
    }
}

?>