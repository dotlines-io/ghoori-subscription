<?php

namespace Dotlines\GhooriSubscription\Tests;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;

class CancelDetailsRequestTest extends TestCase
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

    public string $cancelRequestID = "";
    public string $cancelDetailsRequestUrl = "";

    function setUp(): void
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

        $this->cancelRequestID = '184'; // will come from cancel request
        $this->cancelDetailsRequestUrl = '';
    }

    /**
     * @test
     * @throws \JsonException
     * if the subscription is cancelled run it_gives_error_on_already_canceled_subscription(), otherwise this
     */
    final public function it_can_fetch_subscription_cancel_id(): void
    {
        $this->cancelDetailsRequestUrl = $this->serverUrl . '/api/v1.0/subscription/cancel/' . $this->cancelRequestID; //replace SERVER_URL & id (cancel request id) with value
        $cancelDetailsRequest = \Dotlines\GhooriSubscription\CancelDetailsRequest::getInstance($this->cancelDetailsRequestUrl, $this->accessToken);
        $cancelDetailsRequestResponse = $cancelDetailsRequest->send();

        self::assertNotEmpty($cancelDetailsRequestResponse);
        self::assertArrayHasKey('id', $cancelDetailsRequestResponse, json_encode($cancelDetailsRequestResponse));
        self::assertArrayHasKey('subscriptionID', $cancelDetailsRequestResponse);
        self::assertArrayHasKey('requestID', $cancelDetailsRequestResponse);
        self::assertArrayHasKey('status', $cancelDetailsRequestResponse);
        self::assertEquals('00', $cancelDetailsRequestResponse['errorCode']);
        self::assertArrayHasKey('errorMessage', $cancelDetailsRequestResponse);

        self::assertNotEmpty($cancelDetailsRequestResponse['id']);
        self::assertNotEmpty($cancelDetailsRequestResponse['subscriptionID']);
        self::assertNotEmpty($cancelDetailsRequestResponse['requestID']);
        self::assertNotEmpty($cancelDetailsRequestResponse['status']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     * @throws \Exception
     */
    final public function it_gets_exception_with_empty_cancel_request_url(): void
    {
        $this->cancelDetailsRequestUrl = ""; //replace SERVER_URL & subscriptionID with value
        $cancelDetailsRequest = \Dotlines\GhooriSubscription\CancelDetailsRequest::getInstance($this->cancelDetailsRequestUrl, $this->accessToken);
        $this->expectException(Exception::class);
        $cancelDetailsRequest->send();
    }

    /**
     * @test
     * @throws Exception
     * @throws \JsonException
     */
    final public function it_gives_exception_with_wrong_cancel_request_url(): void
    {
        $this->cancelDetailsRequestUrl = "wrong url"; //replace SERVER_URL & subscriptionID with value
        $cancelDetailsRequest = \Dotlines\GhooriSubscription\CancelDetailsRequest::getInstance($this->cancelDetailsRequestUrl, $this->accessToken);
        $this->expectException(ConnectException::class);
        $cancelDetailsRequest->send();
    }

    /**
     * @test
     * @throws JsonException
     * @throws \JsonException
     */
    final public function it_gets_exception_with_invalid_cancelRequestID(): void
    {
        $this->cancelRequestID = "NA";
        $this->cancelDetailsRequestUrl = $this->serverUrl . '/api/v1.0/subscription/cancel/' . $this->cancelRequestID; //replace SERVER_URL & id (cancel request id) with value
        $cancelDetailsRequest = \Dotlines\GhooriSubscription\CancelDetailsRequest::getInstance($this->cancelDetailsRequestUrl, $this->accessToken);
        $this->expectException(ClientException::class);
        $cancelDetailsRequest->send();
    }
}
