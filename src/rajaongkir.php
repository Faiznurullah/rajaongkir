<?php

namespace Faiznurullah\Rajaongkir;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

/**
 * RajaOngkir API Wrapper
 * 
 * A PHP wrapper for the RajaOngkir shipping cost calculation API
 * 
 * @package Faiznurullah\Rajaongkir
 * @author Faiz Nurullah
 */
class RajaOngkir
{
    /**
     * RajaOngkir API Key
     * 
     * @var string
     */
    private $apiKey;

    /**
     * RajaOngkir account type (Starter, Basic, Pro)
     * 
     * @var string
     */
    private $accountType;

    /**
     * Base API URL
     * 
     * @var string
     */
    private $baseUrl;

    /**
     * HTTP Client
     * 
     * @var Client
     */
    private $client;

    /**
     * Account type constants
     */
    const ACCOUNT_STARTER = 'starter';
    const ACCOUNT_BASIC = 'basic';
    const ACCOUNT_PRO = 'pro';

    /**
     * API Endpoints
     */
    const ENDPOINT_PROVINCE = '/province';
    const ENDPOINT_CITY = '/city';
    const ENDPOINT_SUBDISTRICT = '/subdistrict';
    const ENDPOINT_COST = '/cost';
    const ENDPOINT_INTERNATIONAL_ORIGIN = '/v2/internationalOrigin';
    const ENDPOINT_INTERNATIONAL_DESTINATION = '/v2/internationalDestination';
    const ENDPOINT_INTERNATIONAL_COST = '/v2/internationalCost';
    const ENDPOINT_CURRENCY = '/currency';
    const ENDPOINT_WAYBILL = '/waybill';

