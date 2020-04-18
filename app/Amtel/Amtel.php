<?php

namespace App\Amtel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Amtel\Firms;
use App\Amtel\Models;
use GuzzleHttp\Psr7\Request;
use \Symfony\Component\HttpKernel\Exception\HttpException;
use function GuzzleHttp\json_encode;

/*
    работает только с 1 корзиной - getenv('AMTEL_BASKET_NUM', 1)
    получаем товары и отправляем в заказ только из нее
*/

class Amtel extends Model
{

    private const URI = 'https://ecore-reseller.euroauto.ru';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * get request string with signature
     * 
     * @param (string) params = array of params
     * @return (string) request
     */
    static public function getRequestString($params)
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

        $res = json_decode(self::get('/v2/company', $params));
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
    * @param String $method, e.g. '/v2/company'
    * @param Array params = array of params
    * @param Bool cache - enable cache result and get from cache saved data
    */
    static public function get($method, $params, $cache = true)
    {
        $key = $method . ':' . json_encode($params);
        $key = str_replace('"', '\'', $key);
        //echo "\n" . $key . "\n";

        if ($cache && self::hasCache($key)) {
            Log::info('GET from cache: ' . $key);
            return self::getCache($key);
        } else {
            $client = new Client;
            $signedParams = ['form_params' => [
                'id' => 1,
                'user_id' => getenv('AMTEL_USER_ID'),
                'request' => self::getRequestString($params)
            ]];
            Log::info('GET: ' . self::URI . $method . ', params=' . json_encode($params));
            $res = $client->get(self::URI . $method, $signedParams);

            if ($res->getStatusCode() == 200) {
                $content = $res->getBody()->getContents();
                if ($cache) {
                    Log::info('result 200, add to cache: ' . $key);
                    self::putCache($key, $content);
                } else {
                    Log::info('result 200');
                }

                return $content;
            } else {
                Log::error('GET: ' . self::URI . $method . ', params=' . json_encode($params) . '. result: ' . $res->getStatusCode());
                //Log::error(new \Exception('amtel GET ' . self::URI . $method . ', params=' . json_encode($params) . '. result ' . $res->getStatusCode()));
                throw new HttpException('bad request ' . $res->getStatusCode());
            }
        }
    }

    /*
    * run POST method
    * @param (string) method, e.g. '/v2/company'
    * @param params = array of params
    * @return 
    */
    static public function post($method, $params)
    {
        $client = new Client;
        $signedParams = ['form_params' => [
            'id' => 1,
            'user_id' => getenv('AMTEL_USER_ID'),
            'request' => self::getRequestString($params)
        ]];
        Log::info('POST: ' . self::URI . $method . ', params=' . json_encode($params));
        $res = $client->post(self::URI . $method, $signedParams);

        if ($res->getStatusCode() == 200) {
            Log::info('result 200');
            $content = $res->getBody()->getContents();

            return $content;
        } else {
            Log::error('GET: ' . self::URI . $method . ', params=' . json_encode($params) . '. result: ' . $res->getStatusCode());
            //Log::error(new \Exception('amtel GET ' . self::URI . $method . ', params=' . json_encode($params) . '. result ' . $res->getStatusCode()));
            throw new HttpException('bad request ' . $res->getStatusCode());
        }
    }

    /*
    * run DELETE method
    * @param (string) method, e.g. '/v2/company'
    * @param params = array of params
    * @return 
    */
    static public function del($method, $params)
    {
        $client = new Client;
        $signedParams = ['form_params' => [
            'id' => 1,
            'user_id' => getenv('AMTEL_USER_ID'),
            'request' => self::getRequestString($params)
        ]];
        Log::info('DELETE: ' . self::URI . $method . ', params=' . json_encode($params));
        $res = $client->delete(self::URI . $method, $signedParams);

        if ($res->getStatusCode() == 200) {
            Log::info('result 200');
            $content = $res->getBody()->getContents();

            return $content;
        } else {
            Log::error('GET: ' . self::URI . $method . ', params=' . json_encode($params) . '. result: ' . $res->getStatusCode());
            //Log::error(new \Exception('amtel GET ' . self::URI . $method . ', params=' . json_encode($params) . '. result ' . $res->getStatusCode()));
            throw new HttpException('bad request ' . $res->getStatusCode());
        }
    }

