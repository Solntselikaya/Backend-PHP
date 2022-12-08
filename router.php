<?php

    include_once 'account/userController.php';

    // $method - get, post, delete etc.
    function route($method, $url, $data) {
        switch ($url[1]) {
            case 'account':
                userResponse($method, array_slice($url, 2), $data);
                break;
            default:
                echo 'no such method baka';
                break;
        }
    }
?>