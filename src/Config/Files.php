<?php

namespace App\Config;

// use App\Config\StringUtils;

class Files
{
    public static string $dir = './src/Files/';
    public static array $datas = [];

    /**
     * Abrir un archivo con file() y retornar un array
     *
     * @param string $type Archivo a abrir
     * @return array
     */
    public static function Open(string $type): array 
    {
        self::$datas = file(self::$dir.$type.'.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return self::$datas;
    }

    /**
     * Abrir un archivo con file() y retornar un string
     */
    public static function OpenUnique(string $type): string
    {
        return StringUtils::RandArray(self::Open($type));
    }

    /**
     * Guardar un string en una nueva linea de un .txt
     *
     * @param string $type Archivo a guardar
     */
    public static function Save(string $type, string $content, string $mode = 'a'): void
    {
        $archivo = fopen(self::$dir.$type.'.txt', $mode);
        fwrite($archivo, $content."\n");
		fclose($archivo);
    }

    /**
     * Obtener el número de lineas de un archivo
     */
    public static function Count(string $archivo): int
    {
        return count(self::Open($archivo));
    }
}