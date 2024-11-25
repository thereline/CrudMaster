<?php

namespace Thereline\CrudMaster;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class CrudMaster
{
    protected static bool $useValidation = true;

    protected static bool $useTranslation = true;

    public function __construct()
    {
        self::$useValidation = Config::get('crudmaster.useValidation', true);
        self::$useTranslation = Config::get('crudmaster.useTranslation', true);
    }

    // Helper function to check if an array is associative
    public static function isAssocArray(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function IsWithValidation(): bool
    {
        return self::$useValidation;
    }

    public static function IsWithTranslation(): bool
    {
        return self::$useTranslation;
    }

    public static function stringToPascalCase($string): string
    {
        // First, replace non-alphanumeric characters with spaces (for hyphen or underscore cases)
        $string = preg_replace('/[^a-zA-Z0-9]+/', ' ', $string);

        // Convert the string to camelCase
        $string1 = Str::camel($string);
        $string2 = Str::studly($string1);

        // Capitalize the first letter (for PascalCase)
        return $string2;
    }
}
