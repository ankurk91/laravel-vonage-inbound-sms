<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound\Http\Controllers;

use Ankurk91\Vonage\SMS\Inbound\VonageWebhookConfig;
use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProcessor;

class VonageWebhooksController
{
    public function __invoke(Request $request)
    {
        $webhookConfig = VonageWebhookConfig::get();

        return (new WebhookProcessor($request, $webhookConfig))->process();
    }
}
