<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound\Tests;

use Ankurk91\Vonage\SMS\Inbound\VonageSignatureValidator;
use Ankurk91\Vonage\SMS\Inbound\VonageWebhookConfig;
use Spatie\WebhookClient\WebhookConfig;

class VonageSignatureValidatorTest extends TestCase
{
    private WebhookConfig $config;
    private VonageSignatureValidator $validator;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('vonage-inbound-sms.verify_signature', true);
        config()->set('vonage-inbound-sms.signing_secret', 'test-secret');

        $this->config = VonageWebhookConfig::get();
        $this->validator = new VonageSignatureValidator();
    }

    public function test_passes_for_valid_signature(): void
    {
        $this->markTestSkipped('Asking chat gpt.');
    }

    public function test_failed_on_invalid_signature_url()
    {
        $this->markTestSkipped('Asking chat gpt.');
    }

}
