<?php


namespace Dotlines\GhooriSubscription\Tests;
use Dotlines\GhooriSubscription\Abstracts\ParameterlessRequest;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use PHPUnit\Framework\TestCase;

class ParameterlessRequestTest extends TestCase
{
    public string $serverUrl = 'https://sb-payments.ghoori.com.bd';
    public string $tokenUrl = 'https://sb-payments.ghoori.com.bd/oauth/token';
    public string $username = 'demo@gmail.com';
    public string $password = 'demo1234';
    public int $clientID = 39;
    public string $clientSecret = 'gS2ujsPALkQBakAoumes0pZrxm4y6Oktwggg07AB';

    public string $accessToken = "";
    public string $detailsRequestUrl = "";


    /**
     * @throws \JsonException
     */
    public function setUp(): void
    {
        parent::setUp();
        $accessTokenRequest = AccessTokenRequest::getInstance($this->tokenUrl, $this->username, $this->password, $this->clientID, $this->clientSecret);
        $tokenResponse = $accessTokenRequest->send();

        $this->accessToken = (string)$tokenResponse['access_token'];
        $this->detailsRequestUrl = $this->serverUrl.'/api/v1.0/subscription/432';

    }

    /**
     * @test
     * @throws \JsonException
     */
    final public function it_can_not_fetch_charge_url(): void
    {
        $requestObj = new class($this->detailsRequestUrl, $this->accessToken) extends ParameterlessRequest {};
        $subscriptionDetailsRequest = $requestObj::getInstance($this->detailsRequestUrl, $this->accessToken);
        $subscriptionDetailsRequestResponse = $subscriptionDetailsRequest->send();

        self::assertNotEmpty($subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('id', $subscriptionDetailsRequestResponse, json_encode($subscriptionDetailsRequestResponse));
        self::assertArrayHasKey('invoiceID', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('amount', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('cycle', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('enabled', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('createdDate', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('startDate', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('endDate', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('requestID', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('errorCode', $subscriptionDetailsRequestResponse);
        self::assertArrayHasKey('errorMessage', $subscriptionDetailsRequestResponse);


    }
}