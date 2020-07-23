<?php

namespace Svs\Client\OpenData;

use GuzzleHttp;

/**
 * Class AbstractClient
 */
class AbstractClient
{

    /**
     * @var
     */
    protected $client;

    /**
     * @var string
     */
    protected $apiKey = '';

    /**
     * AbstractClient constructor.
     *
     * @param string $apiKey
     *
     * @throws \Exception
     */
    public function __construct($apiKey = '')
    {
        if (!$apiKey) {
            throw new \Exception("Api key must cannot be empty");
        }

        $this
            ->setApiKey($apiKey)
            ->setClient()
        ;
    }

    /**
     * @param string $apiKey
     *
     * @return \Svs\Client\OpenData\AbstractClient
     */
    public function setApiKey(string $apiKey = ''): AbstractClient
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return \Svs\Client\OpenData\AbstractClient
     */
    private function setClient()
    {
        $this->client = new GuzzleHttp\Client(
            [
                'base_uri' => 'https://data.egov.kz',
                'verify'   => false,
                'query'    => [
                    'apiKey' => $this->getApiKey(),
                ],
            ]
        );

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        $className = sprintf("%s\%s\Data", __NAMESPACE__, strtoupper($key));

        if (!class_exists($className)) {
            throw new \BadFunctionCallException(sprintf("%s class doesn't exists", $className));
        }

        return new $className($this->client);
    }
}
