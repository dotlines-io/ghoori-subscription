<?php


namespace Dotlines\GhooriSubscription\Tests;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use Dotlines\GhooriSubscription\PaymentDetailsRequest;
use Dotlines\GhooriSubscription\PaymentListRequest;
use Dotlines\GhooriSubscription\PaymentRefundDetailsRequest;
use JsonException;
use PHPUnit\Framework\TestCase;

class RefundRequestTests extends TestCase
{
    public string $serverUrl = 'https://sb-payments.ghoori.com.bd';
    public string $tokenUrl = 'https://sb-payments.ghoori.com.bd/oauth/token';
    public string $username = 'demo@gmail.com';
    public string $password = 'demo1234';
    public int $clientID = 39;
    public string $clientSecret = 'gS2ujsPALkQBakAoumes0pZrxm4y6Oktwggg07AB';

    public string $refund_request_id = "";

    /**
     * @test
     */
    final public function fetch_payment_list(): array
    {
        $paymentListRequestUrl = 'https://sb-payments.ghoori.com.bd/api/v1.0/subscription/310/payments'; //replace SERVER_URL & id (cancel request id) with value
        $paymentListRequest = PaymentListRequest::getInstance($paymentListRequestUrl, $this->fetch_access_token());
        $response = $paymentListRequest->send();
        /*if ($is_return) {
            return $response['subscriptionPayments'];
        }*/

        self::assertNotEmpty($response);
        self::assertArrayHasKey('subscriptionPayments', $response);

        foreach ($response['subscriptionPayments'] as $subscriptionPayment) {
            self::assertArrayHasKey('paymentId', $subscriptionPayment);
            self::assertArrayHasKey('cycle', $subscriptionPayment);
            self::assertArrayHasKey('dueDate', $subscriptionPayment);
            self::assertArrayHasKey('reverseTransactionAmount', $subscriptionPayment);
            self::assertArrayHasKey('reverseTransactionDate', $subscriptionPayment);
            self::assertArrayHasKey('reverseTransactionId', $subscriptionPayment);
            self::assertArrayHasKey('status', $subscriptionPayment);
            self::assertArrayHasKey('subscriptionId', $subscriptionPayment);
            self::assertArrayHasKey('transactionDate', $subscriptionPayment);
            self::assertArrayHasKey('transactionId', $subscriptionPayment);


            self::assertNotEmpty($subscriptionPayment['paymentId']);
            self::assertNotEmpty($subscriptionPayment['cycle']);
            self::assertNotEmpty($subscriptionPayment['dueDate']);
            self::assertNotEmpty($subscriptionPayment['reverseTransactionAmount']);
            self::assertNotEmpty($subscriptionPayment['reverseTransactionDate']);
            self::assertNotEmpty($subscriptionPayment['reverseTransactionId']);
            self::assertNotEmpty($subscriptionPayment['status']);
            self::assertNotEmpty($subscriptionPayment['subscriptionId']);
            self::assertNotEmpty($subscriptionPayment['transactionDate']);
            self::assertNotEmpty($subscriptionPayment['transactionId']);
        }

        //return [];
    }


    private function fetch_access_token(): void
    {
        $accessTokenRequest = AccessTokenRequest::getInstance($this->tokenUrl, $this->username, $this->password, $this->clientID, $this->clientSecret);
        $tokenResponse = $accessTokenRequest->send();

        return $tokenResponse['access_token'];
    }
}
