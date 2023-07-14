<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Picsur\Client\Service\RequestService;
use Picsur\Client\Service\UploadService;
use Picsur\Client\Url\UrlConstants;

test('upload should upload image', function () {
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
        ->with('http://some-host:9999' . UrlConstants::UPLOAD_PATH, Mockery::any())
        ->andReturn($expectedResponse);

    $requestService = new RequestService(
        'some-api-key',
        'http://some-host',
        $guzzleMock,
        9999
    );
    $uploadService = new UploadService($requestService);

    $responseData = $uploadService->upload('tests/_data/images/surfpack.jpeg');

    expect($responseData['success'])->toBeTrue();
});

test('upload should handle request exception', function () {
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
        ->with('http://some-host:9999' . UrlConstants::UPLOAD_PATH, Mockery::any())
        ->andThrow(new RequestException('error-message', new Request('post', 'some-uri'), $expectedResponse));

    $requestService = new RequestService(
        'some-api-key',
        'http://some-host',
        $guzzleMock,
        9999
    );
    $uploadService = new UploadService($requestService);

    $responseData = $uploadService->upload('tests/_data/images/surfpack.jpeg');

    expect($responseData['success'])->toBeFalse();
    expect($responseData['data']['message'])->toBe('error');
});

test('upload should handle null exception response', function () {
    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('post')
        ->with('http://some-host:9999' . UrlConstants::UPLOAD_PATH, Mockery::any())
        ->andThrow(new RequestException('error-message', new Request('post', 'some-uri')));

    $requestService = new RequestService(
        'some-api-key',
        'http://some-host',
        $guzzleMock,
        9999
    );
    $uploadService = new UploadService($requestService);

    $responseData = $uploadService->upload('tests/_data/images/surfpack.jpeg');

    expect($responseData['success'])->toBeFalse();
    expect($responseData['data']['message'])->toBe('error-message');
});

test('upload should handle general exception', function () {
    $guzzleMock = Mockery::mock(Client::class);
    $guzzleMock
        ->shouldReceive('post')
        ->with('http://some-host:9999' . UrlConstants::UPLOAD_PATH, Mockery::any())
        ->andThrow(new Exception('general-error-message'));

    $requestService = new RequestService(
        'some-api-key',
        'http://some-host',
        $guzzleMock,
        9999
    );
    $uploadService = new UploadService($requestService);

    $responseData = $uploadService->upload('tests/_data/images/surfpack.jpeg');

    expect($responseData['success'])->toBeFalse();
    expect($responseData['data']['message'])->toBe('general-error-message');
});
