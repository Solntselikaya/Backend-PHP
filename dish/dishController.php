<?php

include_once 'dish/DishService.php';
function dishResponse($method, $url, $data) {
    switch ($method) {
        case 'GET':
            if (empty($url)) {
                DishService::getDishes($data->params);
            }
            else if (empty($url[1])) {
                if (!preg_match($GLOBALS['UUID_REGEX'], $url[0])) {
                    $response = new Response(404, "Invalid UUID $url[0]");
                    setHTTPStatus(404, $response);
                    exit;
                }
                DishService::getDishInfo($url[0]);
            }
            else {

            }
            break;
        case 'POST':
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
        default:
            $response = new Response(404, "There is no such method as $method");
            setHTTPStatus(404, $response);
            break;
    }
}
?>