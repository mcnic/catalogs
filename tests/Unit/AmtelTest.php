<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Amtel\Amtel;
use App\Amtel\Firms;

class AmtelTest extends TestCase
{


    public function testRedis()
    {
        $redis = Redis::connection('default');
        $redis->connect();
        $redis->disconnect();

        $this->assertTrue(true);
    }

    public function testCache()
    {
        //Cache::store('redis')->put('test_key', 'test_data', 10);

        $value = Cache::store('redis')->remember('test_key', 10, function () {
            return 'test_data';
        });

        $this->assertTrue($value == 'test_data');
    }

    public function testGetFirm()
    {
        $res = Amtel::getFirm('Acura');
        Log::info('firm "acura" =' . $res);

        $this->assertTrue(true);
    }

    public function testAmtelRequest()
    {

        $params = [
            'vehicle_model_types' => [2],
            'avail_only' => true
        ];

        $jwt = Amtel::request($params);

        //echo "\n" . $jwt . "\n";

        $this->assertTrue(true);
    }

    public function testAmtelGetFirms()
    {

        $params = [
            'vehicle_model_types' => [2],
            'avail_only' => true
        ];

        $res = Amtel::get('/v2/company', $params);
        Log::info('/v2/company=' . $res);

        $this->assertTrue(true);
    }

    public function testAmtelGetModel()
    {

        $params = [
            'company_id' => 115979555, // audi
            'model_types' => [0],
            'avail_only' => true
        ];

        $res = Amtel::get('/company/models', $params);
        Log::info('/company/models=' . $res);

        $this->assertTrue(true);
    }
}
