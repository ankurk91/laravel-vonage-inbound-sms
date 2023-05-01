<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class VonageWebhooksServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->getConfigPath() => $this->app->configPath('vonage-inbound-sms.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom($this->getConfigPath(), 'vonage-inbound-sms');

        Route::macro('vonageInboundSMS', function (string $url) {
            return Route::post($url, '\Ankurk91\Vonage\SMS\Inbound\Http\Controllers\VonageWebhooksController')
                ->name('vonageInboundSMS');
        });
    }

    protected function getConfigPath(): string
    {
        return __DIR__ . '/../config/vonage-inbound-sms.php';
    }
}
