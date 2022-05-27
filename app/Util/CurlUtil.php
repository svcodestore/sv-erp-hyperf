<?php

declare(strict_types=1);

namespace App\Util;

class CurlUtil
{
    public static function get(string $url, array $headers = [])
    {
        $curl = curl_init();

        $opt = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ];

        if (!empty($headers)) {
            $opt[CURLOPT_HTTPHEADER] = $headers;
        }

        curl_setopt_array($curl, $opt);

        $response = curl_exec($curl);

        return $response;
    }

    public static function post(string $url, array $params = [], array $headers = [])
    {
        $curl = curl_init();

        $opt = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
        ];


        $opt[CURLOPT_HTTPHEADER] = $headers;

        if (!empty($params)) {
            if (!empty($headers)) {
                $headers[] = ['Content-Type: application/x-www-form-urlencoded'];
            } else {
                $headers = ['Content-Type: application/x-www-form-urlencoded'];
            }
            $opt[CURLOPT_POSTFIELDS] = http_build_query($params);
        }

        curl_setopt_array(
            $curl,
            $opt
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}
