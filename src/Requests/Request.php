<?php

namespace HuangYi\AliyunOss\Requests;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use HuangYi\AliyunOss\Contracts\RequestContract;
use HuangYi\AliyunOss\Exceptions\RequestException;
use HuangYi\AliyunOss\OssClient;
use Psr\Http\Message\ResponseInterface;

abstract class Request implements RequestContract
{
    /**
     * Request path.
     *
     * @var string
     */
    protected $path = '/';

    /**
     * Sub resource.
     *
     * @var string
     */
    protected $subResource;

    /**
     * request options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * OSS Client.
     *
     * @var \HuangYi\AliyunOss\OssClient
     */
    protected $client;

    /**
     * Http client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $http;

    /**
     * @var array
     */
    protected $beSignedQueryKeys = [
        'partNumber', 'uploadId', 'comp', 'status', 'startTime', 'endTime',
        'position', 'symlink', 'restore', 'x-oss-process',
        'response-content-type', 'response-content-language',
        'response-cache-control', 'response-content-encoding',
        'response-expires', 'response-content-disposition',
    ];

    /**
     * Make a new Request.
     *
     * @param \HuangYi\AliyunOss\OssClient $client
     * @param array $options
     * @param \GuzzleHttp\ClientInterface|null $http
     * @return static
     */
    public static function make(OssClient $client, array $options = [], ClientInterface $http = null)
    {
        return new static($client, $options, $http);
    }

    /**
     * Create a new Request.
     *
     * @param \HuangYi\AliyunOss\OssClient $client
     * @param array $options
     * @param \GuzzleHttp\ClientInterface $http
     * @return void
     */
    public function __construct(OssClient $client, array $options = [], ClientInterface $http = null)
    {
        $this->client = $client;
        $this->options = $options;
        $this->http = $http ?? new Client;
    }

    /**
     * Return the request method.
     *
     * @return string
     */
    abstract public function method(): string;

    /**
     * Send http request.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    public function request() : ResponseInterface
    {
        try {
            $response = $this->http->request(
                $this->method(),
                $this->url(),
                $this->options()
            );
        } catch (Exception $exception) {
            $this->handleException($exception);
        }

        return $response;
    }

    /**
     * Return the request URL.
     *
     * @return string
     */
    public function url(): string
    {
        return $this->getUrl();
    }

    /**
     * Get request url.
     *
     * @param bool $withBucket
     * @return string
     */
    public function getUrl(bool $withBucket = true): string
    {
        $scheme = $this->getScheme();
        $domain = $this->getDomain($withBucket);
        $path = $this->getPath();
        $queryStrings = $this->getQueryString();

        $url = $scheme . '://' . $domain . $path;

        if ($queryStrings) {
            $url .= '?' . $queryStrings;
        }

        return $url;
    }

    /**
     * Get scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->client->isSecure() ? 'https' : 'http';
    }

    /**
     * Get domain.
     *
     * @param bool $withBucket
     * @return string
     */
    public function getDomain(bool $withBucket = true)
    {
        $domain = $this->client->getEndpoint();

        if ($withBucket) {
            $domain = $this->client->getBucketName() . '.' . $domain;
        }

        return $domain;
    }

    /**
     * Get query string.
     *
     * @param bool $sign
     * @return string
     */
    public function getQueryString($sign = false)
    {
        $parts = [];

        if ($subResource = $this->getSubResource()) {
            $parts[] = $subResource;
        }

        if ($queries = $this->getQueries()) {
            if ($sign) {
                $queries = $this->filterSignQueries($queries);
            }

            $parts[] = http_build_query($queries, null, '&', PHP_QUERY_RFC3986);
        }

        return implode('&', $parts);
    }

