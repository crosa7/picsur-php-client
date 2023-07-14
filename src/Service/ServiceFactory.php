<?php

declare(strict_types=1);


namespace Picsur\Client\Service;


use GuzzleHttp\Client;

class ServiceFactory
{
    /**
     * @param string $apiKey
     * @param string $host
     * @param int|null $port
     */
    public function __construct(
        private string $apiKey,
        private string $host,
        private ?int $port = null
    ) {
    }

    /**
     * @return \Picsur\Client\Service\UploadService
     */
    public function createUploadService(): UploadService
    {
        return new UploadService($this->createRequestService());
    }

    /**
     * @return \Picsur\Client\Service\DeleteService
     */
    public function createDeleteService(): DeleteService
    {
        return new DeleteService($this->createRequestService());
    }

    /**
     * @return \Picsur\Client\Service\RequestService
     */
    private function createRequestService(): RequestService
    {
        return new RequestService($this->apiKey, $this->host, new Client(), $this->port);
    }
}
