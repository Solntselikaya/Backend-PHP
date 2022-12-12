<?php

include_once 'models/BasicDto.php';

class DishDto extends BasicDto {
    protected $id;
    protected $name;
    protected $description;
    protected float $price;
    protected $image;
    protected bool $vegetarian;
    protected float $rating;
    protected $category;

    public function __construct($info) {
        $this->id = $info['id'];
        $this->name = $info['name'];
        $this->description = isset($info['description']) ? $info['description'] : null;
        $this->price = $info['price'];
        $this->image = isset($info['image']) ? $info['image'] : null;
        $this->vegetarian = $info['vegetarian'];
        $this->rating = isset($info['rating']) ? $info['rating'] : null;
        $this->category = $info['category'];
    }
}

?>