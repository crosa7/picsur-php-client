<?php

declare(strict_types=1);


namespace Picsur\Client\Service;


use Picsur\Client\Url\UrlConstants;

class DeleteService
{
    /**
     * @param \Picsur\Client\Service\RequestService $requestService
     */
    public function __construct(private RequestService $requestService)
    {
    }

    /**
     * @param array $imageIds
     *
     * @return array
     */
    public function delete(array $imageIds): array
    {
        $body = ['ids' => $imageIds];

        return $this->requestService->post(UrlConstants::DELETE_PATH, $body);
    }
}
