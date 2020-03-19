<?php

namespace App\Amtel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use App\Amtel\Firms;
use Log;

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
    * get cache status for key
    * @param (string) key
    * @return (boolean)
    */
    static public function hasCache($key)
    {
        if (getenv('AMTEL_CACHE') == 'redis') {
            return Cache::store('redis')->has($key);
        } else {
            return false;
        }
    }

    /*
    * fill all firms
    */
    static public function fillFirms()
    {
        $params = [
            'vehicle_model_types' => [0, 1, 2],
            'avail_only' => false
        ];

        $res = json_decode(Amtel::get('/v2/company', $params));
        foreach ($res->companies as $firmInfo) {
            $firm = Firms::firstOrNew(['title' => $firmInfo->company_name]);

            $firm->title = mb_strtolower($firmInfo->company_name);
            $firm->id = $firmInfo->company_id;
            //dd($firm);
            $firm->save();
        }
    }

    /*
    * get firm id by title. if not fount id database - get request to Amtel and add firm to base
    * @params title
    * @return id
    */
    static public function getFirm($title)
    {
        $firm = Firms::where('title', mb_strtolower($title))
            ->first();

        if ($firm === null) {
            echo "renew all\n";
            self::fillFirms();
        }

        return Firms::where('title', mb_strtolower($title))
            ->firstOrFail();
    }

    /*
    * get cache status for key
    * @param (string) key
    */
    static public function getCache($key)
    {
        if (getenv('AMTEL_CACHE') == 'redis') {
            return Cache::store('redis')->get($key);
        }
    }

    /*
    * put cache status for key
    * @param (string) key
    * @param (string) data
    */
    static public function putCache($key, $data)
    {
        if (getenv('AMTEL_CACHE') == 'redis') {
            Cache::store('redis')->put($key, $data, getenv('AMTEL_CACHE_SEC'));
        }
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

        if (self::hasCache($key)) {
            //echo "\nhas\n";
            return self::getCache($key);
        } else {
            $client = new Client;
            $signedParams = ['form_params' => [
                'id' => 1,
                'user_id' => getenv('AMTEL_USER_ID'),
                'request' => self::request($params)
            ]];
            $res = $client->get(self::URI . $method, $signedParams);

            if ($res->getStatusCode() == 200) {
                $content = $res->getBody()->getContents();

                self::putCache($key, $content);
                //echo "\nadd new\n";
                //echo "\n" . self::getCache($key) . "\n";

                Log::info('amtel GET ' . self::URI . $method . ', params=' . json_encode($params) . '. result 200');

                return $content;
            } else {
                Log::error(new \Exception('amtel GET ' . self::URI . $method . ', params=' . json_encode($params) . '. result ' . $res->getStatusCode()));
                throw new \Symfony\Component\HttpKernel\Exception\HttpException('bad request ' . $res->getStatusCode());
            }
        }
    }
}
