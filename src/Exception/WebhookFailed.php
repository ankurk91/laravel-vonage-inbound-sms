<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound\Exception;

use Exception;

class WebhookFailed extends Exception
{
    public static function jobClassDoesNotExist(string $jobClass): static
    {
        return new static("Could not process webhook, the configured class `$jobClass` not found.");
    }
}
