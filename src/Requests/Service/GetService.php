<?php

namespace HuangYi\AliyunOss\Requests\Service;

use HuangYi\AliyunOss\Requests\Request;

class GetService extends Request
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
        return $this->getUrl(false);
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