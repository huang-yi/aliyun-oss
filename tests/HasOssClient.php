<?php

namespace HuangYi\AliyunOss\Tests;

use GuzzleHttp\Psr7\Response;
use HuangYi\AliyunOss\OssClient;

trait HasOssClient
{
    protected function createOssClientWithMockHttp($exceptBody = '', $exceptHeaders = [], $exceptHttpCode = 200)
    {
        $mockHttp = $this->mockObjectHttp($exceptBody, $exceptHeaders, $exceptHttpCode);

        $client = $this->createOssClient();

        $client->setHttp($mockHttp);

        return $client;
    }

    protected function createOssClient()
    {
        return new OssClient('bucket', 'endpoint', 'access_key_id', 'access_key_secret');
    }

    protected function mockObjectHttp($exceptBody = '', $exceptHeaders = [], $exceptHttpCode = 200)
    {
        $mockResponse = new Response($exceptHttpCode, $exceptHeaders, $exceptBody);

        $mockHttp = $this->getMockBuilder(Client::class)
            ->setMethods(['request'])
            ->getMock($mockResponse);

        $mockHttp->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        return $mockHttp;
    }

    protected function xml2array($xml)
    {
        if (! $xml) {
            return [];
        }

        $xml = simplexml_load_string($xml, "SimpleXMLElement", LIBXML_NOCDATA);

        return  json_decode(json_encode($xml), true);
    }
}
