<?php

namespace Dotlines\GhooriSubscription\Tests;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use Dotlines\GhooriSubscription\Request;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use PHPUnit\Framework\TestCase;

class RequestStatusRequestTest extends TestCase
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

    public string $invoiceID = "";
    public string $requestStatusRequestUrl = "";

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

        $this->invoiceID = "QTCFB934BBA3C0";    //will come from subscription charge request
        $this->requestStatusRequestUrl = '';
    }

    /**
     * @test
     * @throws JsonException
     */
    final public function it_can_fetch_request_status_invoiceID(): void
    {
        $this->requestStatusRequestUrl = $this->serverUrl . '/api/v1.0/subscribe/' . $this->invoiceID . '/status'; //replace SERVER_URL & invoiceID with value
        $requestStatusRequest = \Dotlines\GhooriSubscription\RequestStatusRequest::getInstance($this->requestStatusRequestUrl, $this->accessToken);
        $requestStatusRequestResponse = $requestStatusRequest->send();

        self::assertNotEmpty($requestStatusRequestResponse);
        self::assertArrayHasKey('invoiceID', $requestStatusRequestResponse, json_encode($requestStatusRequestResponse));
        self::assertArrayHasKey('status', $requestStatusRequestResponse);
        self::assertArrayHasKey('subscriptionID', $requestStatusRequestResponse);
        self::assertArrayHasKey('createdAt', $requestStatusRequestResponse);
        self::assertEquals('00', $requestStatusRequestResponse['errorCode']);
        self::assertArrayHasKey('errorMessage', $requestStatusRequestResponse);

        self::assertNotEmpty($requestStatusRequestResponse['invoiceID']);
        self::assertNotEmpty($requestStatusRequestResponse['status']);
        self::assertNotEmpty($requestStatusRequestResponse['subscriptionID']);
        self::assertNotEmpty($requestStatusRequestResponse['createdAt']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gets_exception_with_empty_request_status_request_url(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->requestStatusRequestUrl = "";
        $requestStatusRequest = \Dotlines\GhooriSubscription\RequestStatusRequest::getInstance($this->requestStatusRequestUrl, $this->accessToken);
        $this->expectException(Exception::class);
        $requestStatusRequest->send();
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gets_exception_with_invalid_request_status_request_url(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->requestStatusRequestUrl = "sdsadsasa";
        $requestStatusRequest = \Dotlines\GhooriSubscription\RequestStatusRequest::getInstance($this->requestStatusRequestUrl, $this->accessToken);
        $this->expectException(ConnectException::class);
        $requestStatusRequest->send();
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gets_exception_with_empty_invoiceID(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->invoiceID = "";
        $this->requestStatusRequestUrl = $this->serverUrl . '/api/v1.0/subscribe/' . $this->invoiceID . '/status'; //replace SERVER_URL & invoiceID with value
        $requestStatusRequest = \Dotlines\GhooriSubscription\RequestStatusRequest::getInstance($this->requestStatusRequestUrl, $this->accessToken);
        $this->expectException(ClientException::class);
        $requestStatusRequest->send();
    }
}
