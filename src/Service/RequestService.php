<?php

declare(strict_types=1);


namespace Picsur\Client\Service;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\RequestOptions;
use Picsur\Client\Api\ApiConstants;

class RequestService
{
    /**
     * @param string $apiKey
     * @param string $host
     * @param \GuzzleHttp\Client $guzzleClient
     * @param int|null $port
     */
    public function __construct(
        private string $apiKey,
        private string $host,
        private Client $guzzleClient,
        private ?int $port = null
    ) {
    }

    /**
     * @param string $path
     * @param array $multipartData
     *
     * @return array
     */
    public function postMultipart(string $path, array $multipartData): array
    {
        $boundary = 'picsur-image-upload-boundary';

        $options = [
            RequestOptions::HEADERS => [
                'Authorization' => $this->getAuthorizationHeader(),
                'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
                'Accept' => '*/*',
            ],
            RequestOptions::BODY => new MultipartStream($multipartData, $boundary),
        ];

        return $this->doPost($path, $options);
    }

    /**
     * @param string $path
     * @param array $body
     *
     * @return array
     */
    public function post(string $path, array $body): array
    {
        $options = [
            RequestOptions::HEADERS => [
                'Authorization' => $this->getAuthorizationHeader(),
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
            ],
            RequestOptions::JSON => $body,
        ];

        return $this->doPost($path, $options);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function buildUrl(string $path): string
    {
        $host = $this->host;
        if ($this->port) {
            $host = sprintf('%s:%s', $host, $this->port);
        }

        return sprintf('%s%s', $host, $path);
    }

    /**
     * @return string
     */
    private function getAuthorizationHeader(): string
    {
        return sprintf('%s %s', ApiConstants::AUTHORIZATION_HEADER_PREFIX, $this->apiKey);
    }

    /**
     * @param string $path
     * @param array $options
     *
     * @return array
     */
    private function doPost(string $path, array $options): array
    {
        try {
            $response = $this->guzzleClient->post($this->buildUrl($path), $options);
        } catch (RequestException $exception) {
            if ($exception->getResponse()) {
                return json_decode($exception->getResponse()->getBody()->getContents(), true);
            }
            return ['success' => false, 'data' => ['message' => $exception->getMessage()]];
        } catch (\Throwable $exception) {
            return ['success' => false, 'data' => ['message' => $exception->getMessage()]];
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
