<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound;

use Ankurk91\Vonage\SMS\Inbound\Jobs\ProcessVonageWebhookJob;
use Spatie\WebhookClient\WebhookConfig;

class VonageWebhookConfig
{
    public static function get(): WebhookConfig
    {
        return new WebhookConfig([
            'name' => 'vonage-inbound-sms',
            'signature_validator' => VonageSignatureValidator::class,
            'webhook_profile' => config('vonage-inbound-sms.profile'),
            'webhook_model' => config('vonage-inbound-sms.model'),
            'process_webhook_job' => ProcessVonageWebhookJob::class,
        ]);
    }
}
