<?php

namespace HuangYi\AliyunOss;

use HuangYi\AliyunOss\Exceptions\ResourceNotExistsException;
use HuangYi\AliyunOss\Resources\BucketResource;
use HuangYi\AliyunOss\Resources\ObjectResource;
use HuangYi\AliyunOss\Resources\ServiceResource;

/**
 * OssClient.
 *
 * @property \HuangYi\AliyunOss\Resources\ServiceResource $service
 * @property \HuangYi\AliyunOss\Resources\BucketResource $bucket
 * @property \HuangYi\AliyunOss\Resources\ObjectResource $object
 */
class OssClient
{
    /**
     * OSS bucket.
     *
     * @var string
     */
    protected $bucketName;

    /**
     * OSS endpoint.
     *
     * @var string
     */
    protected $endpoint;

    /**
     * Access key id.
     *
     * @var string
     */
    protected $accessKeyId;

    /**
     * Access key secret.
     *
     * @var string
     */
    protected $accessKeySecret;

    /**
     * Whether to use HTTPS.
     *
     * @var bool
     */
    protected $isSecure = true;

    /**
     * Resources.
     *
     * @var \HuangYi\AliyunOss\Contracts\ResourceContract[]
     */
    protected $resources = [];

    /**
     * Resources map.
     *
     * @var array
     */
    protected $resourcesMap = [
        'service' => ServiceResource::class,
        'bucket' => BucketResource::class,
        'object' => ObjectResource::class,
    ];

    /**
     * Create a new OssClient.
     *
     * @param string $bucketName
     * @param string $endpoint
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @return void
     */
    public function __construct(string $bucketName, string $endpoint, string $accessKeyId, string $accessKeySecret)
    {
        $this->bucketName = $bucketName;
        $this->endpoint = $endpoint;
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
    }

    /**
     * Get resource.
     *
     * @param string $resource
     * @return \HuangYi\AliyunOss\Contracts\ResourceContract
     * @throws \HuangYi\AliyunOss\Exceptions\ResourceNotExistsException
     */
    public function getResource($resource)
    {
        if (! array_key_exists($resource, $this->resourcesMap)) {
            throw new ResourceNotExistsException('Resource');
        }

        if (! isset($this->resources[$resource])) {
            $this->resources[$resource] = $this->createResource($resource);
        }

        return $this->resources[$resource];
    }

    /**
     * Create resource.
     *
     * @param string $resource
     * @return \HuangYi\AliyunOss\Contracts\ResourceContract
     */
    protected function createResource($resource)
    {
        $class = $this->resourcesMap[$resource];

        return new $class($this);
    }

    /**
     * @return string
     */
    public function getBucketName(): string
    {
        return $this->bucketName;
    }

    /**
     * @param string $bucketName
     * @return $this
     */
    public function setBucketName(string $bucketName)
    {
        $this->bucket = $bucketName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     * @return $this
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessKeyId(): string
    {
        return $this->accessKeyId;
    }

    /**
     * @param string $accessKeyId
     * @return $this
     */
    public function setAccessKeyId(string $accessKeyId)
    {
        $this->accessKeyId = $accessKeyId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccessKeySecret(): string
    {
        return $this->accessKeySecret;
    }

    /**
     * @param string $accessKeySecret
     * @return $this
     */
    public function setAccessKeySecret(string $accessKeySecret)
    {
        $this->accessKeySecret = $accessKeySecret;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->isSecure;
    }

    /**
     * @param bool $isSecure
     * @return $this
     */
    public function setIsSecure(bool $isSecure)
    {
        $this->isSecure = $isSecure;

        return $this;
    }

    /**
     * Get resource.
     *
     * @param string $resource
     * @return \HuangYi\AliyunOss\Contracts\ResourceContract
     * @throws \HuangYi\AliyunOss\Exceptions\ResourceNotExistsException
     */
    public function __get($resource)
    {
        return $this->getResource($resource);
    }
}