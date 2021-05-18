<?php


namespace Dotlines\GhooriSubscription;

use Dotlines\Core\Request as RequestParent;

class Request extends RequestParent
{
    private int $clientID;
    private string $requestID;
    private string $package;
    private string $cycle;
    private string $start;
    private string $end;
    private string $userReturnUrl;
    private string $mobile;
    private string $email;
    private string $reference;

    /**
     * SubscriptionRequest constructor.
     *
     * @param string $url
     * @param string $accessToken
     * @param int $clientID
     * @param string $requestID
     * @param string $package
     * @param string $cycle
     * @param string $start
     * @param string $end
     * @param string $userReturnUrl
     * @param string $mobile optional
     * @param string $email optional
     * @param string $reference optional
     *
     * @return Request
     */
    public static function getInstance(string $url, string $accessToken, int $clientID, string $requestID, string $package, string $cycle, string $start, string $end, string $userReturnUrl, string $mobile = '', string $email = '', string $reference = ''): Request
    {
        return new Request($url, $accessToken, $clientID, $requestID, $package, $cycle, $start, $end, $userReturnUrl, $mobile, $email, $reference);
    }

    private function __construct(string $url, string $accessToken, int $clientID, string $requestID, string $package, string $cycle, string $start, string $end, string $userReturnUrl, string $mobile = '', string $email = '', string $reference = '')
    {
        $this->requestMethod = 'POST';
        $this->url = $url;
        $this->accessToken = $accessToken;

        $this->clientID = $clientID;
        $this->requestID = $requestID;
        $this->package = $package;
        $this->cycle = $cycle;
        $this->start = $start;
        $this->end = $end;
        $this->userReturnUrl = $userReturnUrl;
        $this->mobile = $mobile;
        $this->email = $email;
        $this->reference = $reference;
    }

    final public function params(): array
    {
        $params = [
            'clientID' => $this->clientID,
            'requestID' => $this->requestID,
            'package' => $this->package,
            'cycle' => $this->cycle,
            'start' => $this->start,
            'end' => $this->end,
            'userReturnURL' => $this->userReturnUrl,
        ];

        if (! empty($this->mobile)) {
            $params['mobile'] = $this->mobile;
        }
        if (! empty($this->email)) {
            $params['email'] = $this->email;
        }
        if (! empty($this->reference)) {
            $params['reference'] = $this->reference;
        }

        return $params;
    }
}
