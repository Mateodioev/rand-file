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

}
