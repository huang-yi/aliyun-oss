<?php

require __DIR__ . '/vendor/autoload.php';

$bucket = 'huangyi-static';
$endpoint = 'oss-cn-beijing.aliyuncs.com';
$accessKeyId = 'LTAIZQxCdU2JDE9s';
$accessKeySecret = 'sxhTuarYlL6xWhAWKFXLVpBXxs0mtk';

$client = new \HuangYi\AliyunOss\OssClient($bucket, $endpoint, $accessKeyId, $accessKeySecret);

$response = $client->object->getObject('/test.jpeg');

var_export($response->getHeaders());
echo "\n";
var_export($response->getBody());
