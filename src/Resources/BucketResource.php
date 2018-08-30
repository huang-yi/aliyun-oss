<?php

namespace HuangYi\AliyunOss\Resources;

use HuangYi\AliyunOss\Requests\GetRequest;
use HuangYi\AliyunOss\Responses\ArrayResponse;

class BucketResource extends Resource
{
    /**
     * Get bucket. (List objects)
     *
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\ArrayResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function getBucket(array $options = []): ArrayResponse
    {
        $request = GetRequest::make($this->client, $options);

        return ArrayResponse::make($request->request());
    }

    /**
     * List objects. (Get bucket)
     *
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\ArrayResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function listObjects(array $options = []): ArrayResponse
    {
        return $this->getBucket($options);
    }
}