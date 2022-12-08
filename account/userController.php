<?php
    include_once 'UserService.php';

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