<?php

namespace HuangYi\AliyunOss\Requests;

class HeadRequest extends Request
{
    /**
     * Return the request method.
     *
     * @return string
     */
    public function method(): string
    {
        return 'HEAD';
    }
}
