<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Faiznurullah\Rajaongkir\RajaOngkir;

class Test
{
    public function test()
    {
        $object = new RajaOngkir('your-api-key-here');
        $response = $object->getProvinces();
        print_r($response);
    }

    // run test function using command line
    public static function main()
    {
        $test = new self();
        $test->test();
    }

    
}

Test::main();