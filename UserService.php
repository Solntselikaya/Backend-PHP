<?php
    include_once 'UserRegisterModel.php';

    class UserService {
        public static function register($data) {
            $newUser = new UserRegisterModel($data->body);
            $newUser->save();
        }
    }
?>