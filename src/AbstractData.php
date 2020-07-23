<?php

namespace Svs\Client\OpenData;

use GuzzleHttp;

/**
 * Class AbstractObject
 *
 * @package Svs\Client\OpenData
 */
class AbstractData
{

    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var integer
     */
    protected $version;

    /**
     *
     */
    const DEFAULT_SIZE = 100;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array
     */
    protected $query = [];

    /**
     * AbstractObject constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $must
     * @param array $mustNot
     * @param array $should
     * @param array $shouldNot
     *
     * @return \Svs\Client\OpenData\AbstractData
     */
    protected function boolSearchQuery($must = [], $mustNot = [], $should = [], $shouldNot = [])
    {
        $this->query = [
            'size'  => self::DEFAULT_SIZE,
            'query' => [
                'filtered' => [
                    'query' => [
                        'bool' => [],
                    ],
                ],
            ],
        ];

        if (!empty($must)) {
            $this->query['query']['filtered']['query']['bool']['must'] = $must;
        }

        if (!empty($mustNot)) {
            $this->query['query']['filtered']['query']['bool']['must_not'] = $mustNot;
        }

        if (!empty($shouldNot)) {
            $this->query['query']['filtered']['query']['bool']['should_not'] = $shouldNot;
        }

        if (!empty($should)) {
            $this->query['query']['filtered']['query']['bool']['should'] = $should;
        }

        return $this;
    }

    /**
     * @return string
     */
    protected function toString(): string
    {
        return $this->minify(GuzzleHttp\json_encode($this->query));
    }

    /**
     * @param $json
     *
     * @return string
     * @see https://github.com/getify/JSON.minify original code
     */
    protected function minify($json): string
    {
        $tokenizer           = "/\"|(\/\*)|(\*\/)|(\/\/)|\n|\r/";
        $inString            = false;
        $inMultiLineComment  = false;
        $inSingleLineComment = false;
        $tmp                 = null;
        $tmp2                = null;
        $newString             = [];
        $from                = 0;
        $lc                  = null;
        $rc                  = null;
        $lastIndex           = 0;

        while (preg_match($tokenizer, $json, $tmp, \PREG_OFFSET_CAPTURE, $lastIndex)) {
            $tmp       = $tmp[0];
            $lastIndex = $tmp[1] + strlen($tmp[0]);
            $lc        = substr($json, 0, $lastIndex - strlen($tmp[0]));
            $rc        = substr($json, $lastIndex);

            if (!$inMultiLineComment && !$inSingleLineComment) {
                $tmp2 = substr($lc, $from);

                if (!$inString) {
                    $tmp2 = preg_replace("/(\n|\r|\s)*/", "", $tmp2);
                }

                $newString[] = $tmp2;
            }

            $from = $lastIndex;

            if ($tmp[0] == "\"" && !$inMultiLineComment && !$inSingleLineComment) {
                preg_match("/(\\\\)*$/", $lc, $tmp2);

                if (!$inString || !$tmp2 || (strlen(
                            $tmp2[0]
                        ) % 2) == 0) {
                    $inString = !$inString;
                }

                $from--;

                $rc = substr($json, $from);
            } else {
                if ($tmp[0] == "/*" && !$inString && !$inMultiLineComment && !$inSingleLineComment) {
                    $inMultiLineComment = true;
                } else {
                    if ($tmp[0] == "*/" && !$inString && $inMultiLineComment && !$inSingleLineComment) {
                        $inMultiLineComment = false;
                    } else {
                        if ($tmp[0] == "//" && !$inString && !$inMultiLineComment && !$inSingleLineComment) {
                            $inSingleLineComment = true;
                        } else {
                            if (
                                ($tmp[0] == "\n" || $tmp[0] == "\r") &&
                                !$inString &&
                                !$inMultiLineComment &&
                                $inSingleLineComment
                            ) {
                                $inSingleLineComment = false;
                            } else {
                                if (!$inMultiLineComment && !$inSingleLineComment && !(preg_match(
                                        "/\n|\r|\s/", $tmp[0]
                                    ))) {
                                    $new_str[] = $tmp[0];
                                }
                            }
                        }
                    }
                }
            }
        }

        $newString[] = $rc;

        return implode("", $newString);
    }

    /**
     * @return string
     */
    protected function getIndexName(): string
    {
        return $this->indexName;
    }

    /**
     * @return string
     */
    protected function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        return sprintf(
            '/api/v%d/%s/v1',
            $this->getVersion(),
            $this->getIndexName()
        );
    }
}
