<?php

namespace HuangYi\AliyunOss\Resources;

use HuangYi\AliyunOss\Requests\Object\CopyObjectRequest;
use HuangYi\AliyunOss\Requests\Object\PutObjectRequest;
use HuangYi\AliyunOss\Responses\Object\CopyObjectResponse;
use HuangYi\AliyunOss\Responses\Object\PutObjectResponse;

class ObjectResource extends Resource
{
    /**
     * Put object.
     *
     * @param string $path
     * @param string $contents
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\Object\PutObjectResponse
     */
    public function putObject(string $path, string $contents, array $options = [])
    {
        $request = PutObjectRequest::make($this->client);

        $request->setPath($path);

        if (isset($options['headers'])) {
            $request->setHeader($options['headers']);
        }

        $request->setHeader('Content-Length', strlen($contents));

        $request->setBody($contents);

        return PutObjectResponse::make($request->request());
    }

    /**
     * Copy object.
     *
     * @param string $fromBucket
     * @param string $fromPath
     * @param string $newPath
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\Object\CopyObjectResponse
     */
    public function copyObject(string $fromBucket, string $fromPath, string $newPath, array $options = [])
    {
        $request = CopyObjectRequest::make($this->client);

        $request->setPath($newPath);

        if (isset($options['headers'])) {
            $request->setHeader($options['headers']);
        }

        $fromBucket = trim($fromBucket, '/');
        $fromPath = trim($fromPath, '/');

        $request->setHeader('x-oss-copy-source', '/' . $fromBucket . '/' . $fromPath);

        return CopyObjectResponse::make($request->request());
    }
}