<?php

namespace HuangYi\AliyunOss\Tests;

use HuangYi\AliyunOss\OssClient;
use PHPUnit\Framework\TestCase;

class ObjectTest extends TestCase
{
    use HasOssClient;

    public function testCreateOssClient()
    {
        $client = $this->createOssClient();

        $this->assertInstanceOf(OssClient::class, $client);
    }

    public function testPutObject()
    {
        $exceptBody = '';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->putObject('test.txt', 'test-contents');

        $this->assertEquals($exceptBody, $response->getBody());
    }

    public function testCopyObject()
    {
        $exceptBody = '<?xml version="1.0" encoding="UTF-8"?>'.
                      '<CopyObjectResult xmlns="http://doc.oss-cn-hangzhou.aliyuncs.com">'.
                         '<LastModified>Fri, 24 Feb 2012 07:18:48 GMT</LastModified>'.
                         '<ETag>"5B3C1A2E053D763E1B002CC607C5A0FE"</ETag>'.
                      '</CopyObjectResult>';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->copyObject('test.txt', 'test-contents');

        $this->assertEquals($this->xml2array($exceptBody), $response->getBody());
    }

    public function testGetObject()
    {
        $exceptBody = 'test-contens';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->getObject('test.txt');

        $this->assertEquals($exceptBody, $response->getBody());
    }

    public function testAppendObject()
    {
        $exceptBody = '';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->appendObject('test.txt', 'append-contents', 0);

        $this->assertEquals($exceptBody, $response->getBody());
    }

    public function testDeleteObject()
    {
        $exceptBody = '';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->deleteObject('test.txt');

        $this->assertEquals($exceptBody, $response->getBody());
    }

    public function testDeleteMultipleObjects()
    {
        // quite = true
        $exceptBody = '';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->deleteMultipleObjects(['test1.txt', 'test2.txt']);

        $this->assertEquals([], $response->getBody());

        // quite = false
        $exceptBody = '<?xml version="1.0" encoding="UTF-8"?>'.
                      '<Delete>'.
                          '<Quiet>true</Quiet>'.
                          '<Object><Key>test1.txt</Key></Object>'.
                          '<Object><Key>test2.txt</Key></Object>'.
                      '</Delete>';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->deleteMultipleObjects(['test1.txt', 'test2.txt'], false);

        $this->assertEquals($this->xml2array($exceptBody), $response->getBody());
    }

    public function testHeadObject()
    {
        $exceptBody = '';
        $exceptHeaders = [
            'x-oss-request-id' => ['559CC9BDC755F95A64485981'],
            'x-oss-object-type' => ['Normal'],
            'x-oss-storage-class' => ['Archive'],
            'Last-Modified' => ['Fri, 24 Feb 2012 06:07:48 GMT'],
            'ETag' => ['"fba9dede5f27731c9771645a39863328"'],
        ];

        $client = $this->createOssClientWithMockHttp($exceptBody, $exceptHeaders);

        $response = $client->object->headObject('test.txt');

        $this->assertEquals($exceptBody, $response->getBody());
        $this->assertEquals($exceptHeaders, $response->getHeaders());
    }

    public function testPutObjectAcl()
    {
        $exceptBody = '';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->putObjectAcl('test.txt', 'private');

        $this->assertEquals($exceptBody, $response->getBody());
    }

    public function testGetObjectAcl()
    {
        $exceptBody = '<?xml version="1.0" encoding="UTF-8"?>'.
            '<AccessControlPolicy>'.
            '<Owner><ID>00220120222</ID><DisplayName>00220120222</DisplayName></Owner>'.
            '<AccessControlList><Grant>private</Grant></AccessControlList>'.
            '</AccessControlPolicy>';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->getObjectAcl('test.txt');

        $this->assertEquals($this->xml2array($exceptBody), $response->getBody());
    }

    public function testPutSymlink()
    {
        $exceptBody = '';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->putSymlink('test.txt', 'test-symlink.txt');

        $this->assertEquals($exceptBody, $response->getBody());
    }

    public function testGetSymlink()
    {
        $exceptBody = '';
        $exceptHeaders = [
            'x-oss-symlink-target' => ['test.txt'],
        ];

        $client = $this->createOssClientWithMockHttp($exceptBody, $exceptHeaders);

        $response = $client->object->getSymlink('test-symlink.txt');

        $this->assertEquals($exceptBody, $response->getBody());
        $this->assertEquals($exceptHeaders, $response->getHeaders());
    }

    public function testRestoreObject()
    {
        $exceptBody = '';

        $client = $this->createOssClientWithMockHttp($exceptBody);

        $response = $client->object->restoreObject('test.txt');

        $this->assertEquals($exceptBody, $response->getBody());
    }
}
