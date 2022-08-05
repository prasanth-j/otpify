# Laravel Otpify

[![Latest Version on Packagist](https://img.shields.io/packagist/v/prasanth-j/otpify.svg?style=flat-square)](https://packagist.org/packages/prasanth-j/otpify)
[![Total Downloads](https://img.shields.io/packagist/dt/prasanth-j/otpify.svg?style=flat-square)](https://packagist.org/packages/prasanth-j/otpify)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](https://opensource.org/licenses/MIT)
<!--delete-->
---
Otpify is a Laravel package that provides a simple and elegant way to generate and validate one time passwords.

---

## Installation

You can install the package via composer:

```bash
composer require prasanth-j/otpify
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="otpify-migrations"
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

## Usage

```php
use PrasanthJ\Otpify\Facades\Otpify;

Otpify::generate('john@example.com');	\\ Generate otp
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
******