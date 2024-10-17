
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

Use the mail channel as a part of the stack channel. 

```plain
LOG_STACK=daily,mail
```

```php
<?php

use Illuminate\Support\Facades\Log;

try {
    new UndefinedClass;
} catch (\Throwable $e) {
    report($e); // The exception should be reported via both daily and mail channels.
}
```
