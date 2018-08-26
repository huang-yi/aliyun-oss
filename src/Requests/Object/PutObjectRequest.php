<?php

namespace HuangYi\AliyunOss\Requests\Object;

use HuangYi\AliyunOss\Requests\Request;

class PutObjectRequest extends Request
{
    /**
     * Return the request method.
     *
     * @return string
     */
    public function method() : string
    {
        return 'PUT';
    }

    /**
     * Return the request URL.
     *
     * @return string
     */
    public function url() : string
    {
        return $this->getUrl();
    }

    /**
     * Return the request options.
     *
     * @return array
     */
    public function options() : array
    {
        return [];
    }
}