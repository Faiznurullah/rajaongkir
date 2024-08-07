<?php

namespace Faiznurullah\Rajaongkir;
use illuminate\Support\Facades\Facade;

class Rajaongkir extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rajaongkir';
    }
}