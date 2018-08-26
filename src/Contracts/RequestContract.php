<?php

namespace HuangYi\AliyunOss\Contracts;

use Psr\Http\Message\ResponseInterface;

interface RequestContract
{
    /**
     * Send http request.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request() : ResponseInterface;
}