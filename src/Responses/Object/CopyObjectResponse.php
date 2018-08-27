<?php

namespace HuangYi\AliyunOss\Responses\Object;

use HuangYi\AliyunOss\Responses\Response;

class CopyObjectResponse extends Response
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