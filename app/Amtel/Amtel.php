<?php

namespace App\Amtel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use App\Amtel\Firms;
use App\Amtel\Models;
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

    static private function sanitaryze($str)
    {
        $str = str_replace(' ', '_', $str);
        $str = str_replace(',', '-', $str);
        $str = str_replace('[', '', $str);
        $str = str_replace(']', '', $str);
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);
        return $str;
    }

    /*
    * artisan command - fill all firms
    */
    static public function fillFirms()
    {
        $params = [
            'vehicle_model_types' => [0, 1, 2],
            'avail_only' => false
        ];

        $res = json_decode(Amtel::get('/v2/company', $params));
        Log::info('fillFirms res =' . print_r($res, 1));

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
        /*$firm = Firms::where('title', mb_strtolower($title))
            ->first();

        if ($firm === null) {
            Log::info('renew firm');
            self::fillFirms();
        }*/

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
            Log::info('GET from cache: ' . $key);
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

                Log::info('GET: ' . self::URI . $method . ', params=' . json_encode($params) . '. result 200');

                return $content;
            } else {
                Log::error(new \Exception('amtel GET ' . self::URI . $method . ', params=' . json_encode($params) . '. result ' . $res->getStatusCode()));
                throw new \Symfony\Component\HttpKernel\Exception\HttpException('bad request ' . $res->getStatusCode());
            }
        }
    }

    /*
    * artisan command - fill all models
    */
    static public function fillModels()
    {
        $params = [
            'vehicle_model_types' => [0, 1, 2],
            'avail_only' => false
        ];

        $firms = Firms::all();
        //$firms = Firms::take(6)->get(); // audi & bmw
        foreach ($firms as $firm) {
            $models = Models::where('firm', $firm->id)->get();
            if (count($models)) {
                //echo "skip '" . $firm->title . "'\n";
                continue;
            }

            echo "take '" . $firm->title . "'\n";

            $params = [
                'company_id' => $firm->id,
                'model_types' => [0, 1, 2],
                'avail_only' => false
            ];

            try {
                $models = json_decode(Amtel::get('/company/models', $params));
            } catch (\Exception $e) {
                echo "error load '" . $firm->title . "':" . $e->getMessage();
                continue;
            }

            foreach ($models->model_list as $el) {
                $model = Models::firstOrNew(['id' => $el->model_id]);
                $id = $el->model_id;
                $model->id = $el->model_id;
                $model->firm = $firm->id;
                $model->title = mb_strtolower(str_replace('/', '-', $el->model_name));
                $model->start = $el->model_year_start == null ? '' : $el->model_year_start;
                $model->end = $el->model_year_end == null ? '' : $el->model_year_end;
                $model->url = mb_strtolower(self::sanitaryze($model->title . '_' . $model->start . '-' . $model->end));
                $model->group = mb_strtolower(explode('_', explode(' ', explode('/', explode('(', $el->model_name)[0])[0])[0])[0]);

                try {
                    $model->img = $models->model_image_list->$id[0]->url;
                } catch (\Exception $e) {
                }

                $model->save();
            }

            //echo "sleep\n";
            sleep(1);
        }
    }

    static public function getModelGroups($firm)
    {
        $modelGroups = Models::where('firm', Amtel::getFirm($firm)->id)
            ->orderBy('group', 'asc')
            ->orderBy('title', 'desc')
            ->get();

        $groups = [];
        $oldGroup = '';
        foreach ($modelGroups as $model) {
            if ($model->group != $oldGroup) {
                $groups[] = [
                    'title' => $model->group,
                    'img' => $model->img
                ];
                $oldGroup = $model->group;
            }
        }

        //Log::info('groups=' . print_r($groups, 1));
        return $groups;
    }

    static public function getModels($firm, $groupModels)
    {
        $firmId = Amtel::getFirm($firm)->id;
        $modelGroups = Models::where('firm', $firmId)
            ->where('group', $groupModels)
            ->orderBy('title', 'desc')
            ->get();

        $groups = [];
        foreach ($modelGroups as $model) {
            $groups[] = [
                'id' => $model->id,
                'firm' => $firmId,
                'title' => $model->title,
                'url' => $model->url,
                'start' => $model->start,
                'end' => $model->end,
                'image' => $model->img_local != '' ? $model->img_local : $model->img,
            ];
        }

        //Log::info('groups=' . print_r($groups, 1));
        return $groups;
    }
}
