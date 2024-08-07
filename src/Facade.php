<?php

namespace Faiznurullah\Rajaongkir;
use illuminate\Support\Facades\Facade;

class RajaongkirFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rajaongkir';
    }
}