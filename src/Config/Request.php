<?php

namespace App\Config;

/**
 * Crear una request basica a cualquier url
 */
class Request
{

    private static $ch;

    /**
     * Crear una petición a una url
     */
    private static function Create(string $url, string $method = 'GET', ?array $headers = null, $data = null): array
    {

        self::$ch = curl_init($url);

        curl_setopt_array(self::$ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);
        if ($headers != null) {
            curl_setopt(self::$ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($data != null) {
            curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec(self::$ch);
        $info = curl_getinfo(self::$ch);
        curl_close(self::$ch);

        return [
            'info' => $info,
            'code' => $info['http_code'],
            'response' => $response,
        ];

    }

    public static function __callStatic($method, $settings)
    {
        return self::Create(@$settings[0], strtoupper($method), @$settings[1], @$settings[2]);
    }
}
