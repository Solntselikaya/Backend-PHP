<?php
    class UserRegisterModel {
        private $fullName;
        private $password;
        private $email;
        private $address;
        private $birthDate;
        private $gender;
        private $phoneNumber;

        public function __construct($data) {
            $this->fullName = $data->fullName;
            $this->setPassword($data->password);
            $this->email = $data->email;
            $this->address = $data->address;
            $this->birthDate = date('y-m-d',strtotime($data->birthDate));
            $this->gender = $data->gender;
            $this->phoneNumber = $data->phoneNumber;
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
        }

        private function setPassword($password) {
            if (strlen($password) < 6) {
                throw new Exception('wrong password');
            }
            $this->password = $password;
        }
    }
?>