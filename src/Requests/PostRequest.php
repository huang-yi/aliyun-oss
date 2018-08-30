<?php

namespace HuangYi\AliyunOss\Requests;

class PostRequest extends Request
{
    /**
     * Return the request method.
     *
     * @return string
     */
    public function method(): string
    {
        return 'POST';
    }
}
