# Vonage Inbound SMS Webhooks Client for Laravel

[![Packagist](https://badgen.net/packagist/v/ankurk91/laravel-vonage-inbound-sms)](https://packagist.org/packages/ankurk91/laravel-vonage-inbound-sms)
[![GitHub-tag](https://badgen.net/github/tag/ankurk91/laravel-vonage-inbound-sms)](https://github.com/ankurk91/laravel-vonage-inbound-sms/tags)
[![License](https://badgen.net/packagist/license/ankurk91/laravel-vonage-inbound-sms)](LICENSE.txt)
[![Downloads](https://badgen.net/packagist/dt/ankurk91/laravel-vonage-inbound-sms)](https://packagist.org/packages/ankurk91/laravel-vonage-inbound-sms/stats)
[![GH-Actions](https://github.com/ankurk91/laravel-vonage-inbound-sms/workflows/tests/badge.svg)](https://github.com/ankurk91/laravel-vonage-inbound-sms/actions)
[![codecov](https://codecov.io/gh/ankurk91/laravel-vonage-inbound-sms/branch/main/graph/badge.svg)](https://codecov.io/gh/ankurk91/laravel-vonage-inbound-sms)

Handle [Vonage](https://developer.vonage.com/en/messaging/sms/guides/inbound-sms) inbound SMS webhooks in Laravel php framework.

## Installation

You can install the package via composer:

```bash
composer require "ankurk91/laravel-vonage-inbound-sms"
```

The service provider will automatically register itself.

You must publish the config file with:

```bash
php artisan vendor:publish --provider="Ankurk91\Vonage\SMS\Inbound\VonageWebhooksServiceProvider"
```

Next, you must publish the migration with:

```bash
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="webhook-client-migrations"
```

After the migration has been published you can create the `webhook_calls` table by running the migrations:

```bash
php artisan migrate
```

Next, for routing, add this route (guest) to your `routes/web.php`

```bash
Route::vonageInboundSMS('webhooks/vonage/sms/inbound');
```

Behind the scenes this will register a `POST` route to a controller provided by this package. Next, you must add that
route to the `except` array of your `VerifyCsrfToken` middleware:

```php
protected $except = [
    '/webhooks/*',
];
```

It is recommended to set up a queue worker to precess the incoming webhooks.

## Setup Vonage account

* Login to Vonage developer [dashboard](https://dashboard.nexmo.com/settings)
* Enter your webhook URL under "Inbound SMS webhooks". 
* :bulb: You can use [ngrok](https://ngrok.com/) for local development
* Copy the "Signature secret" and specify in your `.env` file like

```dotenv
VONAGE_WEBHOOK_SECRET=secret-key-here
```

* Select `HASH_MD5` as your Signature method
* Select `POST_JSON` as your Webhook format

> **Note**
> You may need to contact Vonage support in-order to make Message Signing work on your account.

### Troubleshoot

When using ngrok during development, you must update your `APP_URL` to match with ngrok vanity URL, for example:

```dotenv
APP_URL=https://af59-111-93-41-42.ngrok-free.app
```

You must verify that your webhook URL is publicly accessible by visiting the URL on your terminal

```bash
curl -X POST https://af59-111-93-41-42.ngrok-free.app/webhooks/vonage/sms/inbound
```

## Usage

There are 2 ways to handle incoming webhooks via this package.

### 1 - Handling webhook requests using jobs

If you want to do something when a specific event type comes in; you can define a job for that event.
Here's an example of such job:

```php
<?php

namespace App\Jobs\Webhook\Vonage\SMS\Inbound;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Models\WebhookCall;

class TextSMSJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected WebhookCall $webhookCall)
    {
        //
    }

    public function handle()
    {      
        $message = new \Vonage\SMS\Webhook\InboundSMS($this->webhookCall->payload);
            
        // todo do something with $message        
    }
}
```

After having created your job you must register it at the `jobs` array in the `config/vonage-inbound-sms.php` config file.
The key must be in lowercase and dots must be replaced by `_`.
The value must be a fully qualified classname.

```php
<?php

return [
     'jobs' => [
          'text' => \App\Jobs\Webhook\Vonage\SMS\Inbound\TextSMSJob::class,
     ],
];
```

### 2 - Handling webhook requests using events and listeners

Instead of queueing jobs to perform some work when a webhook request comes in, you can opt to listen to the events this
package will fire. Whenever a matching request hits your app, the package will fire
a `vonage-inbound-sms::<name-of-the-event>` event.

The payload of the events will be the instance of `WebhookCall` that was created for the incoming request.

You can listen for such event by registering the listener in your `EventServiceProvider` class.

```php
protected $listen = [
    'vonage-inbound-sms::unicode' => [
        \App\Listeners\Vonage\SMS\Inbound\UnicodeSMSListener::class,
    ],
];
```

Here's an example of such listener class:

```php
<?php

namespace App\Listeners\Vonage\SMS\Inbound;

use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

class UnicodeSMSListener implements ShouldQueue
{
    public function handle(WebhookCall $webhookCall)
    {
        $message = $webhookCall->payload;
               
        // todo do something with $message        
    }
}
```

## Pruning old webhooks (opt-in but recommended)

Update your `app/Console/Kernel.php` file like:

```php
use Illuminate\Database\Console\PruneCommand;
use Spatie\WebhookClient\Models\WebhookCall;

$schedule->command(PruneCommand::class, [
            '--model' => [WebhookCall::class]
        ])
        ->onOneServer()
        ->daily()
        ->description('Prune webhook_calls.');
```

This will delete records older than `30` days, optionally you can modify this duration by publishing this config file.

```bash
php artisan vendor:publish --provider="Spatie\WebhookClient\WebhookClientServiceProvider" --tag="webhook-client-config"
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

### Testing

```bash
composer test
```

### Security

If you discover any security issue, please email `pro.ankurk1[at]gmail[dot]com` instead of using the issue tracker.

### Useful Links

* [SMS Overview on Vonage](https://developer.vonage.com/en/messaging/sms/overview)
* [Enable Message Signing on Vonage](https://developer.vonage.com/en/blog/using-message-signatures-to-ensure-secure-incoming-webhooks-dr#enable-message-signing)

### Acknowledgment

This package is highly inspired by:

* https://github.com/spatie/laravel-stripe-webhooks

### License

This package is licensed under [MIT License](https://opensource.org/licenses/MIT).
