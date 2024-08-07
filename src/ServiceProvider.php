<?php

namespace Faiznurullah\Rajaongkir;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class Rajaongkir extends ServiceProvider{

    public function register(){

        
        $this->mergeConfigFrom(__DIR__ . './config/config.php', 'rajaongkir');
        $this->app->singleton('rajaongkir', function($app){
            return new Rajaongkir(Config::get('rajaongkir.API_KEY_ONGKIR'), Config::get('rajaongkir.STATUS_API_KEY'));
        });


    }

    public function boot(){

    }


}
