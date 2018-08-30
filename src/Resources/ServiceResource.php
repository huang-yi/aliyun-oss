<?php

namespace HuangYi\AliyunOss\Resources;

use HuangYi\AliyunOss\Requests\Service\GetServiceRequest;
use HuangYi\AliyunOss\Responses\ArrayResponse;

class ServiceResource extends Resource
{
    /**
     * Get service. (List buckets)
     *
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\ArrayResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function getService(array $options): ArrayResponse
    {
        $request = new GetServiceRequest($this->client, $options);

        return ArrayResponse::make($request->request());
    }

    /**
     * List buckets. (Get service)
     *
     * @param array $options
     * @return \HuangYi\AliyunOss\Responses\ArrayResponse
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function listBuckets(array $options): ArrayResponse
    {
        return $this->getService($options);
    }
}