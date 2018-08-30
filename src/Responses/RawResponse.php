<?php

namespace HuangYi\AliyunOss\Responses;

class RawResponse extends Response
{
    /**
     * Body format.
     *
     * @return string
     */
    public function format()
    {
        return 'raw';
    }
}
