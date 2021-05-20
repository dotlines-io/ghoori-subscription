<?php


namespace Dotlines\GhooriSubscription\Tests;

use Dotlines\Ghoori\AccessTokenRequest;

use Dotlines\GhooriSubscription\Abstracts\ParameterlessRequest;
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
    public string $subscriptionUrl = "";


    /**
     * @throws \JsonException
     */
    public function setUp(): void
    {
        parent::setUp();
        $accessTokenRequest = AccessTokenRequest::getInstance($this->tokenUrl, $this->username, $this->password, $this->clientID, $this->clientSecret);
        $tokenResponse = $accessTokenRequest->send();

        $this->accessToken = (string)$tokenResponse['access_token'];
        $this->subscriptionUrl = $this->serverUrl . '/api/v1.0/subscribe';
    }

    /**
     * @test
     * @throws \JsonException
     */
    final public function it_can_not_fetch_charge_url(): void
    {
        $requestObj = new class($this->subscriptionUrl, $this->accessToken) extends ParameterlessRequest {
        };
        $subscriptionRequest = $requestObj->getInstance($this->subscriptionUrl, $this->accessToken);
        $subscriptionRequestResponse = $subscriptionRequest->send();

        self::assertNotEmpty($subscriptionRequestResponse);
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse, json_encode($subscriptionRequestResponse));
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse, json_encode($subscriptionRequestResponse));
        self::assertArrayHasKey('errorCode', $subscriptionRequestResponse, json_encode($subscriptionRequestResponse));
        self::assertArrayHasKey('errorMessage', $subscriptionRequestResponse, json_encode($subscriptionRequestResponse));
        self::assertNotEmpty('errorCode', (string)$subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty('errorMessage', (string)$subscriptionRequestResponse['errorMessage']);
        self::assertNotEquals('00', (string)$subscriptionRequestResponse['errorCode']);
    }
}
