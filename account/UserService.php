<?php

include_once 'models/UserRegisterModel.php';
include_once 'models/LoginCredentials.php';
include_once 'models/UserDto.php';
include_once 'models/UserEditModel.php';
include_once 'models/Response.php';
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
        $token = explode(' ', getallheaders()['Authorization'])[1];

        checkBearerToken($token);

        addTokenToBlackList($token);

        $response = new Response(null, "Logged Out");
        setHTTPStatus(200, $response);
    }

    public static function getProfile() {
        $token = explode(' ', getallheaders()['Authorization'])[1];

        checkBearerToken($token);

        $user = new UserDto($token);
        $user->echoContents();
    }

    public static function updateProfile($data) {
        $token = explode(' ', getallheaders()['Authorization'])[1];

        checkBearerToken($token);

        $editUser = new UserEditModel($data->body);
        $editUser->saveChanges($token);
    }
}

?>