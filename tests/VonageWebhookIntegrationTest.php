<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound\Tests;

use Ankurk91\Vonage\SMS\Inbound\Exception\WebhookFailed;
use Ankurk91\Vonage\SMS\Inbound\Tests\Factory\VonageWebhookFactory;
use Ankurk91\Vonage\SMS\Inbound\Tests\Fixtures\TestHandlerJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Spatie\WebhookClient\Models\WebhookCall;

class VonageWebhookIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        config()->set('vonage-inbound-sms.verify_signature', false);
        config()->set('vonage-inbound-sms.jobs', [
            'text' => TestHandlerJob::class,
            'binary' => 'UnknownJob::class',
        ]);
    }

    public function test_it_can_processes_webhook_successfully()
    {
        Event::fake();
        Bus::fake(TestHandlerJob::class);

        $payload = VonageWebhookFactory::textInboundSMS();

        $this->postJson('/webhooks/vonage/sms/inbound', $payload)
            ->assertSuccessful();

        $this->assertDatabaseCount('webhook_calls', 1);

        Bus::assertDispatched(TestHandlerJob::class, function ($job) {
            $this->assertInstanceOf(WebhookCall::class, $job->webhookCall);

            return true;
        });

        Event::assertDispatched('vonage-inbound-sms::text', function ($event, $eventPayload) {
            $this->assertInstanceOf(WebhookCall::class, $eventPayload);

            return true;
        });
    }

    public function test_it_fails_when_invalid_job_class_configured()
    {
        Event::fake();

        $payload = VonageWebhookFactory::textInboundSMS();
        $payload['type'] = 'binary';

        $this->postJson('/webhooks/vonage/sms/inbound', $payload)
            ->assertSuccessful();

        Event::assertDispatched('vonage-inbound-sms::binary', function ($event, $eventPayload) {
            $this->assertInstanceOf(WebhookCall::class, $eventPayload);

            return true;
        });

        Event::assertDispatched(JobFailed::class, function ($event) {
            $this->assertInstanceOf(WebhookFailed::class, $event->exception);

            return true;
        });
    }

    public function test_it_process_same_webhook_only_once()
    {
        $payload = VonageWebhookFactory::textInboundSMS();

        $this->postJson('/webhooks/vonage/sms/inbound', $payload)
            ->assertSuccessful();

        $this->postJson('/webhooks/vonage/sms/inbound', $payload)
            ->assertSuccessful();

        $this->assertDatabaseCount('webhook_calls', 1);
    }
}