    /**
     * RajaOngkir constructor
     * 
     * @param string $apiKey RajaOngkir API Key
     * @param string $accountType RajaOngkir account type (starter, basic, pro)
     * @throws InvalidArgumentException If account type is invalid
     */
    public function __construct(string $apiKey, string $accountType = self::ACCOUNT_STARTER)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException('API Key is required');
        }

        $this->apiKey = $apiKey;
        $this->accountType = strtolower($accountType);
        $this->setBaseUrl();
        $this->client = new Client();
    }

    /**
     * Set the base URL based on account type
     * 
     * @return void
     * @throws InvalidArgumentException If account type is invalid
     */
    private function setBaseUrl(): void
    {
        switch ($this->accountType) {
            case self::ACCOUNT_STARTER:
                $this->baseUrl = 'https://api.rajaongkir.com/starter';
                break;
            case self::ACCOUNT_BASIC:
                $this->baseUrl = 'https://api.rajaongkir.com/basic';
                break;
            case self::ACCOUNT_PRO:
                $this->baseUrl = 'https://pro.rajaongkir.com/api';
                break;
            default:
                throw new InvalidArgumentException(
                    "Invalid account type: {$this->accountType}. Valid options are: starter, basic, pro"
                );
        }
    }

    /**
     * Make an API request to RajaOngkir
     * 
     * @param string $method HTTP method (GET, POST)
     * @param string $endpoint API endpoint
     * @param array $params Request parameters
     * @return array API response
     */
    public function request(string $method, string $endpoint, array $params = []): array
    {
        $options = [
            'headers' => [
                'key' => $this->apiKey,
                'content-type' => 'application/x-www-form-urlencoded',
            ]
        ];

        if ($method === 'POST') {
            $options['form_params'] = $params;
        } elseif (!empty($params)) {
            $endpoint .= '?' . http_build_query($params);
        }

        try {
            $response = $this->client->request($method, $this->baseUrl . $endpoint, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return [
                'rajaongkir' => [
                    'status' => [
                        'code' => $e->getCode(),
                        'description' => 'Error: ' . $e->getMessage()
                    ]
                ]
            ];
        } catch (\Exception $e) {
            return [
                'rajaongkir' => [
                    'status' => [
                        'code' => $e->getCode() ?: 500,
                        'description' => 'Error: ' . $e->getMessage()
                    ]
                ]
            ];
        }
    }

    /**
     * Get province data
     * 
     * @param int|null $id Province ID
     * @return array
     */
    public function getProvince(?int $id = null): array
    {
        $params = [];
        if ($id !== null) {
            $params['id'] = $id;
        }
        return $this->request('GET', self::ENDPOINT_PROVINCE, $params);
    }

    /**
     * Get city data
     * 
     * @param int|null $cityId City ID
     * @param int|null $provinceId Province ID
     * @return array
     */
    public function getCities(?int $cityId = null, ?int $provinceId = null): array
    {
        $params = [];
        if ($cityId !== null) {
            $params['id'] = $cityId;
        }
        if ($provinceId !== null) {
            $params['province'] = $provinceId;
        }
        return $this->request('GET', self::ENDPOINT_CITY, $params);
    }

    /**
     * Get subdistrict data (Pro account only)
     * 
     * @param int $cityId City ID
     * @return array
     */
    public function getSubdistricts(int $cityId): array
    {
        if ($this->accountType !== self::ACCOUNT_PRO) {
            return [
                'rajaongkir' => [
                    'status' => [
                        'code' => 400,
                        'description' => 'This feature is only available for Pro accounts'
                    ]
                ]
            ];
        }

        return $this->request('GET', self::ENDPOINT_SUBDISTRICT, ['city' => $cityId]);
    }

    /**
     * Calculate shipping cost
     * 
     * @param int|string $origin Origin city/subdistrict ID
     * @param string $originType Origin type (city, subdistrict)
     * @param int|string $destination Destination city/subdistrict ID
     * @param string $destinationType Destination type (city, subdistrict)
     * @param int $weight Weight in grams
     * @param string $courier Courier code (jne, tiki, pos, etc)
     * @return array
     */
    public function getCost($origin, string $originType, $destination, string $destinationType, int $weight, string $courier): array
    {
        $params = [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        ];

        // Add originType and destinationType parameters for Pro accounts
        if ($this->accountType === self::ACCOUNT_PRO) {
            $params['originType'] = $originType;
            $params['destinationType'] = $destinationType;
        }

        return $this->request('POST', self::ENDPOINT_COST, $params);
    }

    /**
     * Get international origin data
     * 
     * @param int|null $id City ID
     * @param int|null $provinceId Province ID
     * @return array
     */
    public function getInternationalOrigin(?int $id = null, ?int $provinceId = null): array
    {
        if ($this->accountType !== self::ACCOUNT_PRO) {
            return [
                'rajaongkir' => [
                    'status' => [
                        'code' => 400,
                        'description' => 'This feature is only available for Pro accounts'
                    ]
                ]
            ];
        }

        $params = [];
        if ($id !== null) {
            $params['id'] = $id;
        }
        if ($provinceId !== null) {
            $params['province'] = $provinceId;
        }

        return $this->request('GET', self::ENDPOINT_INTERNATIONAL_ORIGIN, $params);
    }

    /**
     * Get international destination data
     * 
     * @param int|null $id Country ID
     * @return array
     */
    public function getInternationalDestination(?int $id = null): array
    {
        if ($this->accountType !== self::ACCOUNT_PRO) {
            return [
                'rajaongkir' => [
                    'status' => [
                        'code' => 400,
                        'description' => 'This feature is only available for Pro accounts'
                    ]
                ]
            ];
        }

        $params = [];
        if ($id !== null) {
            $params['id'] = $id;
        }

        return $this->request('GET', self::ENDPOINT_INTERNATIONAL_DESTINATION, $params);
    }

    /**
     * Calculate international shipping cost
     * 
     * @param int $origin Origin city ID
     * @param int $destination Destination country ID
     * @param int $weight Weight in grams
     * @param string $courier Courier code
     * @return array
     */
    public function getInternationalCost(int $origin, int $destination, int $weight, string $courier): array
    {
        if ($this->accountType !== self::ACCOUNT_PRO) {
            return [
                'rajaongkir' => [
                    'status' => [
                        'code' => 400,
                        'description' => 'This feature is only available for Pro accounts'
                    ]
                ]
            ];
        }

        $params = [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        ];

        return $this->request('POST', self::ENDPOINT_INTERNATIONAL_COST, $params);
    }

    /**
     * Get currency exchange rate (USD to IDR)
     * 
     * @return array
     */
    public function getCurrency(): array
    {
        if ($this->accountType !== self::ACCOUNT_PRO) {
            return [
                'rajaongkir' => [
                    'status' => [
                        'code' => 400,
                        'description' => 'This feature is only available for Pro accounts'
                    ]
                ]
            ];
        }

        return $this->request('GET', self::ENDPOINT_CURRENCY);
    }

    /**
     * Track shipment by waybill number
     * 
     * @param string $waybill Waybill number
     * @param string $courier Courier code
     * @return array
     */
    public function trackWaybill(string $waybill, string $courier): array
    {
        if ($this->accountType === self::ACCOUNT_STARTER) {
            return [
                'rajaongkir' => [
                    'status' => [
                        'code' => 400,
                        'description' => 'This feature is not available for Starter accounts'
                    ]
                ]
            ];
        }

        $params = [
            'waybill' => $waybill,
            'courier' => $courier
        ];

        return $this->request('POST', self::ENDPOINT_WAYBILL, $params);
    }
}