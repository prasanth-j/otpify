# Otpify

[![Build Status](https://scrutinizer-ci.com/g/prasanth-j/otpify/badges/build.png?b=master)](https://scrutinizer-ci.com/g/prasanth-j/otpify/build-status/master)
[![Total Downloads](https://img.shields.io/packagist/dt/prasanth-j/otpify.svg)](https://packagist.org/packages/prasanth-j/otpify)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/prasanth-j/otpify)](https://packagist.org/packages/prasanth-j/otpify)
[![License: MIT](https://img.shields.io/packagist/l/prasanth-j/otpify)](https://opensource.org/licenses/MIT)

A secure, flexible OTP (One-Time Password) package for Laravel. Supports database and cache
storage drivers, multiple purposes per identifier, hashed token storage, events, and a ready-made
validation rule.

## Requirements

- PHP 8.1+
- Laravel 9, 10, 11, 12, or 13

Each Laravel major has its own PHP range and support window (per Laravel's official support
policy), so the actual floor depends on which Laravel version you're on:

| Laravel | PHP range | Security fixes until |
|---|---|---|
| 9 | 8.0 – 8.2 (this package requires 8.1+) | Feb 2024 — EOL |
| 10 | 8.1 – 8.3 | Feb 2025 — EOL |
| 11 | 8.2 – 8.4 | Mar 2026 — EOL |
| 12 | 8.2 – 8.5 | Feb 2027 |
| 13 | 8.3 – 8.5 | Mar 2028 |

Laravel 9, 10, and 11 no longer receive security fixes upstream. This package still works on them,
but for new projects prefer Laravel 12 or 13.

## Installation

```bash
composer require prasanth-j/otpify
```

Publish the config file:

```bash
php artisan vendor:publish --tag=otpify-config
```

If you plan to use the `database` driver (the default), publish and run the migration:

```bash
php artisan vendor:publish --tag=otpify-migrations
php artisan migrate
```

## Configuration

`config/otpify.php`:

```php
return [
    'driver'   => env('OTPIFY_DRIVER', 'database'),
    'digits'   => env('OTPIFY_DIGITS', 6),
    'validity' => env('OTPIFY_VALIDITY', 10), // minutes
    'type'     => env('OTPIFY_TYPE', 'numeric'), // numeric|alpha|alphanumeric
    'cache'    => [
        'prefix' => 'otpify',
        'store'  => env('OTPIFY_CACHE_STORE', null), // null = default cache store
    ],
];
```

| Key | Description |
|---|---|
| `driver` | `database` or `cache`. |
| `digits` | Default OTP length (4-8). Can be overridden per call. |
| `validity` | Default expiry time in minutes. Can be overridden per call. |
| `type` | Default character set: `numeric`, `alpha`, or `alphanumeric`. |
| `cache.prefix` | Prefix used to build cache keys (`otpify:{identifier}:{purpose}`). |
| `cache.store` | Which cache store to use. `null` uses your app's default store. |

## Security

- OTPs are generated with `random_int()`, never `mt_rand()`.
- Only the SHA-256 hash of the OTP is stored (`hash('sha256', $token)`) — the plaintext is never
  persisted.
- Validation compares hashes with `hash_equals()` for constant-time comparison.
- The plaintext token is only ever returned once, from `generate()`/`resend()`, so you can send it
  via email/SMS/etc. `validate()` never returns the token.
- OTPs are single-use: a successful `validate()` call immediately invalidates the OTP.

## Usage

### Generate an OTP

```php
use PrasanthJ\Otpify\Facades\Otpify;

$result = Otpify::generate('user@example.com'); // purpose defaults to "default"

$result->token;     // e.g. "482913" — send this to the user
$result->expiresAt; // Carbon instance
$result->status;    // "generated"
```

With a purpose and custom options:

```php
$result = Otpify::generate('user@example.com', 'login', [
    'digits'   => 4,
    'validity' => 5,          // minutes
    'type'     => 'alphanumeric',
]);
```

Generating a new OTP for the same identifier + purpose deletes any previous one — only the latest
OTP for a given identifier/purpose is ever valid.

### Validate an OTP

```php
$result = Otpify::validate('user@example.com', $token, 'login');

if ($result->isValid()) {
    // proceed
}
```

`OtpResult` status values and helpers:

| Status | Helper | Meaning |
|---|---|---|
| `valid` | `isValid()` | Token matched, unused, not expired. |
| `invalid` | `isInvalid()` | Token does not match. |
| `expired` | `isExpired()` | Token matched an OTP that has expired. |
| `already_used` | `isAlreadyUsed()` | OTP was already validated once. |
| `not_found` | — | No OTP exists for that identifier/purpose. |
| `generated` | `wasGenerated()` | Returned only by `generate()`/`resend()`. |

### Invalidate an OTP

```php
Otpify::invalidate('user@example.com', 'login'); // returns bool
```

### Resend an OTP

Invalidates any existing OTP for the identifier/purpose and generates a fresh one:

```php
$result = Otpify::resend('user@example.com', 'login', ['validity' => 10]);
```

### Multiple purposes

Every method accepts a `purpose` so the same identifier can hold independent OTPs at once, e.g.
`'login'`, `'2fa'`, `'password-reset'`.

```php
Otpify::generate('user@example.com', 'login');
Otpify::generate('user@example.com', '2fa');
```

## Validation rule

Use `OtpRule` to validate an OTP as part of a normal Laravel form request or validator:

```php
use Illuminate\Support\Facades\Validator;
use PrasanthJ\Otpify\Rules\OtpRule;

$validator = Validator::make($request->all(), [
    'otp' => ['required', new OtpRule($request->input('email'), 'login')],
]);
```

## Events

| Event | Fired when |
|---|---|
| `PrasanthJ\Otpify\Events\OtpGenerated` | An OTP is generated. Has `identifier`, `purpose`, `token`, `expiresAt`. |
| `PrasanthJ\Otpify\Events\OtpValidated` | An OTP passes validation. Has `identifier`, `purpose`. |
| `PrasanthJ\Otpify\Events\OtpFailed` | An OTP fails validation. Has `identifier`, `purpose`, `reason` (the failure status). |

```php
use PrasanthJ\Otpify\Events\OtpGenerated;

Event::listen(function (OtpGenerated $event) {
    // send $event->token to $event->identifier via SMS/email
});
```

## Drivers

### Database (default)

Stores hashed OTPs in the `otpify_tokens` table. Requires the migration to be run. Good default
choice — durable, works with the `otpify:clean` command.

### Cache

Stores hashed OTPs in your configured cache store under `otpify:{identifier}:{purpose}`, using the
OTP's expiry as the cache TTL. No migration needed; good fit if you already run Redis/Memcached and
don't need a durable audit trail.

```env
OTPIFY_DRIVER=cache
OTPIFY_CACHE_STORE=redis
```

## Rate limiting

Otpify does not rate limit generation/validation attempts itself — pair it with Laravel's
`RateLimiter`:

```php
use Illuminate\Support\Facades\RateLimiter;
use PrasanthJ\Otpify\Facades\Otpify;

$key = 'otp-generate:' . $request->ip() . ':' . $request->input('email');

if (RateLimiter::tooManyAttempts($key, 5)) {
    abort(429, 'Too many OTP requests. Please try again later.');
}

RateLimiter::hit($key, 60); // 1 attempt per minute window, 5 max

$result = Otpify::generate($request->input('email'));
```

Do the same around `Otpify::validate()` to slow down brute-force guessing attempts.

## Cleaning up expired tokens

For the `database` driver, expired and used rows accumulate over time. Clean them up with:

```bash
php artisan otpify:clean
```

Schedule it in `routes/console.php` (Laravel 11+) or `app/Console/Kernel.php` (Laravel 9/10):

```php
Schedule::command('otpify:clean')->daily();
```

This command is a no-op (with a warning) when the `cache` driver is active, since cache entries
expire on their own via TTL.

## Migrating from v1

v2.0.0 is a full rewrite and breaking change:

- The `otps` table and `Otp` model are gone, replaced by the `otpify_tokens` table (no model).
- `Otpify::generate()`/`validate()` now return an `OtpResult` object instead of an array.
- `Otpify::generate()` signature changed from
  `generate(string $identifier, int $userId = null, string $otpType = null, int $digits = null, int $validity = null)`
  to `generate(string $identifier, string $purpose = 'default', array $options = [])`.
- `otpType` is now `purpose`, and is a required-shaped concept everywhere (defaults to `'default'`).
- OTPs are now stored hashed (SHA-256) instead of in plaintext.
- `user_id` is no longer stored — key OTPs by whatever identifier makes sense for you (email,
  phone, user ID as a string, etc.).

If you have existing data in the old `otps` table, write a one-off migration to hash and copy
`identifier`/`otp_type`/`token`/expiry data into `otpify_tokens`, or simply let outstanding OTPs
expire naturally and drop the old table.

## Development

```bash
composer test      # run the Pest test suite
composer lint       # fix code style with Laravel Pint
composer lint:test  # check code style without fixing
composer analyse    # run static analysis with Larastan
```

Larastan 3.x requires PHP 8.2+ locally to run static analysis, even though the package itself
supports PHP 8.1+.

## License

MIT
