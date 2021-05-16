<?php


namespace Dotlines\GhooriSubscription;

use Dotlines\Core\Request;

class PaymentRefundRequest extends Request
{
    private float $amount;

    /**
     * SubscriptionRequest constructor.
     *
     * @param string $url
     * @param string $accessToken
     * @param float $amount
     * @return PaymentRefundRequest
     */
    public static function getInstance(string $url, string $accessToken, float $amount): PaymentRefundRequest
    {
        return new PaymentRefundRequest($url, $accessToken, $amount);
    }

    private function __construct(string $url, string $accessToken, float $amount)
    {
        $this->requestMethod = 'POST';
        $this->url = $url;
        $this->accessToken = $accessToken;

        $this->amount = $amount;
    }

    final public function params(): array
    {
        return [
            'amount' => $this->amount,
        ];
    }
}
