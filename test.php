<?php

require __DIR__ . '/vendor/autoload.php';

$bucket = 'huangyi-static';
$endpoint = 'oss-cn-beijing.aliyuncs.com';
$accessKeyId = 'LTAIZQxCdU2JDE9s';
$accessKeySecret = 'sxhTuarYlL6xWhAWKFXLVpBXxs0mtk';

$client = new \HuangYi\AliyunOss\OssClient($bucket, $endpoint, $accessKeyId, $accessKeySecret);

$contents = file_get_contents('/Users/huangyi/Desktop/test.jpeg');

$response = $client->object->putObject('/test.jpeg', $contents, [
    'headers' => [
        'Content-Type' => 'image/jpeg',
    ],
]);

var_export($response->getResponse()->getHeaders());

echo $response->getResponse()->getBody()->getContents();
