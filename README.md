# Ghoori Subscription Composer Package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dotlines-io/ghoori-subscription.svg?style=flat-square)](https://packagist.org/packages/dotlines-io/ghoori-subscription)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/dotlines-io/ghoori-subscription/run-tests?label=tests)](https://github.com/dotlines-io/ghoori-subscription/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/dotlines-io/ghoori-subscription/Check%20&%20fix%20styling?label=code%20style)](https://github.com/dotlines-io/ghoori-subscription/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/dotlines-io/ghoori-subscription.svg?style=flat-square)](https://packagist.org/packages/dotlines-io/ghoori-subscription)

---

This composer package can be used for Subscription Payment integration with [Ghoori](http://ghoori.com.bd) Platform.
For the credentials, please contact with support@ghoori.com.bd or call 8809612332215

## Installation

You can install the package via composer:

```bash
composer require dotlines-io/ghoori-subscription
```

## Usage

```php
/**
 * ******************************************************
 * ******************* Token Fetching *******************
 * *********** Contact Ghoori For Credentials ***********
 * ******************************************************
 */
$tokenUrl = 'https://<SERVER_URL>/oauth/token';
$username = '';
$password = '';
$clientID = '';
$clientSecret = '';

$accessTokenRequest = \Dotlines\Ghoori\AccessTokenRequest::getInstance($tokenUrl, $username, $password, $clientID, $clientSecret);
$tokenResponse = $accessTokenRequest->send();
echo json_encode($tokenResponse) . '<br/>';

/**
 * Access Token Request Response looks like below:
 * {
 *  "token_type": "Bearer",
 *  "expires_in": 3600,
 *  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdW.....",
 *  "refresh_token": "def50200284b2371cad76b4d2a4e24746c44fd6a322....."
 * }
 */

/**
 * Access Token can be cached and reused for 1 hour
 * Before the end of accessToken lifetime every hour
 * you can use the refresh token to fetch new accessToken & refreshToken
 */
$accessToken = $tokenResponse['access_token'];
$refreshToken = $tokenResponse['refresh_token'];

/**
 * ******************************************************
 * ******************* Charge Request *******************
 * ******************************************************
 */
$subscriptionUrl = 'https://<SERVER_URL>/api/v1.0/subscribe';
$requestID = ''; //must be unique for each request
$package = ''; //must be pre-registered with Ghoori
$cycle = ''; //possible values: DAILY, WEEKLY, FIFTEEN_DAYS, MONTHLY, THIRTY_DAYS, NINETY_DAYS, ONE_EIGHTY_DAYS
$start = ''; //Format: 2020-04-15
$end = ''; //Format: 2020-04-15
$userReturnUrl = ''; //after payment, user will be redirected back to this URL
$mobile = ''; //optional
$email = ''; //optional
$reference = ''; //optional
$subscriptionRequest = \Dotlines\GhooriSubscription\Request::getInstance($subscriptionUrl, $accessToken, $clientID, $requestID, $package, $cycle, $start, $end, $userReturnUrl, $mobile, $email, $reference);
echo json_encode($subscriptionRequest->send()) . '<br/>';

/**
 * Subscription Request Response looks like below.
 * You must redirect the user to the "url" for payment.
 * {
 *  "url": "https://gateway.sbsubscription.pay.bka.sh/gateway/web/intent/R2G2TXVM",
 *  "invoiceID": "QT5899212E8380",
 *  "errorCode": "00",
 *  "errorMessage": "Operation Success"
 * }
 * Fail response only contains errorCode & errorMessage
 */

/**
 * ******************************************************
 * ******** Subscription Request Status Request *********
 * ******************************************************
 */
$requestStatusRequestUrl = 'https://<SERVER_URL>/api/v1.0/subscribe/<invoiceID>/status'; //replace SERVER_URL & invoiceID with value
$requestStatusRequest = \Dotlines\GhooriSubscription\RequestStatusRequest::getInstance($requestStatusRequestUrl, $accessToken);
echo json_encode($requestStatusRequest->send()) . '<br/>';

/**
 * Subscription Request Status Request Response looks like below:
 * {
 *  "invoiceID": "QT5899212E8380",
 *  "status": "SUCCEEDED",
 *  "subscriptionID": "414",
 *  "createdAt": "15-05-2021 04:10:21 PM",
 *  "errorCode": "00",
 *  "errorMessage": "Operation Successful"
 * }
 * Fail response only contains errorCode & errorMessage
 */
 
/**
 * ******************************************************
 * ************ Subscription Details Request ************
 * ******************************************************
 */
$detailsRequestUrl = 'https://<SERVER_URL>/api/v1.0/subscription/<subscriptionID>'; //replace SERVER_URL & subscriptionID with value
$detailsRequest = \Dotlines\GhooriSubscription\DetailsRequest::getInstance($detailsRequestUrl, $accessToken);
echo json_encode($detailsRequest->send()) . '<br/>';

/**
 * Subscription Details Request Response looks like below:
 * {
 *  "id": 414,
 *  "invoiceID": "QT5899212E8380",
 *  "amount": "25.00",
 *  "cycle": "WEEKLY",
 *  "enabled": "true",
 *  "createdDate": "2021-05-15",
 *  "startDate": "2021-06-22",
 *  "endDate": "2022-06-29",
 *  "requestID": "1272",
 *  "errorCode": "00",
 *  "errorMessage": "Operation Successful",
 * }
 * Fail response only contains errorCode & errorMessage
 */
 
/**
 * ******************************************************
 * ************ Subscription Cancel Request ************
 * ******************************************************
 */
$cancelRequestUrl = 'https://<SERVER_URL>/api/v1.0/subscription/<subscriptionID>/cancel'; //replace SERVER_URL & subscriptionID with value
$cancelRequest = \Dotlines\GhooriSubscription\CancelRequest::getInstance($cancelRequestUrl, $accessToken);
echo json_encode($cancelRequest->send()) . '<br/>';

/**
 * Subscription Details Cancel Response looks like below:
 * {
 *  "id": 11,
 *  "subscriptionID": 19,
 *  "requestID": 11,
 *  "status": "PROCESSING",
 *  "errorCode": "00",
 *  "errorMessage": "Operation Successful"
 * }
 * Fail response only contains errorCode & errorMessage
 */
 
/**
 * ******************************************************
 * ******** Subscription Cancel Details Request *********
 * ******************************************************
 */
$cancelDetailsRequestUrl = 'https://<SERVER_URL>/api/v1.0/subscription/cancel/<id>'; //replace SERVER_URL & id (cancel request id) with value
$cancelDetailsRequest = \Dotlines\GhooriSubscription\CancelDetailsRequest::getInstance($cancelDetailsRequestUrl, $accessToken);
echo json_encode($cancelDetailsRequest->send()) . '<br/>';

/**
 * Subscription Cancel Details Request Response looks like below:
 * {
 *  "id": 11,
 *  "subscriptionID": 19,
 *  "requestID": 11,
 *  "status": "PROCESSING",
 *  "errorCode": "00",
 *  "errorMessage": "Operation Successful"
 * }
 * Fail response only contains errorCode & errorMessage
 */
 
/**
 * ******************************************************
 * ********* Subscription Payment List Request **********
 * ******************************************************
 */
$paymentListRequestUrl = 'https://<SERVER_URL>/api/v1.0/subscription/<subscriptionID>/payments'; //replace SERVER_URL & id (cancel request id) with value
$paymentListRequest = \Dotlines\GhooriSubscription\PaymentListRequest::getInstance($paymentListRequestUrl, $accessToken);
echo json_encode($paymentListRequest->send()) . '<br/>';

/**
 * Subscription Payment List Request Response looks like below:
 * {
 *  "subscriptionPayments": [
 *      {
 *          "paymentId": 17,
 *          "cycle": "DAILY",
 *          "dueDate": "2020-03-30",
 *          "reverseTransactionAmount": "0.00",
 *          "reverseTransactionDate": "",
 *          "reverseTransactionId": "",
 *          "status": "SUCCEEDED_PAYMENT",
 *          "subscriptionId": "22",
 *          "transactionDate": "2020-03-30T13:24:13.720768Z",
 *          "transactionId": "7CU901YXQP"
 *      },
 *      {
 *          "paymentId": 20,
 *          "cycle": "DAILY",
 *          "dueDate": "2020-03-31",
 *          "reverseTransactionAmount": "0.00",
 *          "reverseTransactionDate": "",
 *          "reverseTransactionId": "",
 *          "status": "SUCCEEDED_PAYMENT",
 *          "subscriptionId": "22",
 *          "transactionDate": "2020-03-31T06:00:52.885636Z",
 *          "transactionId": "7CV301YYUT"
 *      }
 *  ],
 *  "errorCode": "00",
 *  "errorMessage": "Operation Successful"
 * }
 * Fail response only contains errorCode & errorMessage
 */
 
/**
 * ******************************************************
 * ******** Subscription Payment Details Request ********
 * ******************************************************
 */
$paymentDetailsRequestUrl = 'https://<SERVER_URL>/api/v1.0/subscription/payment/<id>'; //replace SERVER_URL & id (paymentId) with value
$paymentDetailsRequest = \Dotlines\GhooriSubscription\PaymentDetailsRequest::getInstance($paymentDetailsRequestUrl, $accessToken);
echo json_encode($paymentDetailsRequest->send()) . '<br/>';

/**
 * Subscription Payment Details Request Response looks like below:
 * {
 *  "paymentId": 18,
 *  "cycle": "WEEKLY",
 *  "dueDate": "2020-03-30",
 *  "reverseTransactionAmount": "0.00",
 *  "reverseTransactionDate": "",
 *  "reverseTransactionId": "",
 *  "status": "SUCCEEDED_PAYMENT",
 *  "subscriptionId": "23",
 *  "transactionDate": "2020-03-30T13:29:04.741416Z",
 *  "transactionId": "7CU901YXQZ",
 *  "errorCode": "00",
 *  "errorMessage": "Operation Successful"
 * }
 * Fail response only contains errorCode & errorMessage
 */
 
/**
 * ******************************************************
 * ******** Subscription Payment Refund Request ********
 * ******************************************************
 */
$paymentRefundRequestUrl = 'https://<SERVER_URL>/api/v1.0/subscription/payment/<id>/refund'; //replace SERVER_URL & id (paymentId) with value
$refund_amount = 20;
$paymentRefundRequest = \Dotlines\GhooriSubscription\PaymentRefundRequest::getInstance($paymentRefundRequestUrl, $accessToken, $refund_amount);
echo json_encode($paymentRefundRequest->send()) . '<br/>';

/**
 * Subscription Payment Refund Request Response looks like below:
 * {
 *  "requestID": 4,
 *  "amount": "2",
 *  "status": "SUCCEEDED",
 *  "errorCode": "00",
 *  "errorMessage": "Operation Successful"
 * }
 * Fail response only contains errorCode & errorMessage
 */
 
/**
 * ******************************************************
 * **** Subscription Payment Refund Details Request *****
 * ******************************************************
 */
$paymentRefundDetailsRequestUrl = 'https://<SERVER_URL>/api/v1.0/subscription/refund/<id>'; //replace SERVER_URL & id (refund requestID) with value
$paymentRefundDetailsRequest = \Dotlines\GhooriSubscription\PaymentRefundDetailsRequest::getInstance($paymentRefundDetailsRequestUrl, $accessToken);
echo json_encode($paymentRefundDetailsRequest->send()) . '<br/>';

/**
 * Subscription Payment Refund Details Request Response looks like below:
 * {
 *  "requestID": 4,
 *  "amount": "2",
 *  "status": "SUCCEEDED",
 *  "errorCode": "00",
 *  "errorMessage": "Operation Successful"
 * }
 * Fail response only contains errorCode & errorMessage
 */

/**
 * ******************************************************
 * ******************* Refresh Token *******************
 * ******************************************************
 */
$refreshTokenRequest = \Dotlines\Ghoori\RefreshTokenRequest::getInstance($tokenUrl, $accessToken, $clientID, $clientSecret, $refreshToken);
$tokenResponse = $refreshTokenRequest->send();
echo json_encode($tokenResponse) . '<br/>';

/**
 * Refresh Token Request Response looks like below:
 * {
 *  "token_type": "Bearer",
 *  "expires_in": 3600,
 *  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdW.....",
 *  "refresh_token": "def50200284b2371cad76b4d2a4e24746c44fd6a322....."
 * }
 */
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [TareqMahbub](https://github.com/TareqMahbub)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
