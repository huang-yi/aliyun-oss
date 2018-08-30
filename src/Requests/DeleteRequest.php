<?php

namespace HuangYi\AliyunOss\Requests;

class DeleteRequest extends Request
{
    /**
     * Return the request method.
     *
     * @return string
     */
    public function method(): string
    {
        return 'DELETE';
    }
}
