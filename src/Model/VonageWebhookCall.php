<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound\Model;

use Illuminate\Http\Request;
use Spatie\WebhookClient\Models\WebhookCall;
use Spatie\WebhookClient\WebhookConfig;

class VonageWebhookCall extends WebhookCall
{
    protected $table = 'webhook_calls';

    public static function storeWebhook(WebhookConfig $config, Request $request): WebhookCall
    {
        $headers = self::headersToStore($config, $request);

        return self::create([
            'name' => $config->name,
            'url' => $request->path(),
            'headers' => $headers,
            'payload' => $request->json()->all(),
        ]);
    }
}
