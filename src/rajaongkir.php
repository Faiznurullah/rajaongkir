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
     * API Endpoints
     */
    const ENDPOINT_PROVINCE = '/destination/province';
    const ENDPOINT_CITY = '/destination/city';
    const ENDPOINT_DISTRICT = '/destination/district';
    const ENDPOINT_SUBDISTRICT = '/destination/sub-district';

    const ENDPOINT_CALCULATE_DISTRICT_COST = '/calculate/district/domestic-cost';

    const ENDPOINT_TRACKING_AWB = '/track/waybill';

    const ENDPOINT_SEARCH_DOMESTIC_DESTINATION = '/destination/domestic-destination';


    /**
     * RajaOngkir constructor
     * 
     * @param string $apiKey RajaOngkir API Key
     * @param string $accountType RajaOngkir account type (starter, basic, pro)
     * @throws InvalidArgumentException If account type is invalid
     */
    public function __construct(string $apiKey)
    {
        if (empty($apiKey)) {
            throw new InvalidArgumentException('API Key is required');
        }

        $this->apiKey = $apiKey;
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

        $this->baseUrl = 'https://rajaongkir.komerce.id/api/v1';
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
     * @return array
     */
    public function getProvinces(): array
    {
        return $this->request('GET', self::ENDPOINT_PROVINCE);
    }

    /**
     * Get city data
     * 
     * @param int $provinceId Province ID
     * @return array
     */
    public function getCities(int $provinceId): array
    {
        $url = self::ENDPOINT_CITY . "/" . $provinceId;
        return $this->request('GET', $url);
    }

    /**
     * Get district data
     * @param int $cityId City ID
     * @return array
     */
    public function getDistrict(int $cityId): array
    {
        $url = self::ENDPOINT_DISTRICT . "/" . $cityId;
        return $this->request('GET', $url);
    }

    /**
     * Get subdistrict data
     * @param int $districtId District ID
     * @return array
     */

    public function getSubdistrict(int $districtId): array
    {
        $url = self::ENDPOINT_SUBDISTRICT . "/" . $districtId;
        return $this->request('GET', $url);
    }

    /**
     * Calculate shipping cost based on district
     * @param int $originDistrictId Origin district ID
     * @param int $destinationDistrictId Destination district ID
     * @param int $weight Weight in grams
     * @param string $courier Courier code (jne, pos, tiki)
     * @return array
     */

    public function calculateDistrictCost(int $originDistrictId, int $destinationDistrictId, int $weight, string $courier): array
    {
        $params = [
            'origin' => $originDistrictId,
            'destination' => $destinationDistrictId,
            'weight' => $weight,
            'courier' => $courier,
            'price' => 'lowest'
        ];

        return $this->request('POST', self::ENDPOINT_CALCULATE_DISTRICT_COST, $params);
    }

    /**
     * Track a waybill
     * @param string $waybill Waybill number
     * @param string $courier Courier code (jne, pos, tiki)
     * @return array
     */
    public function trackWaybill(string $waybill, string $courier): array
    {
        $params = [
            'awb' => $waybill,
            'courier' => $courier
        ];

        return $this->request('POST', self::ENDPOINT_TRACKING_AWB, $params);
    }

    /**
     * Search for domestic destination
     * @param string $query Search query (city name or district name)
     * @return array
     */
    public function searchDomesticDestination(string $query, ?int $limit = null, ?int $offset = null): array
    {
        $params = [
            'search' => $query,

        ];

        if ($limit !== null) {
            $params['limit'] = $limit;
        }

        if ($offset !== null) {
            $params['offset'] = $offset;
        }

        return $this->request('GET', self::ENDPOINT_SEARCH_DOMESTIC_DESTINATION, $params);
    }
}
