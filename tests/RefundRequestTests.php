<?php /** @noinspection SpellCheckingInspection */


namespace Dotlines\GhooriSubscription\Tests;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use Dotlines\GhooriSubscription\PaymentDetailsRequest;
use Dotlines\GhooriSubscription\PaymentListRequest;
use Dotlines\GhooriSubscription\PaymentRefundDetailsRequest;
use Dotlines\GhooriSubscription\PaymentRefundRequest;
use JsonException;
use PHPUnit\Framework\TestCase;

class RefundRequestTests extends TestCase
{
    protected $backupStaticAttributes = false;
    protected $runTestInSeparateProcess = false;

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
    final public function fetch_payment_list(bool $is_return = false): array
    {
        $paymentListRequestUrl = 'https://sb-payments.ghoori.com.bd/api/v1.0/subscription/310/payments'; //replace SERVER_URL & id (cancel request id) with value
        $paymentListRequest = PaymentListRequest::getInstance($paymentListRequestUrl, $this->fetch_access_token());
        $response = $paymentListRequest->send();
        if ($is_return) {
            return (array)$response['subscriptionPayments'];
        }

        self::assertNotEmpty($response);
        self::assertArrayHasKey('subscriptionPayments', $response);

        /** @var array $subscriptionPayment */
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

        return [];
    }

    /**
     * @test
     * @throws JsonException
     */
    final public function fetch_payment_details(): void
    {
        $subscriptionPayments = $this->fetch_payment_list(true);
        self::assertNotEmpty($subscriptionPayments);

        /** @var array $subscriptionPayment */
        foreach ($subscriptionPayments as $subscriptionPayment) {
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

            self::assertNotEmpty($paymentDetails['paymentId']);
            self::assertNotEmpty($paymentDetails['cycle']);
            self::assertNotEmpty($paymentDetails['dueDate']);
            self::assertNotEmpty($paymentDetails['reverseTransactionAmount']);
            self::assertNotEmpty($paymentDetails['reverseTransactionDate']);
            self::assertNotEmpty($paymentDetails['reverseTransactionId']);
            self::assertNotEmpty($paymentDetails['status']);
            self::assertNotEmpty($paymentDetails['subscriptionId']);
            self::assertNotEmpty($paymentDetails['transactionDate']);
            self::assertNotEmpty($paymentDetails['transactionId']);
        }
    }

    /**
     * @test
     * @throws JsonException
     */
    final public function send_refund_request(): void
    {
        $subscriptionPayments = $this->fetch_payment_list(true);
        self::assertNotEmpty($subscriptionPayments);

        /** @var array $subscriptionPayment */
        foreach ($subscriptionPayments as $subscriptionPayment) {
            $paymentRefundRequestUrl = "https://sb-payments.ghoori.com.bd/api/v1.0/subscription/payment/{$subscriptionPayment['paymentId']}/refund";
            $refund_amount = 1;
            $paymentRefundRequest = PaymentRefundRequest::getInstance($paymentRefundRequestUrl, $this->fetch_access_token(), $refund_amount);
            $response = $paymentRefundRequest->send();

            self::assertNotEmpty($response);

            if (Carbon::now()->diffInDays(new Carbon((string)$subscriptionPayment['transactionDate'])) >= 15) {
                self::assertArrayHasKey('errorCode', $response);
                self::assertArrayHasKey('errorMessage', $response);

                self::assertNotEmpty($response['errorCode']);
                self::assertNotEmpty($response['errorMessage']);
            } else {
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

                if (empty($this->refund_request_id)) {
                    $this->refund_request_id = (string)($response['requestID'] ?? '');
                }
            }
        }
    }

    /**
     * @test
     * @throws JsonException
     */
    final public function refund_request_details(): void
    {
        $paymentRefundDetailsRequestUrl = "https://sb-payments.ghoori.com.bd/api/v1.0/subscription/refund/$this->refund_request_id";
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

    /**
     * @throws JsonException
     */
    private function fetch_access_token(): string
    {
        $accessTokenRequest = AccessTokenRequest::getInstance($this->tokenUrl, $this->username, $this->password, $this->clientID, $this->clientSecret);
        $tokenResponse = $accessTokenRequest->send();

        return (string)$tokenResponse['access_token'];
    }
}
