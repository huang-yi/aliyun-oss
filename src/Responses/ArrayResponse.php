<?php

namespace HuangYi\AliyunOss\Responses;

class ArrayResponse extends Response
{
    /**
     * Body format.
     *
     * @return string
     */
    public function format()
    {
        return 'array';
    }
}
