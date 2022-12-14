<?php

include_once 'models/BasicDto.php';
include_once 'models/DishBasketDto.php';

class OrderDto extends BasicDto {
    protected $id;
    protected $deliveryTime;
    protected $orderTime;
    protected $status;
    protected float $price;
    protected  $dishes = array();
    protected $address;

    public function __construct($info) {
        $this->id = $info['id'];
        $this->deliveryTime = $info['deliveryTime'];
        $this->orderTime = $info['orderTime'];
        $this->price = $info['price'];
        $this->getDishes();
        $this->address = $info['address'];
    }

    private function getDishes() {
        $dishes = $GLOBALS['dbLink']->query(
            "SELECT * FROM orderContents WHERE orderId = '$this->id'"
        )->fetch_all(MYSQLI_ASSOC);

        foreach($dishes as $key => $value) {
            $currDishId = $value['dishId'];
            $currDishAmount = $value['amount'];
            $currDishInfo = $GLOBALS['dbLink']->query("SELECT id, name, price, image FROM dishes WHERE id = '$currDishId'")->fetch_assoc();
            array_push($this->dishes, (new DishBasketDto($currDishInfo, $currDishAmount))->getContents());
        }
    }
}

?>