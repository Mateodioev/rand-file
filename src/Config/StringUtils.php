<?php

namespace App\Config;

class StringUtils {
    
    /**
     * Eliminar caracteres markdown
     */
    final public static function QuitMarkdown(?string $string, string $replace = ''): ?string
    {
        $mark = ['*', '_', '__', '~', '||', '[', ']', '(', ')', '```', '`'];
        return @str_replace($mark, $replace, $string);
    }

    /**
     * Obtener un elemento aleatorio de un array
     */
    public static function RandArray(array $arr)
    {
        return $arr[array_rand($arr)];
    }

    /**
     * Comparar si existe una cadena en un array
     */
    public static function Compare(array $data, string $id): bool
    {
        return in_array($id, $data);
    }
}
