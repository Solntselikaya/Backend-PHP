<?php

include_once 'helpers/headers.php';
include_once 'models/Response.php';

class OrderCreateModel {
    private $deliveryTime;
    private $address;
    private $orderTime;

    public function __construct($data) {
        $this->setDeliveryTime($data->deliveryTime);
        $this->address = $data->address;
    }

    private function setDeliveryTime($time) {
        $timeList = explode("T", $time);
        $date = $timeList[0];
        $time = $timeList[1];
        $time = substr($time, 0, strpos($time, "."));

        $deliveryTime = $date . " " . $time;

        $deliveryTime = DateTime::createFromFormat('Y-m-d H:i:s', $deliveryTime);
        if (!$deliveryTime) {
            $response = new Response(400, "Invalid delivery time");
            setHTTPStatus(400, $response);
            exit;
        }

        $orderTime = new DateTime();
        $minDeliveryTime = new DateTime();
        $interval = new DateInterval('PT30M');
        $minDeliveryTime->add($interval);

        if ($deliveryTime < $minDeliveryTime) {
            $response = new Response(400, "Delivery time should be at least 30 minutes longer that the order time");
            setHTTPStatus(400, $response);
            exit;
        }

        $this->deliveryTime = $deliveryTime->format('Y-m-d H:i');
        $this->orderTime = $orderTime->format('Y-m-d H:i');
    }

    public function create() {

        $userId = getUserIdFromToken();
        $basket = $GLOBALS['dbLink']->query(
            "SELECT dishId, amount FROM basket WHERE userId = '$userId'"
        )->fetch_all(MYSQLI_ASSOC);

        if (empty($basket)) {
            $response = new Response(400, "Basket is empty");
            setHTTPStatus(400, $response);
            exit;
        }
        
        $orderUUID = (new UUID)->getUUID();
        $GLOBALS['dbLink']->query(
            "INSERT INTO orders (id, userId, deliveryTime, orderTime, address)
            VALUES (
                '$orderUUID',
                '$userId',
                '$this->deliveryTime',
                '$this->orderTime',
                '$this->address'
            )"
        );
        
        $totalPrice = 0;
        foreach($basket as $key => $value) {
            $dishId = $value['dishId'];
            $price = $GLOBALS['dbLink']->query(
                "SELECT price FROM dishes WHERE id = '$dishId'"
            )->fetch_assoc();

            $dishAmount = intval($value['amount']);
            $totalPrice += intval($price['price']) * $dishAmount;

            $GLOBALS['dbLink']->query(
                "INSERT INTO orderContents (orderId, dishId, amount)
                VALUES (
                    '$orderUUID',
                    '$dishId',
                    '$dishAmount'
                )"
            );
        }

        $GLOBALS['dbLink']->query(
            "UPDATE orders SET price = '$totalPrice' WHERE id = '$orderUUID'"
        );

        $GLOBALS['dbLink']->query(
            "DELETE FROM basket WHERE userId = '$userId'"
        );

        $response = new Response(null, "Order succesfully created");
        setHTTPStatus(200, $response);
    }
}

?>