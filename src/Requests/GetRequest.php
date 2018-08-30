<?php

namespace HuangYi\AliyunOss\Requests;

class GetRequest extends Request
{
    /**
     * Return the request method.
     *
     * @return string
     */
    public function method(): string
    {
        return 'GET';
    }
}
