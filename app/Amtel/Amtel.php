<?php

namespace App\Amtel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;

class Amtel extends Model
{

    private const URI = 'https://ecore-reseller.euroauto.ru';

    /**
     * get request string with signature
     * 
     * @param (string) params = array of params
     * @return (string) request
     */
    static public function request($params)
    {
        // Create token header as a JSON string
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);

        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $payload = json_encode($params);

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, getenv('AMTEL_SECRET'), true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Create JWT
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /*
    * run GET method
    * @param (string) method, e.g. '/v2/company'
    * @param params = array of params
    * @return 
    */
    static public function get($method, $params)
    {
        $key = $method . ':' . json_encode($params);
        $key = str_replace('"', '\'', $key);
        //echo "\n" . $key . "\n";

        if (Cache::store('redis')->has($key)) {
            //echo "\nhas\n";
            return Cache::store('redis')->get($key);
        } else {
            $client = new Client;
            $res = $client->get(self::URI . $method, ['form_params' => [
                'id' => 1,
                'user_id' => getenv('AMTEL_USER_ID'),
                'request' => self::request($params)
            ]]);

            if ($res->getStatusCode() == 200) {
                $content = $res->getBody()->getContents();

                Cache::store('redis')->put($key, $content, getenv('AMTEL_CACHE_SEC'));
                //echo "\nadd new\n";
                //echo "\n" . Cache::store('redis')->get($key) . "\n";

                return $content;
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\HttpException('bad request ' . $res->getStatusCode());
            }
        }
    }
}
