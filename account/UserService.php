<?php
    include_once 'models/UserRegisterModel.php';

    class UserService {
        public static function register($data) {
            $newUser = new UserRegisterModel($data->body);
            $newUser->save();
        }
    }
?>