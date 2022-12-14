<?php
include_once 'helpers/headers.php';
include_once 'helpers/token.php';
include_once 'helpers/validators.php';
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
        $this->address = empty($data->address) ? 'NULL' : "'$data->address'";
        $this->birthDate = $this->setBirthDate($data->birthDate);
        $this->setGender($data->gender);
        $this->phoneNumber = $this->setPhoneNumber($data->phoneNumber);

        if($this->errors) {
            $response = new Response(400, "One or more validation errors occured", $this->errors);
            setHTTPStatus(400, $response);
            exit;
        }
    }

    private function setName($name) {
        if (empty($name)) {
            $this->errors['Name'] = "The FullName field is required";
        }
        else if (strlen($name) < 1) {
            $this->errors['Name'] = "Name is too short";
        }

        $this->fullName = $name;
    }

    private function setPassword($password) {
        if (empty($password)) {
            $this->errors['Password'] = "The Password field is required";
        }
        else if (strlen($password) < 6) {
            $this->errors['Password'] = "Password is too short";
        }
        else if (!preg_match('~[0-9]+~', $password)) {
            $this->errors['Password'] = "Password requires at least one digit";
        }

        $this->password = hash('sha1', $password);
    }

    private function setEmail($email) {
        if (empty($email)) {
            $this->errors['Email'] = "The Email field is required";
        }
        else {
            $emailExists = $GLOBALS['dbLink']->query("SELECT email FROM users WHERE email = '$email'")->fetch_assoc();
            if (!is_null($emailExists)) {
                $this->errors['DuplicateEmail'] = "Email '$email' is already used";
            }
            else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors['Email'] = "The Email field is not a valid e-mail address";
            }

            $this->email = $email;
        }
    }

    private function setBirthDate($birthDate) {
        if (empty($birthDate)) {
            return 'NULL';
        }
        
        $formattedBirthDate = DateTime::createFromFormat('Y-m-d', $birthDate);
        if (!$formattedBirthDate) {
            $this->errors['BirthDate'] = "Invalid birthDate";
            return 'NULL';
        }

        $formBirthDate = $formattedBirthDate->format('Y-m-d');
        return "'$formBirthDate'";
    }

    private function setGender($gender) {
        if (empty($gender)) {
            $this->errors['Gender'] = "The Gender field is required";
        }
        if (!Gender::checkGender($gender)) {
            $this->errors['Gender'] = "Wrong gender";
        }
        else {
            $this->gender = $gender;
        }
    }

    private function setPhoneNumber($phoneNumber) {
        if (empty($phoneNumber)) {
            return 'NULL';
        }

        $pattern = '/^\+[7]\ \([0-9]{3}\)\ [0-9]{3}-[0-9]{2}-[0-9]{2}$/';
        if (!preg_match($pattern, $phoneNumber)) {
            $this->errors['PhoneNumber'] = "The PhoneNumber field is not a valid phone number";
            return 'NULL';
        }

        return "'$phoneNumber'";
    }

    public function save() {
        
        $GLOBALS['dbLink']->query(
            "INSERT users (id, fullName, password, email, address, birthDate, gender, phoneNumber)
            values(
                UUID(),
                '$this->fullName',
                '$this->password',
                '$this->email',
                " . $this->address . ",
                " . $this->birthDate . ",
                '$this->gender',
                " . $this->phoneNumber . "
            )"
        );

        $jwt = createToken($this->email);
        $response = new TokenResponse($jwt);
        setHTTPStatus(200, $response);
    }
}
?>