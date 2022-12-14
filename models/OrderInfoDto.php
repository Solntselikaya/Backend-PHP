<?php

include_once 'models/BasicDto.php';
include_once 'models/DishBasketDto.php';

class OrderInfoDto extends BasicDto {
    protected $id;
    protected $deliveryTime;
    protected $orderTime;
    protected $status;
    protected float $price;

    public function __construct($info) {
        $this->id = $info['id'];
        $this->deliveryTime = $info['deliveryTime'];
        $this->orderTime = $info['orderTime'];
        $this->status = $info['status'];
        $this->price = $info['price'];
    }
}

?>