<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Amtel\Amtel;
//use App\Amtel\Models;
use Illuminate\Support\Facades\Log;
use \Symfony\Component\HttpKernel\Exception\HttpException;

class AtmtelController extends Controller
{

    /*
        chech allow true ip from list
    */
    private function isAllowIp()
    {
        $ipList = json_decode(getenv('ALLOW_IP'), '[127.0.0.1]');
        $request = Request();
        return in_array($request->ip(), $ipList);
    }

    /**
     * get list firms
     * 
     * @return ['lightCars'=>[], 'trucks'=>[]]
     */
    public function getFirm()
    {
        $lightCars = [];
        $trucks = [];
        $prefix = "/" . getenv('MIX_AMTEL_PREFIX');

        // fill lightCars
        $cars = json_decode(Amtel::get('/v2/company', [
            'vehicle_model_types' => [0],
            'avail_only' => true
        ]));

        foreach ($cars->companies as $car) {
            //$lightCars[$car->company_id] = $car->company_name;
            $lightCars[] = [
                'id' => $car->company_id,
                'title' => $car->company_name,
                'url' => $prefix . '/cars/' . $car->company_name
            ];
        }

        // fill trucks
        $cars = json_decode(Amtel::get('/v2/company', [
            'vehicle_model_types' => [1],
            'avail_only' => true
        ]));

        foreach ($cars->companies as $car) {
            //$trucks[$car->company_id] = $car->company_name;
            $trucks[] = [
                'id' => $car->company_id,
                'title' => $car->company_name,
                'url' => $prefix . '/truck/' . $car->company_name
            ];
        }

        return [
            'lightCars' => $lightCars,
            'trucks' => $trucks
        ];
    }

    /**
     * get array of modelGroups for firms
     * @param firm
     * @return {
            "title": string,
            "img": string,
        }
     */
    public function getModelGroups($firm)
    {
        return Amtel::getModelGroups($firm);
    }

    /**
     * get list models for firms&modelGroup
     * @param firm
     * @param modelGroup
     * @return {
            "company_name": string,
            "company_name_ru": string,
            "goods_fh_avail": int,
            "goods_sh_avail": int,
            "model_image_list": {...},
            "model_list": [],
            "result": boolean
        }
     */
    public function getModels($typeAutos, $firm, $modelGroup)
    {
        $prefix = "/" . getenv('MIX_AMTEL_PREFIX');

        $models = Amtel::getModels($firm, $modelGroup);
        Log::info('models=' . print_r($models, 1));

        if (!count($models)) {
            return [
                'models' => [],
                'avail' => [],
                'error' => 'wrong model'
            ];
        }

        switch (mb_strtolower($typeAutos)) {
            case 'cars':
                $type = 0;
                break;
            case 'truck':
                $type = 1;
                break;
            default:
                return [
                    'models' => [],
                    'avail' => []
                ];
        }
        //Log::info('firm=' . Amtel::getFirm($firm)->id);

        $params = [
            'company_id' => Amtel::getFirm($firm)->id,
            'model_types' => [$type],
            'avail_only' => true
        ];

        $avail = Amtel::get('/company/models', $params);
        Log::info('avail=' . print_r(json_decode($avail), 1));

        return [
            'models' => $models,
            'avail' => json_decode($avail)
        ];
    }

    public function getModel($modelUrl)
    {
        return Amtel::getModelByUrl($modelUrl);
    }

    public function getGoodsList($modelId)
    {
        return Amtel::getGoodsList($modelId);
    }

    public function getGoodsAll($modelId, $goodId)
    {
        return Amtel::getGoodsAll($modelId, $goodId);
    }

    public function getGoods($modelId, $goodId)
    {
        return Amtel::getGoods($modelId, $goodId);
    }

    public function getGoodsByNum($num)
    {
        return Amtel::getGoodsByNum($num);
    }

    public function getBasket()
    {
        return Amtel::getBasket();
    }

    public function add2Basket($goods_id, $goods_supplier_sh_id, $supplier_point_id, $count)
    {
        return Amtel::add2Basket($goods_id, $goods_supplier_sh_id, $supplier_point_id, $count);
    }

    public function delFromBasket($goods_list)
    {
        return Amtel::delFromBasket($goods_list);
    }

    public function getOrder(Request $request)
    {
        return Amtel::getOrder($request);
    }

    public function add2Order($goods_list)
    {
        return Amtel::add2Order($goods_list);
    }

    public function delFromOrder($goods_id)
    {
        return Amtel::delFromOrder($goods_id);
    }

    public function confirmOrder($goods_id)
    {
        return Amtel::confirmOrder($goods_id);
    }
}
