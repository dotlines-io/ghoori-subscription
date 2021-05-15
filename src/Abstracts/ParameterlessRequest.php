<?php


namespace Dotlines\GhooriSubscription\Abstracts;

use Dotlines\Ghoori\Request;

abstract class ParameterlessRequest extends Request
{
    public static function getInstance(string $url, string $accessToken): ParameterlessRequest
    {
        return new static($url, $accessToken);
    }

    /**
     * ParameterlessRequest constructor.
     *
     * @param string $url
     * @param string $accessToken
     */
    private function __construct(string $url, string $accessToken)
    {
        $this->requestMethod = 'POST';
        $this->url = $url;
        $this->accessToken = $accessToken;
    }

    /**
     * @return array
     */
    final public function params(): array
    {
        return [];
    }
}
