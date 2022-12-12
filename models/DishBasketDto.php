<?php
include_once 'models/BasicDto.php';
class DishBasketDto extends BasicDto {
    protected $id;
    protected $name;
    protected int $price;
    protected $totalPrice;
    protected int $amount;
    protected $image;

    public function __construct($info, $amount) {
        $this->id = $info['id'];
        $this->name = $info['name'];
        $this->price = $info['price'];
        $this->amount = $amount;
        $this->totalPrice = $this->price * $this->amount;
        $this->image = $info['image'];
    }
}
?>