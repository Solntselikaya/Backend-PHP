<?php
enum Sorting: string {
    case NameAsc = "name ASC";
    case NameDesc = "name DESC";
    case PriceAsc = "price ASC";
    case PriceDesc = "price DESC";
    case RatingAsc = "rating ASC";
    case RatingDesc = "rating DESC";

    public static function checkSorting($param) {
        return match ($param) {
            'NameAsc' => self::NameAsc,
            'NameDesc' => self::NameDesc,
            'PriceAsc' => self::PriceAsc,
            'PriceDesc' => self::PriceDesc,
            'RatingAsc' => self::RatingAsc,
            'RatingDesc' => self::RatingDesc,
            default => false
        };
    }
}
?>