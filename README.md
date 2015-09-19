# ontraport-php-sdk
SDK for the New ONTRAPORT API

Uses Guzzle 5.3

Based on original documentation located here: https://api.ontraport.com/doc/

[**composer**](https://getcomposer.org/) is the recommended way to install the SDK.

It is available at [https://packagist.org](https://packagist.org/packages/kzap/ontraport-php-sdk). To use it in your project, you need to include it as a dependency in your project composer.json file.

## Installation
1. Download [Composer](https://getcomposer.org/download/) if not already installed
2. Go to your project directory. If you do not have one, just create a directory and `cd` in.

    ```sh
mkdir project
cd project
    ```
3. Execute `composer require "kzap/ontraport-php-sdk:*" ` on command line. Replace composer with composer.phar if required. It should show something like this:

    ```sh
> composer require "kzap/ontraport-php-sdk:*"

Loading composer repositories with package information
Updating dependencies (including require-dev)
- Installing kzap/ontraport-php-sdk (0.1)
Loading from cache

Writing lock file
Generating autoload files
    ```

## How to use
1. Make sure you are auto-loading Composer in your bootstrap file or main php file:
	```php
require_once __DIR__ . '/vendor/autoload.php';
	```
2. In your class or PHP file, include the namespace of the class:
	```php
use Kzap\Ontraport\Api\Sdk as OntraportSdk;
	```
3. In your constructor or wherever you want to instantiate / use the API, create a new instance of the class and use your **APP_ID** and **API_KEY** as the parameters:
	```php
$this->ontraportSdk = new OntraportSdk("{APP_ID}", "{API_KEY}");
	```
4. Call one of the methods in **Sdk.php** to access the API:
    ```php
$parameters = array(
	'objectId' => $this->ontraportSdk->getObjectTypeByName('contact'),
);
$jsonResponse = $this->ontraportSdk->getObject($parameters);
var_dump($jsonResponse);
    ```

**Sample Code**
	```php
<?php

namespace App;

use Kzap\Ontraport\Api\Sdk as OntraportSdk;

require_once __DIR__ . '/../../../vendor/autoload.php';

class OntraportApp
{
    /* Properties
    -------------------------------*/
    private $ontraportSdk = null;
    
    public function __construct($appId, $apiKey)
    {
        $this->ontraportSdk = new OntraportSdk($appId, $apiKey);
    }

    public function getContacts()
    {
        $parameters = array(
            'objectId' => $this->ontraportSdk->getObjectTypeByName('contact'),
        );
        $jsonResponse = $this->ontraportSdk->getObject($parameters);
        
        return $jsonResponse;
    }
}

$ontraportApp = new OntraportApp("APP_ID", "API_KEY");
$contacts = $ontraportApp->getContacts();
var_dump($contacts);

	```