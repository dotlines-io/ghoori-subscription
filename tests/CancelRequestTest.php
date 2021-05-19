<?php

namespace Dotlines\GhooriSubscription\Tests;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;

class CancelRequestTest extends TestCase
{
    public string $serverUrl = 'https://sb-payments.ghoori.com.bd';
    public string $tokenUrl = 'https://sb-payments.ghoori.com.bd/oauth/token';
    public string $username = 'demo@gmail.com';
    public string $password = 'demo1234';
    public int $clientID = 39;
    public string $clientSecret = 'gS2ujsPALkQBakAoumes0pZrxm4y6Oktwggg07AB';

    public string $accessToken = "";
    public string $subscriptionUrl = "";
    public string $requestID = "";
    public string $package = "";
    public string $cycle = "";
    public string $start = "";
    public string $end = "";
    public string $userReturnUrl = "";
    public string $mobile = "";
    public string $email = "";
    public string $reference = "";

    public string $subscriptionID = "";
    public string $cancelRequestUrl = "";

    public function setUp(): void
    {
        parent::setUp();
        $accessTokenRequest = AccessTokenRequest::getInstance($this->tokenUrl, $this->username, $this->password, $this->clientID, $this->clientSecret);
        $tokenResponse = $accessTokenRequest->send();
        $this->accessToken = $tokenResponse['access_token'];

        $this->subscriptionUrl = $this->serverUrl . '/api/v1.0/subscribe';
        $this->package = 'BBC_Janala_Weekly1';
        $this->cycle = 'WEEKLY'; //possible values: DAILY, WEEKLY, FIFTEEN_DAYS, MONTHLY, THIRTY_DAYS, NINETY_DAYS, ONE_EIGHTY_DAYS
        $this->start = Carbon::now()->format('Y-m-d');
        $this->end = Carbon::now()->addYear()->format('Y-m-d');
        $this->userReturnUrl = 'https://test-app.local';
        $this->mobile = ''; //optional
        $this->email = ''; //optional
        $this->reference = ''; //optional

        $this->subscriptionID = '431'; // will come from request status
        $this->cancelRequestUrl = '';
    }

    /**
     * @test
     * @throws \JsonException
     * if the subscription is cancelled run it_gives_error_on_already_canceled_subscription(), otherwise this
     */
    final public function it_can_fetch_subscription_cancel_id(): void
    {
        $this->cancelRequestUrl = $this->serverUrl . '/api/v1.0/subscription/' . $this->subscriptionID . '/cancel'; //replace SERVER_URL & subscriptionID with value
        $cancelRequest = \Dotlines\GhooriSubscription\CancelRequest::getInstance($this->cancelRequestUrl, $this->accessToken);
        $cancelRequestResponse = $cancelRequest->send();

        self::assertNotEmpty($cancelRequestResponse);
        self::assertArrayHasKey('id', $cancelRequestResponse);
        self::assertArrayHasKey('subscriptionID', $cancelRequestResponse);
        self::assertArrayHasKey('requestID', $cancelRequestResponse);
        self::assertEquals('PROCESSING', $cancelRequestResponse['status']);
        self::assertEquals('00', $cancelRequestResponse['errorCode']);
        self::assertArrayHasKey('errorMessage', $cancelRequestResponse);

        self::assertNotEmpty($cancelRequestResponse['id']);
        self::assertNotEmpty($cancelRequestResponse['subscriptionID']);
        self::assertNotEmpty($cancelRequestResponse['requestID']);
        self::assertNotEmpty($cancelRequestResponse['status']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     * @throws \Exception
     */
    final public function it_gets_exception_with_empty_cancel_request_url(): void
    {
        $this->cancelRequestUrl = ""; //replace SERVER_URL & subscriptionID with value
        $cancelRequest = \Dotlines\GhooriSubscription\CancelRequest::getInstance($this->cancelRequestUrl, $this->accessToken);
        $this->expectException(Exception::class);
        $cancelRequest->send();
    }

    /**
     * @test
     * @throws Exception
     * @throws \JsonException
     */
    final public function it_gives_exception_with_wrong_cancel_request_url(): void
    {
        $this->cancelRequestUrl = "sadafafaf"; //replace SERVER_URL & subscriptionID with value
        $cancelRequest = \Dotlines\GhooriSubscription\CancelRequest::getInstance($this->cancelRequestUrl, $this->accessToken);
        $this->expectException(ConnectException::class);
        $cancelRequest->send();
    }

    /**
     * @test
     * @throws JsonException
     * @throws \JsonException
     */
    final public function it_gets_exception_with_invalid_subscriptionID(): void
    {
        $this->subscriptionID = "489";
        $this->cancelRequestUrl = $this->serverUrl . '/api/v1.0/subscription/' . $this->subscriptionID . '/cancel'; //replace SERVER_URL & subscriptionID with value
        $cancelRequest = \Dotlines\GhooriSubscription\CancelRequest::getInstance($this->cancelRequestUrl, $this->accessToken);
        $this->expectException(ClientException::class);
        $cancelRequest->send();
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     * @throws \Exception
     */
    final public function it_gives_error_on_already_canceled_subscription(): void
    {
        $this->cancelRequestUrl = $this->serverUrl . '/api/v1.0/subscription/' . $this->subscriptionID . '/cancel'; //replace SERVER_URL & subscriptionID with value
        $cancelRequest = \Dotlines\GhooriSubscription\CancelRequest::getInstance($this->cancelRequestUrl, $this->accessToken);
        $cancelRequestResponse = $cancelRequest->send();
        self::assertArrayNotHasKey('id', $cancelRequestResponse);
        self::assertArrayNotHasKey('requestID', $cancelRequestResponse);
        self::assertArrayNotHasKey('status', $cancelRequestResponse);
        self::assertNotEquals('00', $cancelRequestResponse['errorCode']);
        self::assertStringContainsStringIgnoringCase('Invalid Parameter', $cancelRequestResponse['errorMessage']);
    }
}
