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
                    UserService::login($data);
                    break;
                case 'logout':
                    UserService::logout();
                    break;
                default:
                    $response = new Response(404, "There is no such path as /$url[0]");
                    setHTTPStatus(404, $response);
                    break;
            }
            break;
        case 'GET':
            switch($url[0]) {
                case 'profile':
                    UserService::getProfile();
                    break;
                default:
                    $response = new Response(404, "There is no such path as /$url[0]");
                    setHTTPStatus(404, $response);
                    break;
            }
            break;
        case 'PUT':
            switch ($url[0]) {
                case 'profile':
                    UserService::updateProfile($data);
                    break;
                default:
                $response = new Response(404, "There is no such path as /$url[0]");
                setHTTPStatus(404, $response);
                break;
            }
            break;
        default:
            $response = new Response(404, "There is no such method as $method");
            setHTTPStatus(404, $response);
            break;
    }
}

?>