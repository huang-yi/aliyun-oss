<?php

namespace HuangYi\AliyunOss\Responses;

use HuangYi\AliyunOss\Contracts\ResponseContract;
use Psr\Http\Message\ResponseInterface;

abstract class Response implements ResponseContract
{
    /**
     * Raw response.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

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
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}