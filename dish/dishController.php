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
                if (is_null($url[2]) || $url[1] != 'rating' || $url[2] != 'check') {
                    $response = new Response(404, "Invalid url /$url[1]/$url[2]");
                    setHTTPStatus(404, $response);
                    exit;
                }
                DishService::isSettingDishRatingAvaliable($url[0]);
            }
            break;
        case 'POST':
            if (!preg_match($GLOBALS['UUID_REGEX'], $url[0])) {
                $response = new Response(404, "Invalid UUID $url[0]");
                setHTTPStatus(404, $response);
                exit;
            }
            if (is_null($url[1]) || $url[1] != 'rating') {
                $response = new Response(404, "Invalid url /$url[1]");
                setHTTPStatus(404, $response);
                exit;
            }
            DishService::setDishRating($url[0], $data->params['ratingScore']);
            break;
        default:
            $response = new Response(404, "There is no such method as $method");
            setHTTPStatus(404, $response);
            break;
    }
}
?>