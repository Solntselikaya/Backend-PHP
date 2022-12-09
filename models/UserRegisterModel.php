<?php
include_once 'helpers/headers.php';
include_once 'helpers/token.php';

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
        $this->birthDate = isset($data->birthDate) ? date('y-m-d',strtotime($data->birthDate)) : null;
        $this->setGender($data->gender);
        $this->phoneNumber = isset($data->phoneNumber) ? $this->setPhoneNumber($data->phoneNumber) : null;

        $err = array();
        foreach ($this->errors as $key => $value) {
            if ($value) {
                $err[$key] = $value;
            }
        }

        if($err) {
            setHTTPStatus(400, "One or more validation errors occured.", $err);
            exit;
        }
    }

    private function setName($name) {
        if (strlen($name) < 1) {
            //throw new Exception('Name is too short');
            $this->errors['Name'] = "Name is too short.";
        }
        $this->fullName = $name;
    }

    //в пароле должна быть хотя бы одна циферка...
    //надо добавить в проверку
    private function setPassword($password) {
        if (strlen($password) < 6) {
            //throw new Exception('Wrong password');
            $this->errors['Password'] = "Password is too short.";
        }
        $this->password = hash('sha1', $password);
    }

    private function setEmail($email) {
        $emailExists = $GLOBALS['dbLink']->query("SELECT email FROM users WHERE email = '$email'")->fetch_assoc();
        if (!is_null($emailExists)) {
            //throw new Exception('Wrong email');
            $this->errors['DuplicateEmail'] = "Email '$email' is already used.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['Email'] = "The Email field is not a valid e-mail address.";
        }
        $this->email = $email;
    }

    private function setGender($gender) {
        if ($gender != 'Male' && $gender != 'Female') {
            //throw new Exception('Wrong gender');
            $this->errors['Gender'] = "Wrong gender.";
        }
        $this->gender = $gender;
    }

    private function setPhoneNumber($phoneNumber) {
        $pattern = '/^\+[7]\ \([0-9]{3}\)\ [0-9]{3}-[0-9]{2}-[0-9]{2}$/';
        if (isset($phoneNumber) && !preg_match($pattern, $phoneNumber)) {
            //throw new Exception('Wrong phone number');
            $this->errors['PhoneNumber'] = "The PhoneNumber field is not a valid phone number.";
        }
        else if (!isset($phoneNumber)) {
            $phoneNumber = null;
        }
        $this->phoneNumber = $phoneNumber;
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
        setHTTPStatus(200, $jwt);
    }
}
?>