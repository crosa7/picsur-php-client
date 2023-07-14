<?php

declare(strict_types=1);

namespace Picsur\Client;


use Picsur\Client\Service\ServiceFactory;

class PicsurClient
{
    private ServiceFactory $serviceFactory;

    /**
     * @param string $apiKey
     * @param string $host
     * @param int|null $port
     */
    public function __construct(
        private readonly string $apiKey,
        private readonly string $host,
        private readonly ?int $port = null
    ) {
        $this->serviceFactory = new ServiceFactory($this->apiKey, $this->host, $this->port);
    }

    /**
     * @param string $filePath
     *
     * @return array
     */
    public function upload(string $filePath): array
    {
        return $this->serviceFactory->createUploadService()->upload($filePath);
    }

    /**
     * @param array $imageIds
     *
     * @return array
     */
    public function delete(array $imageIds): array
    {
        return $this->serviceFactory->createDeleteService()->delete($imageIds);
    }
}