    /*
    * artisan command - fill all models
    */
    static public function fillModels()
    {
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
                $models = json_decode(self::get('/company/models', $params));
            } catch (\Exception $e) {
                Log::error('amtel fillModels("' . $firm->title . '") error: ' . $e->getMessage());
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
        $modelGroups = Models::where('firm', self::getFirm($firm)->id)
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
        $firmId = self::getFirm($firm)->id;
        $modelGroups = Models::where('firm', $firmId)
            ->where('group', $groupModels)
            ->orderBy('title', 'asc')
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

    static public function getModelByUrl($modelUrl)
    {
        //Log::info('getModelByUrl=' . $modelUrl . ", decode=" . urldecode($modelUrl) . ", encode=" . urlencode($modelUrl));
        return Models::where('url', $modelUrl)
            ->firstOrFail();
    }

    static public function getGoodsList($modelId)
    {
        $params = [
            'model_id' => $modelId,
            'avail_only' => true
        ];

        try {
            $res = json_decode(self::get('/goods/names', $params), true);
            Log::info('getGoodsList=' . print_r($res, 1));

            return $res;
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(404, 'Model with id None not found');
        }
    }

    /*
        get all goods from supplier as is
    */
    static public function getGoodsAll($modelId, $goodId)
    {
        $params = [
            'model_id' => $modelId,
            'goods_name_id' => $goodId
        ];

        try {
            $res = json_decode(self::get('/goods/by_model', $params), true);
            Log::info('getGoodsAll=' . print_r($res, 1));

            return $res;
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(404, 'Model with id None not found');
        }
    }

    /*
        get goods for frontend

     * @param (string) modelId = id models
     * @param (string) goodId = id goods
     * @param (float) priceMul = multiplexer for client price price
    */
    static public function getGoods($modelId, $goodId, $priceMul = 1)
    {
        try {
            $res = self::getGoodsAll($modelId, $goodId);

            return self::explodeGoods($res, $priceMul);
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        get goods for frontend as linear array

     * @param (array) list - list goods
     * @param (float) priceMul = multiplexer for client price price
    */
    static private function explodeGoods($list, $priceMul = 1)
    {
        Log::info('explodeGoods goods=' . print_r($list, 1));

        $goods = [];
        if ($res['result'] = true) {
            foreach ($list['avail_sh'] as $avail) {
                $goodsInfo = $list['goods_list'][$avail['goods_id']] ?? [
                    'company_id' => '',
                    'company_name' => '',
                    'num' => ''
                ];

                if ($goodsInfo['company_name'] == '' || $goodsInfo['num'] == '') {
                    continue;
                }

                $goodsNameList = $list['goods_name_list'][$avail['goods_name_id']] ?? [
                    'goods_name_long_ru' => 'деталь',
                    'goods_name_short_ru' => 'деталь'
                ];

                $goods[$avail['goods_internal_id']] = [
                    'id' => $avail['goods_internal_id'],
                    'goods_id' => $goodsInfo['goods_id'],
                    //'model_id' => $modelId,
                    'company_id' => $goodsInfo['company_id'],
                    'goods_supplier_sh_id' => $avail['goods_supplier_sh_id'],
                    'supplier_point_id' => $avail['supplier_point_id'],
                    'company_name' => $goodsInfo['company_name'],
                    'num' => $goodsInfo['num'],
                    'comment' => $avail['comment'],
                    'avail' => $avail['count_avail'],
                    'wearout' => $avail['wearout'],
                    'price' => $avail['price_reseller'] * $priceMul,
                    'name_long' => $goodsNameList['goods_name_long_ru'],
                    'name_short' => $goodsNameList['goods_name_short_ru'],
                    'supplier' => $list['supplier_point_list'][$avail['supplier_point_id']] ?? [],
                    'img' => $list['image_sh_list'][$avail['goods_supplier_sh_id']] ?? []
                ];
            }
        }

        return $goods;
    }

    /*
        get all goods by num(article)
    */
    static public function getGoodsByNum($num, $priceMul = 1)
    {
        $params = [
            'num' => $num,
            'use_cross_references' => true, // Возвращать ли аналоги товара в результате поиска
            'use_search_form' => true, // Использовать поисковую форму номера (без спецсимволов)
            //'company_id' => // int Идентификатор компании-производителя товара
            //'goods_name_id_list' => // int[] Список наименований товаров
        ];

        try {
            $res = json_decode(self::get('/goods/avail_by_num', $params), true);
            Log::info('getGoodsByNum=' . print_r($res, 1));

            return self::explodeGoods($res, $priceMul);
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        get all baskets and goods in baskets
    */
    static public function getBasket()
    {
        $params = [
            //'session_id ' => // int Уникальный идентификатор сессии клиента, может использоваться, если клиент еще не авторизован и user_data_id неизвестен
            'user_data_id ' => (int) getenv('AMTEL_USER_DATA_ID'), // (int) – Идентификатор клиента для которого делается заказ (отличается от user_id для доступа к API, выдается вместе с остальными учетными данными при подключении)
            //'all' => true, // Получить корзины всех клиентов
            'basket_num_list' => [(int) getenv('AMTEL_BASKET_NUM', 1)] //[int] Список идентификаторов корзин
        ];

        try {
            $res = json_decode(self::get('/basket', $params, false), true);
            Log::info('get basket=' . print_r($res, 1));

            return $res;
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        create basket and add goods in basket
    */
    static public function add2Basket($goods_id, $goods_supplier_sh_id, $supplier_point_id, $count)
    {
        $params = [
            'user_data_id' => (int) getenv('AMTEL_USER_DATA_ID'), // (int) * – Идентификатор клиента для которого делается заказ (отличается от user_id для доступа к API, выдается вместе с остальными учетными данными при подключении)
            'basket_num' => (int) getenv('AMTEL_BASKET_NUM', 1), //(int) * – Номер корзины, указанный реселлером, у клиента может быть несколько корзин с разными числовыми идентификаторами уникальными для данного клиента
            'goods_id' =>  $goods_id, // (int) * – Идентификатор нового товара (необязателен, если указан goods_supplier_sh_id).
            'goods_supplier_sh_id' => $goods_supplier_sh_id, //(int) * – Идентификатор б/у товара (обязателен только для б/у товаров).
            'supplier_point_id' => $supplier_point_id, // (int) * – Идентификатор точки выдачи поставщика.
            'count' => $count, // (int) * – Количество единиц товара.
            //'price' => $price, // (float) – Стоимость единицы товара, может быть большей, например для отображения реселлером своему клиенту. Если не указано, то будет отображаться стоимость для реселлера (price_reseller).
            //shipping_company_id – Идентификатор транспортной компании.
            //payment_method_id – Идентификатор способа оплаты.
        ];

        try {
            $res = json_decode(self::post('/basket', $params), true);
            Log::info('add to basket=' . print_r($res, 1));

            return $res;
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        delete goods from basket
    */
    static public function delFromBasket($goods_list)
    {
        $params = [
            'user_data_id' => (int) getenv('AMTEL_USER_DATA_ID'), // (int) * – Идентификатор клиента
            //'basket_num' => getenv('AMTEL_BASKET_NUM', 1), //(int) * – Идентификатор корзины, указанный реселлером
            'basket_goods_list' => json_decode($goods_list), // Список идентификаторов позиций корзины
            //'session_id' =>  null
        ];

        try {
            $res = json_decode(self::del('/basket', $params), true);
            Log::info('del from basket=' . print_r($res, 1));

            return $res;
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        get all orders
        @http_params: state_present, state_except, order_list, from_date, to_date, tags
    */
    static public function getOrder($request)
    {
        $params = [
            'user_data_id' => (int) getenv('AMTEL_USER_DATA_ID'), // (int) * – Идентификатор клиента
            'state_present' => $request->input('state_present'), // Только заказы находящиеся в статусах, если null, то возвращаются заказы во всех статусах.
            'state_except' => $request->input('state_except'), // Только заказы не находящиеся в статусах, если null, то возвращаются заказы во всех статусах.
            'order_header_id_list' =>  $request->input('order_list'), // Получение содержимого только для указанных заказов
            'from_date' =>  $request->input('from_date'), // С даты, в формате yyyy-mm-dd hh:mm:ss.
            'to_date' => $request->input('to_date'), // По дату, в формате yyyy-mm-dd hh:mm:ss.
            'tags' => $request->input('tags'), //Массив тегов, по которым осуществить фильтрацию.
            //filter_headers_by_list – Фильтровать список заказов по идентификаторам в order_header_id_list.
        ];

        try {
            $res = json_decode(self::get('/order', $params, false), true);
            Log::info('get order=' . print_r($res, 1));

            return $res;
            //return self::explodeOrders($res);
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        add to order in basket by goods_id
    */
    static public function add2Order($goods_list)
    {
        $params = [
            'user_data_id' => (int) getenv('AMTEL_USER_DATA_ID'), // (int) * – Идентификатор клиента для которого делается заказ (отличается от user_id для доступа к API, выдается вместе с остальными учетными данными при подключении)
            'basket_num' => (int) getenv('AMTEL_BASKET_NUM', 1), // Номер корзины клиента, указанный реселлером при добавлении товара в корзину.
            'delivery_city_id' => getenv('AMTEL_DELIVERY_CITY_ID', 1), // Идентификатор города доставки.
            'delivery_address' => getenv('AMTEL_DELIVERY_ADDRESS', ''), // Адрес доставки.
            'delivery_contact' => getenv('AMTEL_DELIVERY_CONTACT', ''), // Телефон получателя заказа.
            'delivery_tel' => getenv('AMTEL_DELIVERY_TEL', ''), // Телефон получателя заказа.
            'delivery_comment' => getenv('AMTEL_DELIVERY_COMMENT', ''), // Комментарий к заказу.
            'allow_supplier_offer_sim' => getenv('AMTEL_ALLOW_REPLACE', false), // Разрешить поставщику предлагать замену.
            'to_split' => false, // Разбивать заказ при наличии разных поставщиков товаров в корзине.
            'basket_goods_list' => json_decode($goods_list), // Список basket_goods_id для заказа, остальные останутся в корзине, если не указан - закажутся все товары.
            //'shipping_company_id => ' – Идентификатор транспортной компании.
            //payment_method_id – Идентификатор способа оплаты.
            //tags – Список тегов, по которым после создания заказа будет возможна фильтрация, задаются в виде массива строк, например: ["tag1", "tag2"]. Например можно указать номер заказа, идентификатор клиента в системе реселлера.
            //order_list_url – Адрес на стороне реселлера, по которому доступен лист заказа (будет приложен поставщиком к заказу).
        ];

        try {
            $res = json_decode(self::post('/order', $params), true);
            Log::info('add to order=' . print_r($res, 1));

            return $res;
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        delete goods from order
    */
    static public function delFromOrder($goods_id)
    {
        $params = [
            'user_data_id' => (int) getenv('AMTEL_USER_DATA_ID'), // (int) * – Идентификатор клиента
            'order_goods_id' => (int) $goods_id, // Идентификатор товара в заказе
        ];

        try {
            $res = json_decode(self::del('/order/goods', $params), true);
            Log::info('del from order=' . print_r($res, 1));

            return $res;
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        add to order in basket by goods_id
    */
    static public function confirmOrder($goods_id)
    {
        $params = [
            //'user_data_id' => (int) getenv('AMTEL_USER_DATA_ID'), // (int) * – Идентификатор клиента для которого делается заказ (отличается от user_id для доступа к API, выдается вместе с остальными учетными данными при подключении)
            'order_goods_id' => (int) $goods_id, // Идентификатор товара в заказе
        ];

        try {
            $res = json_decode(self::post('/order/goods_confirm', $params), true);
            Log::info('add to order=' . print_r($res, 1));

            return $res;
        } catch (\Exception $e) {
            Log::error($e);
            throw new HttpException(500, $e->getMessage());
        }
    }

    /*
        plain info from api '/order' result

     * @param (object) orders
    */
    static private function explodeOrders($orders)
    {
        //Log::info('explodeOrders goods=' . print_r($orders, 1));

        $goods = [];
        if ($orders['result'] = true) {
            foreach ($orders['order_goods_list'] as $avail) {
                $avail = $avail[0];
                Log::info('avail=' . print_r($avail, 1));

                $goods[$avail['goods_internal_id']] = [
                    'id' => $avail['goods_internal_id'],
                    'goods_id' => $avail['goods_id'],
                    'company_name' => $avail['company_name'],
                    'count' => $avail['count'],
                    'count_avail' => $avail['count_avail'],
                    'expected_delivery_days' => $avail['expected_delivery_days'],
                    'goods_name' => $avail['goods_name'],
                    'goods_supplier_sh_id' => $avail['goods_supplier_sh_id'],
                    'num' => $avail['num'],
                    'price' => $avail['price'],
                    'price_reseller' => $avail['price_reseller'],
                    'state' => $avail['state'],
                    'state_change_date' => $avail['state_change_date'],
                    'supplier_point_id' => $avail['supplier_point_id'],
                    'tags' => $avail['tags'],
                    'user_comment' => $avail['user_comment'],
                ];
            }
        }

        return $goods;
    }
}
