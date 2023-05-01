<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound\Jobs;

use Ankurk91\Vonage\SMS\Inbound\Exception\WebhookFailed;
use Illuminate\Support\Str;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessVonageWebhookJob extends ProcessWebhookJob
{
    public function handle(): void
    {
        $message = $this->webhookCall->payload;

        $eventKey = $this->createEventKey($message['type']);

        event("vonage-inbound-sms::$eventKey", $this->webhookCall);

        $jobClass = config("vonage-inbound-sms.jobs.$eventKey");

        if (empty($jobClass)) {
            return;
        }

        if (!class_exists($jobClass)) {
            $this->fail(WebhookFailed::jobClassDoesNotExist($jobClass));
            return;
        }

        dispatch(new $jobClass($this->webhookCall));
    }

    protected function createEventKey(string $eventType): string
    {
        return Str::of($eventType)->lower()->replace('.', '_')->value();
    }
}
