# Laravel Log Mail

[![Packagist](https://img.shields.io/packagist/v/confetticode/laravel-log-mail.svg?label=Packagist&style=flat-square)](https://packagist.org/packages/confetticode/laravel-log-mail)
[![Test](https://img.shields.io/github/actions/workflow/status/confetticode/laravel-log-mail/test.yml?branch=main&label=Test&style=flat-square)](https://github.com/confetticode/laravel-log-mail/actions/workflows/test.yml)
[![Downloads](https://img.shields.io/packagist/dt/confetticode/laravel-log-mail.svg?label=Downloads&style=flat-square)](https://packagist.org/packages/confetticode/laravel-log-mail)

Supported PHP 8.2+ and Laravel 11+

## Installation

```
composer require confetticode/laravel-log-mail
```

## Usage

Configure a mail channel.
```php
<?php

return [
    // ...
    'channels' => [
        'mail' => [
            'driver' => 'mail',
            'mailer' => env('LOG_MAIL_MAILER'),
            'name' => env('LOG_MAIL_NAME'),
            'level' => env('LOG_MAIL_LEVEL'),
            'bubble' => env('LOG_MAIL_BUBBLE'),
            'from' => [
                'address' => env('LOG_MAIL_FROM_ADDRESS'),
                'name' => env('LOG_MAIL_FROM_NAME'),
            ],
            'to' => [
                'address' => env('LOG_MAIL_TO_ADDRESS'),
                'name' => env('LOG_MAIL_TO_NAME'),
            ],
        ],
    ],
    // ...
];

```

Log an error via the mail channel.

```php
<?php

use Illuminate\Support\Facades\Log;

try {
    new UndefinedClass;
} catch (\Throwable $e) {
    Log::channel('mail')->error($e->getMessage(), ['exception' => $e]);
}
```

Use the mail channel as a part of the log stack. 

```plain
LOG_STACK=daily,mail
```

## Contributing

Thank you for considering contributing to the `Laravel Log Mail` package!
The contribution guide can be found in the [contributing documentation](https://github.com/confetticode/.github/blob/master/CONTRIBUTING.md).

<div id="license"></div>

## License (MIT)

The MIT License (MIT). Please see the [License](./LICENSE.md) for more information.
