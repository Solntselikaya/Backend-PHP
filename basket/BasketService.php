<?php

include_once 'models/DishBasketDto.php';
include_once 'helpers/token.php';
include_once 'helpers/headers.php';
class BasketService {

    public static function getBasket() {
        $token = explode(' ', getallheaders()['Authorization'])[1];
        checkBearerToken($token);

        $userEmail = getEmailFromToken($token);
        $userId = $GLOBALS['dbLink']->query("SELECT id FROM users WHERE email = '$userEmail'")->fetch_assoc();

        $uId = $userId['id'];
        $basket = $GLOBALS['dbLink']->query("SELECT dishId, amount FROM basket WHERE userId = '$uId'")->fetch_all(MYSQLI_ASSOC);

        $basketDishes = array();
        foreach($basket as $key => $value) {
            $id = $value['dishId'];
            $info = $GLOBALS['dbLink']->query("SELECT id, name, price, image FROM dishes WHERE id = '$id'")->fetch_assoc();

            $dish = new DishBasketDto($info, $value['amount']);
            array_push($basketDishes, $dish->getContents());
        }

        echo json_encode($basketDishes);
        setHTTPStatus(200);
    }

    public static function addDish($dishId) {
        $token = explode(' ', getallheaders()['Authorization'])[1];
        checkBearerToken($token);

        $userEmail = getEmailFromToken($token);
        $userId = $GLOBALS['dbLink']->query("SELECT id FROM users WHERE email = '$userEmail'")->fetch_assoc();
        $uId = $userId['id'];

        $GLOBALS['dbLink']->query(
            "INSERT INTO basket (userId, dishId, amount) 
            VALUES ('$uId', '$dishId', 1)
            ON DUPLICATE KEY UPDATE amount = amount + 1"
        );

        $response = new Response(null, "Succesfully added dish with id: '$dishId' to basket");
        setHTTPStatus(200, $response);
    }

    public static function decreaseDish($dishId) {
        $token = explode(' ', getallheaders()['Authorization'])[1];
        checkBearerToken($token);

        $userEmail = getEmailFromToken($token);
        $userId = $GLOBALS['dbLink']->query("SELECT id FROM users WHERE email = '$userEmail'")->fetch_assoc();
        $uId = $userId['id'];

        $GLOBALS['dbLink']->query(
            "UPDATE basket 
            SET amount = amount - 1 
            WHERE userId = '$uId' AND dishId = '$dishId'"
        );

        $GLOBALS['dbLink']->query("DELETE FROM basket WHERE amount = 0");

        $response = new Response(null, "Succesfully decreased number of dishes with id: '$dishId' in basket");
        setHTTPStatus(200, $response);
    }

    public static function deleteWholeDish($dishId) {
        $token = explode(' ', getallheaders()['Authorization'])[1];
        checkBearerToken($token);

        $userEmail = getEmailFromToken($token);
        $userId = $GLOBALS['dbLink']->query("SELECT id FROM users WHERE email = '$userEmail'")->fetch_assoc();
        $uId = $userId['id'];

        $GLOBALS['dbLink']->query("DELETE FROM basket WHERE userId = '$uId' AND dishId = '$dishId'");

        $response = new Response(null, "Succesfully deleted dishes with id: '$dishId' from basket");
        setHTTPStatus(200, $response);
    }

}

?>