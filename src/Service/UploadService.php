<?php

declare(strict_types=1);


namespace Picsur\Client\Service;


use Picsur\Client\Url\UrlConstants;

class UploadService
{
    /**
     * @param \Picsur\Client\Service\RequestService $requestService
     */
    public function __construct(private RequestService $requestService)
    {
    }

    /**
     * @param string $filePath
     *
     * @return array
     */
    public function upload(string $filePath): array
    {
        $multipart = [
            [
                'name' => 'image',
                'contents' => fopen($filePath, 'r'),
            ],
        ];

        return $this->requestService->postMultipart(UrlConstants::UPLOAD_PATH, $multipart);
    }
}