    /**
     * @param array $queries
     * @return array
     */
    protected function filterSignQueries($queries)
    {
        $signQueries = [];

        foreach ($queries as $key => $value) {
            if (in_array($key, $this->beSignedQueryKeys)) {
                $signQueries[$key] = $value;
            }
        }

        ksort($signQueries);

        return $signQueries;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function options(): array
    {
        $this->options['headers'] = $this->getHeaders();

        unset($this->options['query']);

        return $this->options;
    }

    /**
     * Handle exception.
     *
     * @param \Exception $exception
     * @return void
     * @throws \HuangYi\AliyunOss\Exceptions\RequestException
     */
    protected function handleException(Exception $exception)
    {
        $requestException = new RequestException(
            $exception->getMessage(),
            $exception->getCode(),
            $exception
        );

        if ($exception instanceof BadResponseException) {
            $requestException->withResponse($exception->getResponse());
        }

        throw $requestException;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->path = '/' . ltrim($path, '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getSubResource()
    {
        return $this->subResource;
    }

    /**
     * @param string $subResource
     * @return $this
     */
    public function setSubResource(string $subResource)
    {
        $this->subResource = $subResource;

        return $this;
    }

    /**
     * @return array
     */
    public function getQueries(): array
    {
        return $this->options['query'] ?? [];
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setQuery($key, $value = null)
    {
        if (is_array($key)) {
            return $this->setQueries($key);
        }

        if (! isset($this->options['query'])) {
            $this->options['query'] = [];
        }

        $this->options['query'][$key] = $value;

        return $this;
    }

    /**
     * @param array $queries
     * @return $this
     */
    public function setQueries(array $queries)
    {
        if (! isset($this->options['query'])) {
            $this->options['query'] = [];
        }

        $this->options['query'] = array_merge($this->options['query'], $queries);

        return $this;
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = $this->options['headers'] ?? [];

        if (isset($this->options['body'])) {
            $headers['Content-Length'] = strlen($this->options['body']);
            $headers['Content-MD5'] = base64_encode(md5($this->options['body'], true));

            if (! isset($headers['Content-Type'])) {
                $headers['Content-Type'] = 'application/octet-stream';
            }
        }

        $headers['Date'] = gmdate('D, d M Y H:i:s \G\M\T');
        $headers['Authorization'] = $this->getAuthorization($headers);

        $this->options['headers'] = $headers;

        return $headers;
    }

    /**
     * Set header.
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setHeader($key, $value = null)
    {
        if (is_array($key)) {
            return $this->setHeaders($key);
        }

        if (! isset($this->options['headers'])) {
            $this->options['headers'] = [];
        }

        $this->options['headers'][$key] = $value;

        return $this;
    }

    /**
     * Set headers.
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        if (! isset($this->options['headers'])) {
            $this->options['headers'] = [];
        }

        $this->options['headers'] = array_merge($this->options['headers'], $headers);

        return $this;
    }

    /**
     * @return string
     */
    public function getBody() : string
    {
        return $this->options['body'] ?? '';
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body)
    {
        $this->options['body'] = $body;

        return $this;
    }

    /**
     * Get authorization.
     *
     * @param array $headers
     * @return string
     */
    protected function getAuthorization(array $headers)
    {
        $contentMD5 = $headers['Content-MD5'] ?? '';
        $contentType = $headers['Content-Type'] ?? '';

        $signString = $this->method() . "\n" .
            $contentMD5 . "\n" .
            $contentType . "\n" .
            $headers['Date'] . "\n" .
            $this->getCanonicalizedOssHeadersString($headers) .
            $this->getCanonicalizedResource();

        $accessKeyId = $this->client->getAccessKeyId();
        $accessKeySecret = $this->client->getAccessKeySecret();

        $hash = base64_encode(hash_hmac('sha1', $signString, $accessKeySecret, true));

        return 'OSS ' . $accessKeyId . ':' . $hash;
    }

    /**
     * Get canonicalized OSS headers string.
     *
     * @param array $headers
     * @return string
     */
    protected function getCanonicalizedOssHeadersString(array $headers)
    {
        $canonicalizedOssHeaders = [];

        foreach ($headers as $key => $value) {
            $key = strtolower($key);

            if (strpos($key, 'x-oss-') === 0) {
                $canonicalizedOssHeaders[] = $key.':'.$value;
            }
        }

        if (empty($canonicalizedOssHeaders)) {
            return '';
        }

        sort($canonicalizedOssHeaders);

        return implode("\n", $canonicalizedOssHeaders) . "\n";
    }

    /**
     * Get canonicalized resource
     *
     * @return string
     */
    protected function getCanonicalizedResource()
    {
        $resource = '/' . $this->client->getBucketName() . $this->getPath();

        $queryString = $this->getQueryString(true);

        if ($queryString) {
            $resource .= '?' . $queryString;
        }

        return $resource;
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    public function getHttp(): ClientInterface
    {
        return $this->http;
    }

    /**
     * @param \GuzzleHttp\ClientInterface $http
     * @return $this
     */
    public function setHttp(ClientInterface $http)
    {
        $this->http = $http;

        return $this;
    }
}