<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound\Tests;

use Ankurk91\Vonage\SMS\Inbound\Http\Controllers\VonageWebhooksController;
use Ankurk91\Vonage\SMS\Inbound\Jobs\ProcessVonageWebhookJob;
use Ankurk91\Vonage\SMS\Inbound\Model\VonageWebhookCall;
use Ankurk91\Vonage\SMS\Inbound\VonageSignatureValidator;
use Ankurk91\Vonage\SMS\Inbound\VonageWebhookConfig;
use Ankurk91\Vonage\SMS\Inbound\VonageWebhookProfile;
use Ankurk91\Vonage\SMS\Inbound\VonageWebhooksServiceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use Spatie\WebhookClient\WebhookConfig;

#[CoversClass(VonageSignatureValidator::class)]
#[CoversClass(VonageWebhooksController::class)]
#[CoversClass(ProcessVonageWebhookJob::class)]
#[CoversClass(VonageWebhookCall::class)]
#[CoversClass(VonageSignatureValidator::class)]
#[CoversClass(VonageWebhookConfig::class)]
#[CoversClass(VonageWebhookProfile::class)]
#[CoversClass(VonageWebhooksServiceProvider::class)]
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
