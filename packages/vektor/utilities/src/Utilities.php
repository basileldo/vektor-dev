<?php

namespace Vektor\Utilities;

class Utilities
{
    public static function flattenProperties($input, $prefix = '')
    {
        $output = [];
        $sep = '.';

        if (!is_array($input)) $input = (array) $input;

        foreach($input as $property => $value) {
            $_key = ltrim($prefix . $sep . $property, $sep);

            if (is_array($value) || is_object($value)) {
                $output = array_merge($output, self::flattenProperties($value, $_key));
            } else {
                $output[$_key] = $value;
            }
        }

        return $output;
    }

    public static function getNestedValue($input, $property)
    {
        if (!is_array($input)) $input = (array) $input;
        if (array_key_exists($property, $input)) {
            return $input[$property];
        } else {
            return null;
        }
    }

    public static function getNestedFlattenedValue($input, $property)
    {
        $input = self::flattenProperties($input);
        if (array_key_exists($property, $input)) {
            return $input[$property];
        } else {
            return null;
        }
    }
}
