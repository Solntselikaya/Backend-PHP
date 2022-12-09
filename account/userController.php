<?php

include_once 'UserService.php';
include_once 'helpers/headers.php';

function userResponse($method, $url, $data) {
    switch ($method) {
        case 'POST':
            switch ($url[0]) {
                case 'register':
                    UserService::register($data);
                    break;
                case 'login':
                    break;
                case 'logout':
                    break;
                default:
                    setHTTPStatus(404, "There is no such path");
                    break;
            }
            break;
        case 'GET':
            break;
        case 'PUT':
            break;
    }
}

?>