<?php

include_once 'account/userController.php';
include_once 'helpers/headers.php';

// $method - get, post, delete etc.
function route($method, $url, $data) {
    switch ($url[1]) {
        case 'account':
            userResponse($method, array_slice($url, 2), $data);
            break;
        default:
            setHTTPStatus(404, "There is no such path as /$url[1]");
            break;
    }
}

?>