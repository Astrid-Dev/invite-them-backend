<?php
namespace App\Traits;

trait BaseEnum
{
    public static function values(): array
    {
        $data = array();
        foreach (self::cases() as $case) {
            $data[] = $case->value;
        }

        return $data;
    }
}
