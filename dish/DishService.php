<?php

include_once 'models/DishPagedListDto.php';
include_once 'helpers/headers.php';
include_once 'models/Response.php';
class DishService {
    public static function getDishes($params) {
        $dishList = new DishPagedListDto($params);
        $dishList->echoContents();
    }

    public static function getDishInfo($id) {
        $dishInfo = $GLOBALS['dbLink']->query(
            "SELECT * 
            FROM dishes 
            WHERE id = '$id'"
            )->fetch_assoc();

        if (empty($dishInfo)) {
            $response = new Response(404, "There is no dish with such id");
            setHTTPStatus(404, $response);
            exit;
        }

        $dish = new DishDto($dishInfo);
        $dish->echoContents();
    }

    public static function isSettingDishRatingAvaliable($dishId) {
        $userId = getUserIdFromToken();

        $isDishOrdered = $GLOBALS['dbLink']->query(
            "SELECT dishId FROM userOrderedDishes WHERE userId = '$userId'"
        )->num_rows;

        if (!$isDishOrdered) {
            echo json_encode(false);
        }
        else {
            echo json_encode(true);
        }

        setHTTPStatus(200);
    }

    public static function setDishRating($dishId, $ratingScore) {
        $userId = getUserIdFromToken();

        if(empty($ratingScore)) {
            $response = new Response(400, "Rating score not assigned");
            setHTTPStatus(400, $response);
            exit;
        }

        $isDishOrdered = $GLOBALS['dbLink']->query(
            "SELECT dishId FROM userOrderedDishes WHERE userId = '$userId'"
        )->num_rows;

        if (!$isDishOrdered) {
            $response = new Response(403, "User with id: '$userId' haven't ordered dish with id: '$dishId'");
            setHTTPStatus(403, $response);
            exit;
        }

        $ratingScore = intval($ratingScore);
        $GLOBALS['dbLink']->query(
            "INSERT INTO dishesRating (userId, dishId, rating)
            VALUES (
                '$userId',
                '$dishId',
                '$ratingScore'
            )
            ON DUPLICATE KEY UPDATE rating = '$ratingScore'"
        );

        $response = new Response(null, "Rating succesfully posted");
        setHTTPStatus(200, $response);
    }
}
?>