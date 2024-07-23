<?php

declare(strict_types=1);

namespace JtlConnector;

class Types
{
    /** @var string[] */
    public static array $allowedTypes = [
        'array',
        'bool',
        'float',
        'int',
        'mixed',
        'object',
        'string',
        'resource',
        'callable',
    ];

    /**
     * Returns a valid variable type for param/var tags.
     *
     * If type is not one of the standard types, it must be a custom type.
     * Returns the correct type name suggestion if type name is invalid.
     *
     * @param string $varType The variable type to process.
     *
     * @return string
     */
    public static function suggestType(string $varType): string
    {
        if ($varType === '') {
            return '';
        }

        if (in_array($varType, self::$allowedTypes, true) === true) {
            return $varType;
        } else {
            $lowerVarType = strtolower($varType);
            switch ($lowerVarType) {
                case 'bool':
                case 'boolean':
                    return 'bool';
                case 'double':
                case 'real':
                case 'float':
                    return 'float';
                case 'int':
                case 'integer':
                    return 'int';
                case 'array()':
                case 'array':
                    return 'array';
            }//end switch

            if (str_contains($lowerVarType, 'array(')) {
                // Valid array declaration:
                // array, array(type), array(type1 => type2).
                $matches = [];
                $pattern = '/^array\(\s*([^\s^=^>]*)(\s*=>\s*(.*))?\s*\)/i';
                if (preg_match($pattern, $varType, $matches) !== 0) {
                    $type1 = '';
                    if (isset($matches[1]) === true) {
                        $type1 = $matches[1];
                    }

                    $type2 = '';
                    if (isset($matches[3]) === true) {
                        $type2 = $matches[3];
                    }

                    $type1 = self::suggestType($type1);
                    $type2 = self::suggestType($type2);
                    if ($type2 !== '') {
                        $type2 = ' => '.$type2;
                    }

                    return "array($type1$type2)";
                } else {
                    return 'array';
                }//end if
            } else if (in_array($lowerVarType, self::$allowedTypes, true) === true) {
                // A valid type, but not lower cased.
                return $lowerVarType;
            } else {
                // Must be a custom type name.
                return $varType;
            }//end if
        }//end if

    }//end suggestType()
}