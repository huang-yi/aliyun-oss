<?php

namespace HuangYi\AliyunOss\Requests;

class GetServiceRequest extends Request
{
    /**
     * Return the request method.
     *
     * @return string
     */
    public function method() : string
    {
        return 'GET';
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
}