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
}
?>