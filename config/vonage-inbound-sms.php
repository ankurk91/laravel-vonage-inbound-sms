<?php

declare(strict_types=1);

return [
    /*
     * You can define the job that should run when a certain webhook hits your application
     * here. See the examples below for key names.
     */
    'jobs' => [
        // 'text' => \App\Jobs\Webhook\Vonage\SMS\Inbound\HandleTextJob::class,
        // 'unicode' => \App\Jobs\Webhook\Vonage\SMS\Inbound\HandleTextJob::class,
    ],

    /*
    * The classname of the model to be used. The class should equal or extend
    * \Ankurk91\Vonage\SMS\Inbound\Model\VonageWebhookCall.
    */
    'model' => \Ankurk91\Vonage\SMS\Inbound\Model\VonageWebhookCall::class,

    /**
     * This class determines if the incoming webhook call should be stored and processed.
     */
    'profile' => \Ankurk91\Vonage\SMS\Inbound\VonageWebhookProfile::class,

    /*
     * Vonage can sign each webhook using a secret. You can find the used secret at the
     * webhook configuration settings: https://dashboard.nexmo.com/settings
     */
    'signing_secret' => env('VONAGE_WEBHOOK_SECRET'),
    'signing_method' => env('VONAGE_WEBHOOK_SIGNING_METHOD', 'md5hash'),

    /*
     * When disabled, the package will not verify if the signature is valid.
     * This can be handy in local environments and testing.
     */
    'verify_signature' => (bool) env('VONAGE_SIGNATURE_VERIFY', true),

];
