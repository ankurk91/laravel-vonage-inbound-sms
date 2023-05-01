<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound;

use Ankurk91\Vonage\SMS\Inbound\Model\VonageWebhookCall;
use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class VonageWebhookProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        $config = VonageWebhookConfig::get();

        return VonageWebhookCall::query()
            ->where('name', $config->name)
            ->where('payload->messageId', $request->input('messageId'))
            ->doesntExist();
    }
}
