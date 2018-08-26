<?php

namespace HuangYi\AliyunOss\Resources;

use HuangYi\AliyunOss\Contracts\ResourceContract;
use HuangYi\AliyunOss\OssClient;

abstract class Resource implements ResourceContract
{
    /**
     * OSS Client.
     *
     * @var \HuangYi\AliyunOss\OssClient
     */
    protected $client;

    /**
     * Create a new Resource.
     *
     * @param \HuangYi\AliyunOss\OssClient $client
     * @return void
     */
    public function __construct(OssClient $client)
    {
        $this->client = $client;
    }
}