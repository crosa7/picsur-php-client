<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use Picsur\Client\Service\DeleteService;
use Picsur\Client\Service\RequestService;
use Picsur\Client\Service\UploadService;
use Picsur\Client\Url\UrlConstants;

test('delete should delete multiple images', function () {
    $expectedResponse = new Response(
        201,
        ['Content-Type' => 'application/json'],
        json_encode(
            [
                'success' => true
            ]
        )
    );

    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('post')
        ->with(
            'http://some-host:9999' . UrlConstants::DELETE_PATH,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Api-Key some-api-key',
                    'Content-Type' => 'application/json',
                    'Accept' => '*/*',
                ],
                RequestOptions::JSON => ['ids' => ['some-image-id', 'some-other-image-id']],
            ]
        )
        ->andReturn($expectedResponse);

    $requestService = new RequestService(
        'some-api-key',
        'http://some-host',
        $guzzleMock,
        9999
    );
    $deleteService = new DeleteService($requestService);

    $responseData = $deleteService->delete(['some-image-id', 'some-other-image-id']);

    expect($responseData['success'])->toBeTrue();
});

test('delete should handle request exception', function () {
    $expectedResponse = new Response(
        201,
        ['Content-Type' => 'application/json'],
        json_encode(
            [
                'success' => false,
                'data' => [
                    'message' => 'error'
                ]
            ]
        )
    );

    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('post')
        ->with('http://some-host:9999' . UrlConstants::DELETE_PATH, Mockery::any())
        ->andThrow(new RequestException('error-message', new Request('post', 'some-uri'), $expectedResponse));

    $requestService = new RequestService(
        'some-api-key',
        'http://some-host',
        $guzzleMock,
        9999
    );
    $deleteService = new DeleteService($requestService);

    $responseData = $deleteService->delete(['some-image-id']);

    expect($responseData['success'])->toBeFalse();
    expect($responseData['data']['message'])->toBe('error');
});

test('delete should handle null exception response', function () {
    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('post')
        ->with('http://some-host:9999' . UrlConstants::DELETE_PATH, Mockery::any())
        ->andThrow(new RequestException('error-message', new Request('post', 'some-uri')));

    $requestService = new RequestService(
        'some-api-key',
        'http://some-host',
        $guzzleMock,
        9999
    );
    $deleteService = new DeleteService($requestService);

    $responseData = $deleteService->delete(['some-image-id']);

    expect($responseData['success'])->toBeFalse();
    expect($responseData['data']['message'])->toBe('error-message');
});

test('delete should handle general exception', function () {
    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('post')
        ->with('http://some-host:9999' . UrlConstants::DELETE_PATH, Mockery::any())
        ->andThrow(new Exception('general-error-message'));

    $requestService = new RequestService(
        'some-api-key',
        'http://some-host',
        $guzzleMock,
        9999
    );
    $deleteService = new DeleteService($requestService);

    $responseData = $deleteService->delete(['some-image-id']);

    expect($responseData['success'])->toBeFalse();
    expect($responseData['data']['message'])->toBe('general-error-message');
});
