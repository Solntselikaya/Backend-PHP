<?php

include_once 'models/UserRegisterModel.php';
include_once 'models/LoginCredentials.php';
include_once 'helpers/token.php';

class UserService {
    public static function register($data) {
        $newUser = new UserRegisterModel($data->body);
        $newUser->save();
    }

    public static function login($data) {
        $user = new LoginCredentials($data->body);
        $user->login();
    }

    public static function logout() {
        $authList = explode(' ', getallheaders()['Authorization']);
        $token = $authList[1];

        if (!isTokenExist($token)) {
            setHTTPStatus(401, 'Invalid token.');
            exit;
        }

        if (isTokenInBlackList($token)) {
            setHTTPStatus(401, 'User is unauthorized.');
            exit;
        }

        if(!isTokenAlive($token)) {
            setHTTPStatus(401, 'Token has been expired.');
            addTokenToBlackList($token);
            exit;
        }

        addTokenToBlackList($token);
        setHTTPStatus(200);
    }
}

?>