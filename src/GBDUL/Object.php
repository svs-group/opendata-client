<?php

namespace Svs\Client\OpenData\GBDUL;

use GuzzleHttp\Exception\BadResponseException;
use Svs\Client\OpenData\AbstractObject;

/**
 * Class Object
 *
 * @see     https://data.egov.kz/datasets/view?index=gbd_ul
 *
 * @package Svs\Client\OpenData\GBDUL
 */
class Object extends AbstractObject
{

    protected $version = 4;
    protected $indexName = 'gbd_ul';

    /**
     * @param string $bin
     *
     * @return array|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLegalEntityByBin(string $bin)
    {
        try {
            return $this
                ->client
                ->request(
                    'get',
                    $this->getUrl(),
                    [
                        'query' => array_merge(
                            $this->client->getConfig('query'),
                            [
                                'source' => $this
                                    ->boolSearchQuery()
                                    ->toString(),
                            ]
                        ),
                    ]
                );
        } catch (BadResponseException $exception) {
            return $exception->getResponse();
        }
    }
}
