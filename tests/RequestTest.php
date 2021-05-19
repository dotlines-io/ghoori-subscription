<?php /** @noinspection PhpUndefinedConstantInspection */
/** @noinspection PhpUnhandledExceptionInspection */

/** @noinspection PhpComposerExtensionStubsInspection */
/** @noinspection SpellCheckingInspection */
/** @noinspection MethodVisibilityInspection */

namespace Dotlines\GhooriSubscription\Tests;

use Carbon\Carbon;
use Dotlines\Ghoori\AccessTokenRequest;
use Dotlines\GhooriSubscription\Request;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use JsonException;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    protected $backupStaticAttributes = false;
    protected $runTestInSeparateProcess = false;

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

    final public function setUp(): void
    {
        parent::setUp();
        $accessTokenRequest = AccessTokenRequest::getInstance($this->tokenUrl, $this->username, $this->password, $this->clientID, $this->clientSecret);
        $tokenResponse = $accessTokenRequest->send();

        $this->accessToken = (string)$tokenResponse['access_token'];
        $this->subscriptionUrl = $this->serverUrl . '/api/v1.0/subscribe';
        $this->package = 'BBC_Janala_Weekly1';
        $this->cycle = 'WEEKLY'; //possible values: DAILY, WEEKLY, FIFTEEN_DAYS, MONTHLY, THIRTY_DAYS, NINETY_DAYS, ONE_EIGHTY_DAYS
        $this->start = Carbon::now()->format('Y-m-d');
        $this->end = Carbon::now()->addYear()->format('Y-m-d');
        $this->userReturnUrl = 'https://test-app.local';
        $this->mobile = ''; //optional
        $this->email = ''; //optional
        $this->reference = ''; //optional
    }

    /**
     * @test
     * @throws JsonException
     */
    final public function it_can_fetch_charge_url(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();

        self::assertNotEmpty($subscriptionRequestResponse);
        self::assertArrayHasKey('url', $subscriptionRequestResponse, json_encode($subscriptionRequestResponse, JSON_THROW_ON_ERROR));
        self::assertArrayHasKey('invoiceID', $subscriptionRequestResponse, json_encode($subscriptionRequestResponse, JSON_THROW_ON_ERROR));
        self::assertArrayHasKey('errorCode', $subscriptionRequestResponse, json_encode($subscriptionRequestResponse, JSON_THROW_ON_ERROR));
        self::assertArrayHasKey('errorMessage', $subscriptionRequestResponse, json_encode($subscriptionRequestResponse, JSON_THROW_ON_ERROR));
        self::assertNotEmpty('url', (string)$subscriptionRequestResponse['url']);
        self::assertNotEmpty('invoiceID', (string)$subscriptionRequestResponse['invoiceID']);
        self::assertNotEmpty('errorCode', (string)$subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty('errorMessage', (string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_exception_with_empty_subscription_url(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->subscriptionUrl = "";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $this->expectException(Exception::class);
        $subscriptionRequest->send();
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_exception_with_wrong_subscription_url(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->subscriptionUrl = "adasdsadsadas";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $this->expectException(ConnectException::class);
        $subscriptionRequest->send();
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_with_duplicate_requestID(): void
    {
        $this->requestID = '1345';
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty($subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gives_error_with_empty_package_name(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->package = "";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty($subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gives_error_with_wrong_package_name(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->package = "assada";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty($subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gives_error_on_empty_cycle_input(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->cycle = "";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty($subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gives_error_on_invalid_cycle_input(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->cycle = "asdada";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gives_error_on_empty_start_date(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->start = "";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gives_error_on_wrong_format_start_date(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->start = "04-03-2021";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_on_start_date_before_current_date(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->start = "2021-05-10";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws JsonException
     * @throws Exception
     */
    final public function it_gives_error_on_empty_end_date(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->end = "";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_on_wrong_format_end_date(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->end = "04-03-2021";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_on_end_date_before_current_date(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->end = "2019-10-10";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_on_empty_userReturnUrl(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->userReturnUrl = "";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_on_wrong_format_userReturnUrl(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->userReturnUrl = "sssssd";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_on_less_than_11_digits_mobile_input(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->mobile = "1212";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertNotEmpty((string)$subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_on_non_integer_mobile_input(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->mobile = "sdsdsd";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertStringContainsStringIgnoringCase('Invalid Parameter.  The mobile must be a number', $subscriptionRequestResponse['errorMessage']);
    }

    /**
     * @test
     * @throws Exception
     */
    final public function it_gives_error_on_invalid_email_input(): void
    {
        $this->requestID = 'test-app-' . random_int(111111, 999999);
        $this->email = "sdsdsd";
        $subscriptionRequest = Request::getInstance($this->subscriptionUrl, $this->accessToken, $this->clientID, $this->requestID, $this->package, $this->cycle, $this->start, $this->end, $this->userReturnUrl, $this->mobile, $this->email, $this->reference);
        $subscriptionRequestResponse = $subscriptionRequest->send();
        self::assertArrayNotHasKey('url', $subscriptionRequestResponse);
        self::assertArrayNotHasKey('invoiceID', $subscriptionRequestResponse);
        self::assertNotEquals('00', $subscriptionRequestResponse['errorCode']);
        self::assertStringContainsStringIgnoringCase('Invalid Parameter.  The email must be a valid email address', $subscriptionRequestResponse['errorMessage']);
    }
}
