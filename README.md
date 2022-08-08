# Laravel Otpify 🔑

[![Build Status](https://scrutinizer-ci.com/g/prasanth-j/otpify/badges/build.png?b=master)](https://scrutinizer-ci.com/g/prasanth-j/otpify/build-status/master)
[![Total Downloads](https://img.shields.io/packagist/dt/prasanth-j/otpify.svg)](https://packagist.org/packages/prasanth-j/otpify)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/prasanth-j/otpify)](https://packagist.org/packages/prasanth-j/otpify)
[![License: MIT](https://img.shields.io/packagist/l/prasanth-j/otpify)](https://opensource.org/licenses/MIT)

## Introduction

Otpify is a Laravel package that provides a simple and elegant way to generate and validate one time passwords.

## Installation

You can install the package via composer:

```bash
composer require prasanth-j/otpify
```

You can run the migrations with:

```bash
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="otpify-config"
```

This is the contents of the published config file:

```php
return [
    /**
     * The length of token.
     */
    'digits'    => env('OTPIFY_DIGITS', 6),

    /**
     * The expiry time of token in minutes.
     */
    'validity'  => env('OTPIFY_VALIDITY', 15)
];
```

Optionally, you can publish the migrations using

```php
php artisan vendor:publish --tag="otpify-migrations"
```

## Usage

### 1. Generate OTP

```php
use PrasanthJ\Otpify\Facades\Otpify;

Otpify::generate(string $identifier, int $userId, string $otpType, int $digits, int $validity);
```

-   `$identifier`: The identity (email or mobile) that will be tied to the OTP.
-   `$userId (optional | default = null)`: The user id from user table.
-   `$otpType (optional | default = null)`: The category of the OTP, like login, verification, etc.
-   `$digits (optional | default = 6)`: The amount of digits to be generated, should be 4 to 8.
-   `$validity (optional | default = 15)`: The validity period of the OTP in minutes.

#### Example

```php
use PrasanthJ\Otpify\Facades\Otpify;

$otp = Otpify::generate('john@example.com', 2, 'verification', 6, 10);
```

This will generate a six digit OTP that will be valid for 10 minutes and the success response will be:

```object
{
  "status": "success",
  "token": "535923",
  "message": "OTP genetated successfully"
}
```

### 2. Validate OTP

```php
use PrasanthJ\Otpify\Facades\Otpify;

Otpify::validate(string $identifier, string $token, string $otpType);
```

-   `$identifier`: The identity (email or mobile) that will be tied to the OTP.
-   `$token`: The token tied to the identity.
-   `$otpType (optional | default = null)`: The category of the OTP, like login, verification, etc.

#### Example

```php
use PrasanthJ\Otpify\Facades\Otpify;

$otp = Otpify::generate('john@example.com', '535923', 'verification');
```

#### Responses

**On Success**

```object
{
  "status": "success",
  "message": "OTP is valid"
}
```

**Does not exist**

```object
{
  "status": "error",
  "message": "OTP does not exist"
}
```

**On Error**

```object
{
  "status": "warning",
  "message": "OTP invalid"
}
```

**Expired**

```object
{
  "status": "error",
  "message": "OTP Expired"
}
```

**Already Verified**

```object
{
  "status": "info",
  "message": "OTP already verified"
}
```

### Delete verified tokens

You can delete verified tokens by running the following artisan command:

```bash
php artisan otpify:clean
```

You can also add this artisan command to `app/Console/Kernel.php` to automatically clean on scheduled

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('otpify:clean')->daily();
}
```

## Contribution

If you find an issue with this package or you have any suggestions please help out.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
