<?php

namespace HuangYi\AliyunOss\Responses;

use HuangYi\AliyunOss\Contracts\ResponseContract;
use Psr\Http\Message\ResponseInterface;

/**
 * Response.
 *
 * @mixin \GuzzleHttp\Psr7\Response
 */
abstract class Response implements ResponseContract
{
    /**
     * Http Response.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * Transformed body.
     *
     * @var mixed
     */
    protected $transformedBody;

    /**
     * Make a new Response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return static
     */
    public static function make(ResponseInterface $response)
    {
        return new static($response);
    }

    /**
     * Create a new Response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return void
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Body format: raw, array
     *
     * @return string
     */
    abstract public function format();

    /**
     * Get response.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get raw body.
     *
     * @return string
     */
    public function getRawBody()
    {
        return $this->response->getBody()->getContents();
    }

    /**
     * Get body.
     *
     * @return mixed
     */
    public function getBody()
    {
        if ($this->transformedBody) {
            return $this->transformedBody;
        }

        if ($this->format() === 'array') {
            return $this->transformedBody = $this->xml2array($this->getRawBody());
        }

        return $this->getRawBody();
    }

    /**
     * Transform xml to array.
     *
     * @param string $xml
     * @return array
     */
    protected function xml2array($xml)
    {
        if (! $xml) {
            return [];
        }

        $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);

        return  json_decode(json_encode($xml), true);
    }

    /**
     * Call Response methods.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->response, $method], $arguments);
    }
}