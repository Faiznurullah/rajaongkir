<?php

namespace Faiznurullah\Rajaongkir;
use GuzzleHttp\Client;

class rajaongkir
{
    
    private $API_KEY_ONGKIR, $STATUS_API_KEY, $url;
    
    
    public function __construct($API_KEY_ONGKIR, $STATUS_API_KEY)
    {
        $this->API_KEY_ONGKIR = $API_KEY_ONGKIR;
        $this->STATUS_API_KEY = $STATUS_API_KEY;
        
        $this->url = 'https://api.rajaongkir.com/starter';
        
        if($this->STATUS_API_KEY == 'Pro'){
            $this->url = 'https://pro.rajaongkir.com/api';
        }
        
        
    }
    
    
    public function getFunction ($method, $subendpoint, $form_params = [])
    {
        try {
            
            $client = new Client();
            $response = $client->request($method, $this->url.$subendpoint, [
                'headers' => [
                    'key' => $this->API_KEY_ONGKIR,
                    'content-type' => 'application/x-www-form-urlencoded',  
                ], 
                'form_params' => $form_params
            ]);
            $result = json_decode($response->getBody()->getContents(), true);
            return $result;
            
        } catch (\Throwable $th) {
            $response = [
                'code' => $th->getCode(),
                'status' => 'error',
                'message' => 'Error API Raja Ongkir',
            ];
        }
    }
    
    
    
    public function getProvince($id)
    {
        $subendpoint = '/province?id='.$id;
        $result = $this->getFunction('GET', $subendpoint);
        return $result;
    }
    
    
    public function getCities($city_id, $province_id)
    {
        $subendpoint = '/city?id='.$city_id.'&province='.$province_id;
        $form_params = [
            'province' => $province_id
        ];
        $result = $this->getFunction('GET', $subendpoint, $form_params);
        return $result;
    }
    
    public function subdistricts($city_id)
    {
        $subendpoint = '/subdistrict?city='.$city_id;
        $result = $this->getFunction('GET', $subendpoint); 
        return $result;
    }
    
    
    
    public function getCost($origin, $originType, $destination, $destinationType, $weight, $courier)
    {
        $subendpoint = '/cost';
        $form_params = [
            'origin' => $origin,
            'originType' => $originType,
            'destination' => $destination,
            'destinationType' => $destinationType,
            'weight' => $weight, 
            'courier' => $courier
        ];
        $result = $this->getFunction('POST', $subendpoint, $form_params);
        return $result;
    }
    
    public function InterntionalOrigin($id, $province){ 
        $subendpoint = '/v2/internationalOrigin?id='.$id.'&province='.$province;
        $result = $this->getFunction('GET', $subendpoint);
        return $result;
    }
    
    public function InterntionalDestination($id){ 
        $subendpoint = '/v2/internationalDestination?id='.$id;
        $result = $this->getFunction('GET', $subendpoint);
        return $result;
    }
    
    public function getCostInterntional($origin, $destination, $weight, $courier)
    {
        $subendpoint = '/v2/internationalCost';
        $form_params = [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        ];
        $result = $this->getFunction('POST', $subendpoint, $form_params);
        return $result;
    }
    
    public function getDollarCurrency()
    {
        $subendpoint = '/currency';
        $result = $this->getFunction('GET', $subendpoint);
        return $result;
    }
    
    public function waybill($waybill, $courier){
        $subendpoint = '/waybill';
        $form_params = [
            'waybill' => $waybill,
            'courier' => $courier
        ];
        $result = $this->getFunction('POST', $subendpoint, $form_params);
        return $result;
    }
    
    
}