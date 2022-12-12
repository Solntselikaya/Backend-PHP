<?php

include_once 'basket/BasketService.php';
function basketResponse($method, $url, $data) {
    switch ($method) {
        case 'GET':
            if (!empty($url)) {
                $response = new Response(404, "There is no GET method on path /$url");
                setHTTPStatus(404, $response);
                exit;
            }
            BasketService::getBasket();
            break;
        case 'POST':
            if (!preg_match($GLOBALS['UUID_REGEX'], $url[1])) {
                $response = new Response(404, "Invalid dishId $url[1]");
                setHTTPStatus(404, $response);
                exit;
            }
            BasketService::addDish($url[1]);
            break;
        case 'DELETE':
            if (!preg_match($GLOBALS['UUID_REGEX'], $url[1])) {
                $response = new Response(404, "Invalid dishId $url[1]");
                setHTTPStatus(404, $response);
                exit;
            }
            $isIncreased = filter_var($data->params['increase'], FILTER_VALIDATE_BOOLEAN);
            switch($isIncreased) {
                case true:
                    BasketService::decreaseDish($url[1]);
                    break;
                case false:
                    BasketService::deleteWholeDish($url[1]);
                    break;
                default:
                    $response = new Response(404, "Invalid increase parameter");
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