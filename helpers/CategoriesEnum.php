<?php
enum Categories: string {
    case Wok = "Wok";
    case Pizza = "Pizza";
    case Soup = "Soup";
    case Dessert = "Dessert";
    case Drink = "Drink";

    public static function checkCategories($param) {
        return match ($param) {
            'Wok' => self::Wok,
            'Pizza' => self::Pizza,
            'Soup' => self::Soup,
            'Dessert' => self::Dessert,
            'Drink' => self::Drink,
            default => false
        };
    }
}
?>