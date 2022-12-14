<?php

include_once 'helpers/token.php';
include_once 'helpers/headers.php';
include_once 'models/OrderDto.php';
include_once 'models/OrderInfoDto.php';
include_once 'models/OrderCreateModel.php';
include_once 'models/Response.php';
include_once 'helpers/UUID.php';

class OrderService {

    public static function getOrderInfo($orderId) {

        $orderInfo = $GLOBALS['dbLink']->query(
            "SELECT * FROM orders WHERE id = '$orderId'"
        )->fetch_assoc();
            
        if (empty($orderInfo)) {
            $response = new Response(404, "No such order in database");
            setHTTPStatus(404, $response);
            exit;
        }
            
        $userId = getUserIdFromToken();
        if ($orderInfo['userId'] != $userId) {
            $response = new Response(403, "Access denied");
            setHTTPStatus(403, $response);
            exit;
        }

        $order = new OrderDto($orderInfo);
        $order->echoContents();

        setHTTPStatus(200);
    }

    public static function getListOfOrders() {

        $userId = getUserIdFromToken();
        $listOfOrders = $GLOBALS['dbLink']->query(
            "SELECT * FROM orders WHERE userId = '$userId'"
        )->fetch_all(MYSQLI_ASSOC);

        foreach($listOfOrders as $key => $value) {
            $orderInfo = new OrderInfoDto($value);
            $orderInfo->echoContents();
        }

        setHTTPStatus(200);
    }

    public static function createOrder($data) {
        $order = new OrderCreateModel($data->body);
        $order->create();
    }

    public static function confirmOrderDelivery($orderId) {

        $orderInfo = $GLOBALS['dbLink']->query(
            "SELECT userId FROM orders WHERE id = '$orderId'"
        )->fetch_assoc();

        if (empty($orderInfo)) {
            $response = new Response(404, "No such order in database");
            setHTTPStatus(404, $response);
            exit;
        }

        $userId = getUserIdFromToken();
        if ($orderInfo['userId'] != $userId) {
            $response = new Response(403, "Access denied");
            setHTTPStatus(403, $response);
            exit;
        }
        
        $GLOBALS['dbLink']->query(
            "UPDATE orders SET status = 'Delivered' WHERE id = '$orderId'"
        );

        $orderContents = $GLOBALS['dbLink']->query(
            "SELECT dishId, amount FROM orderContents WHERE orderId = '$orderId'"
        )->fetch_all(MYSQLI_ASSOC);
            
        $userId = getUserIdFromToken();
        foreach($orderContents as $key => $value) {
            $dishId = $value['dishId'];

            $GLOBALS['dbLink']->query(
                "INSERT INTO userOrderedDishes (userId, dishId)
                VALUES (
                    '$userId',
                    '$dishId'
                )
                ON DUPLICATE KEY UPDATE userId = userId"
            );
        }

        $response = new Response(null, "Delivery succesfully confirmed");
        setHTTPStatus(200, $response);
    }
}

?>