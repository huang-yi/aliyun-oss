<?php

namespace HuangYi\AliyunOss\Resources;

use HuangYi\AliyunOss\Exceptions\MethodNotSupportedException;
use HuangYi\AliyunOss\Requests\DeleteRequest;
use HuangYi\AliyunOss\Requests\GetRequest;
use HuangYi\AliyunOss\Requests\HeadRequest;
use HuangYi\AliyunOss\Requests\PostRequest;
use HuangYi\AliyunOss\Requests\PutRequest;
use HuangYi\AliyunOss\Responses\ArrayResponse;
use HuangYi\AliyunOss\Responses\RawResponse;

class ObjectResource extends Resource
{
    /**
     * Put object.
     *
     * @param string $path
     * @param string $contents
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function putObject(string $path, string $contents, array $options = [])
    {
        $request = PutRequest::make($this->client, $options);

        $request->setPath($path);
        $request->setHeader('Content-Length', strlen($contents));
        $request->setBody($contents);

        return RawResponse::make($request->request());
    }

    /**
     * Copy object.
     *
     * @param string $fromPath
     * @param string $newPath
     * @param string $fromBucket
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\ArrayResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function copyObject(string $fromPath, string $newPath, string $fromBucket = null, array $options = [])
    {
        $request = PutRequest::make($this->client, $options);

        $request->setPath($newPath);

        $fromBucket = $fromBucket ? trim($fromBucket, '/') : $this->client->getBucketName();
        $fromPath = trim($fromPath, '/');

        $request->setHeader('x-oss-copy-source', '/' . $fromBucket . '/' . $fromPath);

        return ArrayResponse::make($request->request());
    }

    /**
     * Copy object.
     *
     * @param string $path
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function getObject(string $path, array $options = [])
    {
        $request = GetRequest::make($this->client, $options);

        $request->setPath($path);

        return RawResponse::make($request->request());
    }

    /**
     * Append object.
     *
     * @param string $path
     * @param int $position
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function appendObject(string $path, int $position = 0, array $options = [])
    {
        $request = PostRequest::make($this->client, $options);

        $request->setPath($path);
        $request->setSubResource('append');
        $request->setQuery('position', $position);

        return RawResponse::make($request->request());
    }

    /**
     * Delete object.
     *
     * @param string $path
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function deleteObject(string $path, array $options = [])
    {
        $request = DeleteRequest::make($this->client, $options);

        $request->setPath($path);

        return RawResponse::make($request->request());
    }

    /**
     * Delete multiple objects.
     *
     * @param array $paths
     * @param bool $quite
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\ArrayResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function deleteMultipleObjects(array $paths, bool $quite = true, array $options = [])
    {
        $request = PostRequest::make($this->client, $options);

        $quite = $quite ? 'true' : 'false';
        $objects = '<Object><Key>' . implode('</Key></Object><Object><Key>', $paths) . '</Key></Object>';
        $body = sprintf('<?xml version="1.0" encoding="UTF-8"?><Delete><Quiet>%s</Quiet>%s</Delete>', $quite, $objects);

        $request->setBody($body);
        $request->setSubResource('delete');

        return ArrayResponse::make($request->request());
    }

    /**
     * Head object.
     *
     * @param string $path
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function headObject(string $path, array $options = [])
    {
        $request = HeadRequest::make($this->client, $options);

        $request->setPath($path);

        return RawResponse::make($request->request());
    }

    /**
     * Get object meta.
     *
     * @param string $path
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function getObjectMeta(string $path, array $options = [])
    {
        // $request = GetRequest::make($this->client, $options);

        // $request->setPath($path);
        // $request->setSubResource('objectMeta');

        // return RawResponse::make($request->request());

        return $this->headObject($path, $options);
    }

    /**
     * Put object acl.
     *
     * @param string $path
     * @param string $permission
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function putObjectAcl(string $path, string $permission, array $options = [])
    {
        $request = PutRequest::make($this->client, $options);

        $request->setPath($path);
        $request->setSubResource('acl');
        $request->setHeader('x-oss-object-acl', $permission);

        return RawResponse::make($request->request());
    }

    /**
     * Get object acl.
     *
     * @param string $path
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\ArrayResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function getObjectAcl(string $path, array $options = [])
    {
        $request = GetRequest::make($this->client, $options);

        $request->setPath($path);
        $request->setSubResource('acl');

        return ArrayResponse::make($request->request());
    }

    /**
     * Post object.
     *
     * @param string $path
     * @param array $options
     * @return void
     * @throws \HuangYi\AliyunOss\Exceptions\MethodNotSupportedException
     */
    public function postObject(string $path, array $options = [])
    {
        throw new MethodNotSupportedException("Method [PostObject] is not supported.");
    }

    /**
     * Callback.
     *
     * @return void
     * @throws \HuangYi\AliyunOss\Exceptions\MethodNotSupportedException
     */
    public function callback()
    {
        throw new MethodNotSupportedException("Method [Callback] is not supported.");
    }

    /**
     * Put symlink.
     *
     * @param string $path
     * @param string $target
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function putSymlink(string $path, string $target, array $options = [])
    {
        $request = PutRequest::make($this->client, $options);

        $request->setPath($path);
        $request->setSubResource('symlink');
        $request->setHeader('x-oss-symlink-target', $target);

        return RawResponse::make($request->request());
    }

    /**
     * Get symlink.
     *
     * @param string $path
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function getSymlink(string $path, array $options = [])
    {
        $request = GetRequest::make($this->client, $options);

        $request->setPath($path);
        $request->setSubResource('symlink');

        return RawResponse::make($request->request());
    }

    /**
     * Restore object.
     *
     * @param string $path
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\RawResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function restoreObject(string $path, array $options = [])
    {
        $request = PostRequest::make($this->client, $options);

        $request->setPath($path);
        $request->setSubResource('restore');

        return RawResponse::make($request->request());
    }

    /**
     * Select object.
     *
     * @return void
     * @throws \HuangYi\AliyunOss\Exceptions\MethodNotSupportedException
     */
    public function selectObject()
    {
        throw new MethodNotSupportedException("Method [Select] is not supported.");
    }
}