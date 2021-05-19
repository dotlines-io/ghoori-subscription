<?php

namespace Dotlines\GhooriSubscription\Tests;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use Dotlines\GhooriSubscription\Request;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use JsonException;
use PHPUnit\Framework\TestCase;

class DetailsRequestTest extends TestCase
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
    public string $subscriptionID = "";
    public string $detailsRequestUrl = "";

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

        $this->invoiceID = 'QTCFB934BBA3C0';      // will come from subscription charge request
        $this->subscriptionID = '426'; // will come from request status
        $this->detailsRequestUrl = '';
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_can_fetch_details_request_id(): void
    {
        $this->detailsRequestUrl = $this->serverUrl . '/api/v1.0/subscription/' . $this->subscriptionID; //replace SERVER_URL & subscriptionID with value
        $detailsRequest = \Dotlines\GhooriSubscription\DetailsRequest::getInstance($this->detailsRequestUrl, $this->accessToken);
        $detailsRequestResponse =  $detailsRequest->send();

        self::assertNotEmpty($detailsRequestResponse);
        self::assertArrayHasKey('id', $detailsRequestResponse);
        self::assertArrayHasKey('invoiceID', $detailsRequestResponse);
        self::assertArrayHasKey('amount', $detailsRequestResponse);
        self::assertArrayHasKey('cycle', $detailsRequestResponse);
        self::assertArrayHasKey('enabled', $detailsRequestResponse);
        self::assertArrayHasKey('createdDate', $detailsRequestResponse);
        self::assertArrayHasKey('startDate', $detailsRequestResponse);
        self::assertArrayHasKey('endDate', $detailsRequestResponse);
        self::assertArrayHasKey('requestID', $detailsRequestResponse);
        self::assertEquals('00', $detailsRequestResponse['errorCode']);
        self::assertArrayHasKey('errorMessage', $detailsRequestResponse);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gets_exception_with_empty_url(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->detailsRequestUrl = "";
        $detailsRequest = \Dotlines\GhooriSubscription\DetailsRequest::getInstance($this->detailsRequestUrl, $this->accessToken);
        $this->expectException(Exception::class);
        $detailsRequest->send();
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_exception_with_wrong_url(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->detailsRequestUrl = "sadasdas";
        $detailsRequest = \Dotlines\GhooriSubscription\DetailsRequest::getInstance($this->detailsRequestUrl, $this->accessToken);
        $this->expectException(ConnectException::class);
        $detailsRequest->send();
    }

    /**
     * @test
     * @throws JsonException
     */
    final public function it_gets_exception_with_invalid_subscriptionID(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->subscriptionID = "NA";
        $this->detailsRequestUrl = $this->serverUrl . '/api/v1.0/subscription/' . $this->subscriptionID; //replace SERVER_URL & subscriptionID with value
        $detailsRequest = \Dotlines\GhooriSubscription\DetailsRequest::getInstance($this->detailsRequestUrl, $this->accessToken);
        $this->expectException(ClientException::class);
        $detailsRequest->send();
    }
}
