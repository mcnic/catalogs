<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use App\Amtel\Amtel;

class ExampleTest extends TestCase
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

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetAmtelRequest()
    {

        $params = [
            'vehicle_model_types' => [1],
            'avail_only' => true
        ];

        $jwt = Amtel::request($params);

        //echo "\n" . $jwt . "\n";

        $this->assertTrue(true);

        $res = Amtel::get('/v2/company', $params);
        //print_r($res);
    }
}
