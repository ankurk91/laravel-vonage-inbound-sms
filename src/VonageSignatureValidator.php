<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound;

use Illuminate\Http\Request;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;
use Spatie\WebhookClient\WebhookConfig;
use Vonage\Client\Signature as VonageSignature;

class VonageSignatureValidator implements SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        if (!config('vonage-inbound-sms.verify_signature')) {
            return true;
        }

        $signature = new VonageSignature(
            $request->json()->all(),
            config('vonage-inbound-sms.signing_secret'),
            config('vonage-inbound-sms.signing_method')
        );

        return $signature->check($request->json('sig'));
    }
}
