<?php
declare(strict_types=1);

namespace Ankurk91\Vonage\SMS\Inbound\Tests\Factory;

class VonageWebhookFactory
{
    public static function textInboundSMS(): array
    {
        return self::readFile('text.json');
    }

    public static function textUnicodeSMS(): array
    {
        return self::readFile('unicode.json');
    }

    protected static function readFile(string $name): array
    {
        $body = file_get_contents(__DIR__.'/Messages/'.$name);
        return json_decode($body, true);
    }
}
