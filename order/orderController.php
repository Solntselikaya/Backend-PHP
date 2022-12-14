<?php

include_once 'OrderService.php';
include_once 'helpers/headers.php';

function orderResponse($method, $url, $data) {
    switch ($method) {
        case 'POST':
            if (empty($url)) {
                OrderService::createOrder($data);
                exit;;
            }
            if (!preg_match($GLOBALS['UUID_REGEX'], $url[0])) {
                $response = new Response(404, "Invalid UUID $url[0]");
                setHTTPStatus(404, $response);
                exit;
            }
            if (is_null($url[1]) || $url[1] != 'status') {
                $response = new Response(404, "No such path /$url[0]/$url[1] for method 'POST'");
                setHTTPStatus(404, $response);
                exit;
            }
            OrderService::confirmOrderDelivery($url[0]);
            break;
        case 'GET':
            if (empty($url)) {
                OrderService::getListOfOrders();
                exit;;
            }
            if (!preg_match($GLOBALS['UUID_REGEX'], $url[0])) {
                $response = new Response(404, "Invalid UUID $url[0]");
                setHTTPStatus(404, $response);
                exit;
            }
            OrderService::getOrderInfo($url[0]);
            break;
        default:
            $response = new Response(404, "There is no such method as $method");
            setHTTPStatus(404, $response);
            break;
    }
}

?>