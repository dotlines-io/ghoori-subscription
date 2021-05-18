<?php

/** @noinspection PhpComposerExtensionStubsInspection */
/** @noinspection SpellCheckingInspection */
/** @noinspection MethodVisibilityInspection */

namespace Dotlines\GhooriSubscription\Tests;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use Dotlines\GhooriSubscription\Request;
use JsonException;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public string $serverUrl = 'https://sb-payments.ghoori.com.bd';
    public string $tokenUrl = 'https://sb-payments.ghoori.com.bd/oauth/token';
    public string $username = 'demo@gmail.com';
    public string $password = 'demo1234';
    public int $clientID = 39;
    public string $clientSecret = 'gS2ujsPALkQBakAoumes0pZrxm4y6Oktwggg07AB';

    /**
     * @test
     * @throws JsonException
     */
    final public function it_can_fetch_charge_url(): void
    {
        $accessTokenRequest = AccessTokenRequest::getInstance($this->tokenUrl, $this->username, $this->password, $this->clientID, $this->clientSecret);
        $tokenResponse = $accessTokenRequest->send();

        $accessToken = $tokenResponse['access_token'];

        $subscriptionUrl = $this->serverUrl . '/api/v1.0/subscribe';
        $requestID = 'test-app-' . random_int(111111, 999999);
        $package = 'BBC_Janala_Weekly1';
        $cycle = 'WEEKLY'; //possible values: DAILY, WEEKLY, FIFTEEN_DAYS, MONTHLY, THIRTY_DAYS, NINETY_DAYS, ONE_EIGHTY_DAYS
        $start = Carbon::now()->format('Y-m-d');
        $end = Carbon::now()->addYear()->format('Y-m-d');
        $userReturnUrl = 'https://test-app.local';
        $mobile = ''; //optional
        $email = ''; //optional
        $reference = ''; //optional
        $subscriptionRequest = Request::getInstance($subscriptionUrl, $accessToken, $this->clientID, $requestID, $package, $cycle, $start, $end, $userReturnUrl, $mobile, $email, $reference);

        self::assertNotEmpty($subscriptionRequest->send());
    }
}
