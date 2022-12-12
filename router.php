<?php

include_once 'account/userController.php';
include_once 'dish/dishController.php';
include_once 'helpers/headers.php';
include_once 'models/Response.php';

// $method - get, post, delete etc.
function route($method, $url, $data) {
    switch ($url[1]) {
        case 'account':
            userResponse($method, array_slice($url, 2), $data);
            break;
        case 'dish':
            dishResponse($method, array_slice($url, 2), $data);
            break;
        default:
            $response = new Response(404, "There is no such path as /$url[1]");
            setHTTPStatus(404, $response);
            break;
    }
}

?>