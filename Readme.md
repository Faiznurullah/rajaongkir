# Raja ongkir for php projects

this package is unofficial written in PHP.

## Official documentation
For Documentation please check [Raja ongkir](https://rajaongkir.com/dokumentasi/)

## Installation
Install this package with composer by following command:
```
composer require faiznurullah/rajaongkir
```
or add manually in your ```Composer.json``` file.

## Usage 
### Laravel
on file .env you can add this configuration:
```
API_KEY_ONGKIR = "API_KEY_ONGKIR";
STATUS_API_KEY = "Pro or Starter";
```
on file ```config/app.php```  you can add this configuration.
```
'providers' => [ 
    Faiznurullah\Rajaongkir\RajaongkirServiceProvider::class,
],
```
### Native
Initialize some required credentials. You can get credentials on your raja ongkir account dashboard.
```
<?php

require_once 'location/rajaongkir.php';

// API Key
$apikey = 'API_KEY_ONGKIR';
$status = 'Pro or Starter';
```

## Contributing
For any requests, bugs, or comments, please open an [issue](https://github.com/Faiznurullah/rajaongkir/issues).

## Installing Packages
Before you start to code, run this command to install all of the required packages. Make sure you have composer installed in your computer.
```
composer install
```
I hope you can enjoy and contribute to future development.
