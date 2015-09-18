# ontraport-php-sdk
SDK for the New ONTRAPORT API

Uses Guzzle 5.3

Based on original documentation located here: https://api.ontraport.com/doc/

[**composer**](https://getcomposer.org/) is the recommended way to install the SDK.

It is available at [https://packagist.org](https://packagist.org/packages/kzap/ontraport-php-sdk). To use it in your project, you need to include it as a dependency in your project composer.json file.

## Instructions
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