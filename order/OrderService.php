<?php

include_once 'helpers/token.php';
include_once 'helpers/headers.php';
include_once 'models/OrderDto.php';
include_once 'models/OrderInfoDto.php';
include_once 'models/OrderCreateModel.php';
include_once 'helpers/UUID.php';

class OrderService {

    public static function getOrderInfo($orderId) {

        $orderInfo = $GLOBALS['dbLink']->query(
            "SELECT * FROM orders WHERE id = '$orderId'")->fetch_assoc();

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

    }
}

?>