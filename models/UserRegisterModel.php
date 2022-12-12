<?php
include_once 'helpers/headers.php';
include_once 'helpers/token.php';
include_once 'models/Gender.php';

class UserRegisterModel {
    private $fullName;
    private $password;
    private $email;
    private $address;
    private $birthDate;
    private $gender;
    private $phoneNumber;
    private $errors = array();

    public function __construct($data) {
        $this->setName($data->fullName);
        $this->setPassword($data->password);
        $this->setEmail($data->email);
        $this->address = isset($data->address) ? $data->address : null;
        $this->birthDate = isset($data->birthDate) ? $this->setBirthDate($data->birthDate) : null;
        $this->setGender($data->gender);
        $this->phoneNumber = isset($data->phoneNumber) ? $this->setPhoneNumber($data->phoneNumber) : null;

        if($this->errors) {
            $response = new Response(400, "One or more validation errors occured", $this->errors);
            setHTTPStatus(400, $response);
            exit;
        }
    }

    private function setName($name) {
        if (strlen($name) < 1) {
            $this->errors['Name'] = "Name is too short";
        }
        $this->fullName = $name;
    }

    private function setPassword($password) {
        if (!preg_match('~[0-9]+~', $password)) {
            $this->errors['Password'] = "Password requires at least one digit";
        }
        if (strlen($password) < 6) {
            $this->errors['Password'] = "Password is too short";
        }
        $this->password = hash('sha1', $password);
    }

    private function setEmail($email) {
        $emailExists = $GLOBALS['dbLink']->query("SELECT email FROM users WHERE email = '$email'")->fetch_assoc();
        if (!is_null($emailExists)) {
            $this->errors['DuplicateEmail'] = "Email '$email' is already used";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['Email'] = "The Email field is not a valid e-mail address";
        }
        $this->email = $email;
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

    public function save() {
        $GLOBALS['dbLink']->query(
            "INSERT users (id, fullName, password, email, address, birthDate, gender, phoneNumber)
            values(
                UUID(),
                '$this->fullName',
                '$this->password',
                '$this->email',
                '$this->address',
                '$this->birthDate',
                '$this->gender',
                '$this->phoneNumber'
            )"
        );

        $jwt = createToken($this->email);
        $response = new TokenResponse($jwt);
        setHTTPStatus(200, $response);
    }
}
?>