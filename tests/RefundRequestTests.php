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
     * @param bool $is_return
     * @return array
     * @throws JsonException
     */
    final public function fetch_payment_list($is_return = false): array {
        $paymentListRequestUrl = 'https://sb-payments.ghoori.com.bd/api/v1.0/subscription/310/payments'; //replace SERVER_URL & id (cancel request id) with value
        $paymentListRequest = PaymentListRequest::getInstance($paymentListRequestUrl, $this->fetch_access_token());
        $response = $paymentListRequest->send();
        if($is_return) return $response['subscriptionPayments'];

        self::assertNotEmpty($response);
        self::assertArrayHasKey('subscriptionPayments', $response);

        foreach ($response['subscriptionPayments'] as $subscriptionPayment){
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
        }
        return [];
    }

    /**
     * @test
     * @throws JsonException
     */
    final public function fetch_payment_details(): void{
        $subscriptionPayments = $this->fetch_payment_list(true);
        self::assertNotEmpty($subscriptionPayments);

        foreach ($subscriptionPayments as $subscriptionPayment){
            $paymentDetailsRequestUrl = "https://sb-payments.ghoori.com.bd/api/v1.0/subscription/payment/{$subscriptionPayment['paymentId']}";
            $paymentDetailsRequest = PaymentDetailsRequest::getInstance($paymentDetailsRequestUrl, $this->fetch_access_token());
            $paymentDetails = $paymentDetailsRequest->send();

            self::assertNotEmpty($paymentDetails);
            self::assertArrayHasKey('paymentId', $paymentDetails);
            self::assertArrayHasKey('cycle', $paymentDetails);
            self::assertArrayHasKey('dueDate', $paymentDetails);
            self::assertArrayHasKey('reverseTransactionAmount', $paymentDetails);
            self::assertArrayHasKey('reverseTransactionDate', $paymentDetails);
            self::assertArrayHasKey('reverseTransactionId', $paymentDetails);
            self::assertArrayHasKey('status', $paymentDetails);
            self::assertArrayHasKey('subscriptionId', $paymentDetails);
            self::assertArrayHasKey('transactionDate', $paymentDetails);
            self::assertArrayHasKey('transactionId', $paymentDetails);
        }

    }

    /**
     * @test
     * @throws JsonException
     */
    final public function send_refund_request(): void{
        $subscriptionPayments = $this->fetch_payment_list(true);
        self::assertNotEmpty($subscriptionPayments);

        foreach ($subscriptionPayments as $subscriptionPayment){
            $paymentRefundRequestUrl = "https://sb-payments.ghoori.com.bd/api/v1.0/subscription/payment/{$subscriptionPayment['paymentId']}/refund";
            $refund_amount = 1;
            $paymentRefundRequest = \Dotlines\GhooriSubscription\PaymentRefundRequest::getInstance($paymentRefundRequestUrl, $this->fetch_access_token(), $refund_amount);
            $response = $paymentRefundRequest->send();

            self::assertNotEmpty($response);

            if(Carbon::now()->diffInDays($subscriptionPayment['transactionDate']) >= 15){
                self::assertArrayHasKey('errorCode', $response);
                self::assertArrayHasKey('errorMessage', $response);

                self::assertNotEmpty($response['errorCode']);
                self::assertNotEmpty($response['errorMessage']);
            }else{
                self::assertArrayHasKey('requestID', $response);
                self::assertArrayHasKey('amount', $response);
                self::assertArrayHasKey('status', $response);
                self::assertArrayHasKey('errorCode', $response);
                self::assertArrayHasKey('errorMessage', $response);

                if(empty($this->refund_request_id))
                    $this->refund_request_id = $response['requestID'] ?? '';
            }
        }
    }

    /**
     * @test
     * @throws JsonException
     */
    final public function refund_request_details(): void{
        $paymentRefundDetailsRequestUrl = "https://sb-payments.ghoori.com.bd/api/v1.0/subscription/refund/{$this->refund_request_id}";
        $paymentRefundDetailsRequest = PaymentRefundDetailsRequest::getInstance($paymentRefundDetailsRequestUrl, $this->fetch_access_token());
        $response = $paymentRefundDetailsRequest->send();

        self::assertNotEmpty($response);

        self::assertArrayHasKey('requestID', $response);
        self::assertArrayHasKey('amount', $response);
        self::assertArrayHasKey('status', $response);
        self::assertArrayHasKey('errorCode', $response);
        self::assertArrayHasKey('errorMessage', $response);

        self::assertNotEmpty($response['requestID']);
        self::assertNotEmpty($response['amount']);
        self::assertNotEmpty($response['status']);
        self::assertNotEmpty($response['errorCode']);
        self::assertNotEmpty($response['errorMessage']);
    }

    private function fetch_access_token(): void
    {
        $accessTokenRequest = AccessTokenRequest::getInstance($this->tokenUrl, $this->username, $this->password, $this->clientID, $this->clientSecret);
        $tokenResponse = $accessTokenRequest->send();

        return $tokenResponse['access_token'];
    }

}