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
     * Request queries.
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Request headers.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Request body.
     *
     * @var string
     */
    protected $body = '';

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
     * Make a new Request.
     *
     * @param \HuangYi\AliyunOss\OssClient $client
     * @param \GuzzleHttp\ClientInterface|null $http
     * @return static
     */
    public static function make(OssClient $client, ClientInterface $http = null)
    {
        return new static($client, $http);
    }

    /**
     * Create a new Request.
     *
     * @param \HuangYi\AliyunOss\OssClient $client
     * @param \GuzzleHttp\ClientInterface $http
     * @return void
     */
    public function __construct(OssClient $client, ClientInterface $http = null)
    {
        $this->client = $client;
        $this->http = $http ?? new Client;
    }

    /**
     * Return the request method.
     *
     * @return string
     */
    abstract public function method(): string;

    /**
     * Return the request URL.
     *
     * @return string
     */
    abstract public function url(): string;

    /**
     * Return the request options.
     *
     * @return array
     */
    abstract public function options(): array;

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
                $this->getOptions()
            );
        } catch (Exception $exception) {
            $this->handleException($exception);
        }

        return $response;
    }

    /**
     * Get request url.
     *
     * @param bool $withBucket
     * @return string
     */
    public function getUrl(bool $withBucket = true)
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
            $domain = $this->client->getBucket() . '.' . $domain;
        }

        return $domain;
    }

    /**
     * Get query string.
     *
     * @return string
     */
    public function getQueryString()
    {
        $parts = $this->getQueryStringParts();

        if ($this->getSubResource()) {
            array_unshift($parts, rawurlencode($this->getSubResource()));
        }

        return implode('&', $parts);
    }

    /**
     * Get query string parts.
     *
     * @return array
     */
    protected function getQueryStringParts()
    {
        $parts = [];

        foreach ($this->getQueries() as $key => $value) {
            $parts[] = rawurlencode($key) . '=' . rawurlencode($value);
        }

        sort($parts);

        return $parts;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->options();

        if ($this->getBody()) {
            $options['body'] = $this->getBody();
        }

        $options['headers'] = $this->getHeaders();

        return $options;
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
        if ($exception instanceof BadResponseException) {
            $message = (string)$exception->getResponse()->getBody();
        } else {
            $message = $exception->getMessage();
        }

        throw new RequestException($message, $exception->getCode(), $exception);
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
        return $this->queries;
    }

    /**
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setQuery($key, $value)
    {
        if (is_array($key)) {
            return $this->setQueries($key);
        }

        $this->queries[$key] = $value;

        return $this;
    }

    /**
     * @param array $queries
     * @return $this
     */
    public function setQueries(array $queries)
    {
        $this->queries = array_merge($this->queries, $queries);

        return $this;
    }

    /**
     * Set header.
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setHeader($key, $value = null)
    {
        if (is_array($key)) {
            return $this->setHeaders($key);
        }

        $this->headers[$key] = $value;

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
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        $options = $this->options();
        $headers = $this->headers;

        if (isset($options['headers'])) {
            $headers = array_merge($options['headers'], $headers);
        }

        if (! isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'application/octet-stream';
        }

        if ($this->getBody()) {
            $headers['Content-Length'] = strlen($this->getBody());
            $headers['Content-MD5'] = base64_encode(md5($this->getBody(), true));
        }

        $headers['Date'] = gmdate('D, d M Y H:i:s \G\M\T');
        $headers['Authorization'] = $this->getAuthorization($headers);

        return $headers;
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

        $signString = $this->method() . "\n" .
            $contentMD5 . "\n" .
            $headers['Content-Type'] . "\n" .
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
        $resource = '/' . $this->client->getBucket() . $this->getPath();

        $queryString = $this->getQueryString();

        if ($queryString) {
            $resource .= '?' . $queryString;
        }

        return $resource;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
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