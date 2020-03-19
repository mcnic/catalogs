<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Amtel\Amtel;
use Log;

class AtmtelController extends Controller
{

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
     * get list models for firms
     * @param firm
     * @return {
            "company_name": "BYD",
            "company_name_ru": string,
            "goods_fh_avail": int,
            "goods_sh_avail": int,
            "model_image_list": {...},
            "model_list": [],
            "result": boolean
        }
     */
    private function getModels($firm, $typeAutos)
    {
        $prefix = "/" . getenv('MIX_AMTEL_PREFIX');

        //Log::info('firm=' . Amtel::getFirm($firm)->id);
        $params = [
            'company_id' => Amtel::getFirm($firm)->id,
            'model_types' => [$typeAutos],
            'avail_only' => true
        ];

        $cars = Amtel::get('/company/models', $params);
        Log::info('cars=' . print_r(json_decode($cars), 1));

        return $cars;
    }

    public function getModelsCars($firm)
    {
        return $this->getModels($firm, 0);
    }

    public function getModelsTruck($firm)
    {
        return $this->getModels($firm, 1);
    }
}
