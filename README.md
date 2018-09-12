# Aliyun OSS

This package provides an unofficial Aliyun OSS SDK for PHP.

## Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use HuangYi\AliyunOss\OssClient;

$bucketName = 'bucket';
$endpoint = 'oss-cn-hangzhou.aliyuncs.com';
$accessKeyId = 'access_key_id';
$accessKeySecret = 'access_key_secret';

$client = new OssClient($bucketName, $endpoint, $accessKeyId, $accessKeySecret);

// Service APIs
$client->service->getService();

// Bucket APIs
$client->bucket->getBucket();

// Object APIs
$client->object->putObject('new-object', 'contents');

```
