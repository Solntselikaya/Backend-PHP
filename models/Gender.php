<?php
enum Gender: string {
    case Male = "Male";
    case Female = "Female";

    public static function checkGender($gender) {
        return match ($gender) {
            'Male' => self::Male,
            'Female' => self::Female,
            default => false
        };
    }
}
?>