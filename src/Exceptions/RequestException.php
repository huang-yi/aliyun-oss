<?php

namespace HuangYi\AliyunOss\Exceptions;

use Psr\Http\Message\ResponseInterface;

class RequestException extends AliyunOssException
{
    /**
     * Response.
     *
     * @var \Psr\Http\Message\ResponseInterface
     */
    protected $response;

    /**
     * With response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return $this
     */
    public function withResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response instanceof ResponseInterface;
    }
}