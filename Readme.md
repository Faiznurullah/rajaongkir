# RajaOngkir PHP Wrapper

A lightweight PHP library to access RajaOngkir endpoints through `https://rajaongkir.komerce.id/api/v1`.

## Features

- Get provinces
- Get cities by province
- Get districts by city
- Get subdistricts by district
- Calculate domestic shipping cost by district
- Track waybill (AWB)
- Search domestic destinations

## Requirements

- PHP 7.4+
- Composer

## Installation

If you are using this package in another project:

```bash
composer require faiznurullah/rajaongkir
```

For local development in this repository:

```bash
composer install
```

## Quick Start

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Faiznurullah\Rajaongkir\RajaOngkir;

$apiKey = 'YOUR_API_KEY';
$rajaOngkir = new RajaOngkir($apiKey);

$provinces = $rajaOngkir->getProvinces();
print_r($provinces);
```

## API Methods

### 1) Get Provinces

```php
$response = $rajaOngkir->getProvinces();
```

### 2) Get Cities

```php
$provinceId = 5;
$response = $rajaOngkir->getCities($provinceId);
```

### 3) Get Districts

```php
$cityId = 39;
$response = $rajaOngkir->getDistrict($cityId);
```

### 4) Get Subdistricts

```php
$districtId = 574;
$response = $rajaOngkir->getSubdistrict($districtId);
```

### 5) Calculate District Shipping Cost

```php
$originDistrictId = 574;
$destinationDistrictId = 114;
$weight = 1000; // grams
$courier = 'jne';

$response = $rajaOngkir->calculateDistrictCost(
	$originDistrictId,
	$destinationDistrictId,
	$weight,
	$courier
);
```

### 6) Track Waybill

```php
$waybill = 'JP1234567890';
$courier = 'jne';

$response = $rajaOngkir->trackWaybill($waybill, $courier);
```

### 7) Search Domestic Destination

```php
$query = 'Bandung';

// With optional limit and offset
$response = $rajaOngkir->searchDomesticDestination($query, 10, 0);

// Without optional arguments
$response = $rajaOngkir->searchDomesticDestination($query);
```

## Error Handling

All methods return an array.

If a request fails, the library returns an array with an error description (from Guzzle exception or generic exception), for example:

```php
[
	'rajaongkir' => [
		'status' => [
			'code' => 500,
			'description' => 'Error: ...'
		]
	]
]
```

## Run Local Test Script

This repository includes `src/test.php`.

From project root:

```bash
php src/test.php
```

Or from `src` directory:

```bash
php test.php
```

## Troubleshooting

### Class not found (`RajaOngkir`)

Make sure Composer autoload is loaded before using the class:

```php
require_once __DIR__ . '/../vendor/autoload.php';
```

### Warning: Module "ftp" is already loaded

This warning comes from your PHP extension configuration and is unrelated to this package.

Check your `php.ini` and remove duplicate `extension=ftp` entries.

## License

MIT
