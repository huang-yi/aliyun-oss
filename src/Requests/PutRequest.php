<?php

namespace HuangYi\AliyunOss\Requests;

class PutRequest extends Request
{
    /**
     * Return the request method.
     *
     * @return string
     */
    public function method(): string
    {
        return 'PUT';
    }
}
