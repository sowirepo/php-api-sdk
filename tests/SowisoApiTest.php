<?php

declare(strict_types=1);

use Mockery\Mock;
use Mockery\MockInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Exceptions\InvalidBaseUrlException;
use Sowiso\SDK\Exceptions\NoApiKeyException;
use Sowiso\SDK\Exceptions\NoBaseUrlException;
use Sowiso\SDK\SowisoApi;

it('accepts a configuration', function () {
    $configuration = configuration();

    expect(new SowisoApi(configuration: $configuration))
        ->getConfiguration()->toBe($configuration);
});

it('accepts PSR-17 and PSR-18 implementations', function () {
    $httpClient = new GuzzleHttp\Client();
    $httpFactory = new Psr17Factory();

    $api = new SowisoApi(
        configuration: configuration(),
        httpClient: $httpClient,
        httpRequestFactory: $httpFactory,
        httpStreamFactory: $httpFactory,
    );

    expect($api)
        ->getHttpClient()->toBe($httpClient)
        ->getHttpRequestFactory()->toBe($httpFactory)
        ->getHttpStreamFactory()->toBe($httpFactory);
});

it('automatically discovers PSR-17 and PSR-18 implementations', function () {
    $api = new SowisoApi(configuration: configuration());

    expect($api)
        ->getHttpClient()->toBeInstanceOf(GuzzleHttp\Client::class)
        ->getHttpRequestFactory()->toBeInstanceOf(Psr17Factory::class)
        ->getHttpStreamFactory()->toBeInstanceOf(Psr17Factory::class);
});

it('runs endpoint callbacks correctly', function (string $class, array $data) {
    $api = new SowisoApi(
        configuration: configuration(),
        httpClient: mockHttpClient(
            path: '/test',
            response: ['tryId' => 123],
        ),
    );

    $context = context();

    /** @var Mock|MockInterface&CallbackInterface $callback */
    $callback = mock($class)
        ->makePartial();

    $callback->expects('onRequest')
        ->withSomeOfArgs($context)
        ->once();

    $callback->expects('onResponse')
        ->withSomeOfArgs($context)
        ->once();

    $callback->expects('onSuccess')
        ->withSomeOfArgs($context)
        ->once();

    $callback->expects('onFailure')
        ->never();

    $api->useCallback($callback);

    $api->request($context, json_encode($data));
})->with([
    [PlayExerciseSetCallback::class, ['__endpoint' => 'play/set']],
    [EvaluateAnswerCallback::class, ['__endpoint' => 'evaluate/answer']],
]);

it('fails when no base url is set', function () {
    api(baseUrl: "")->request(context(), "{}");
})->throws(NoBaseUrlException::class);

it('fails when an invalid base url is set', function () {
    api(baseUrl: "ABC")->request(context(), "{}");
})->throws(InvalidBaseUrlException::class);

it('fails when no API key is set', function () {
    api(apiKey: "")->request(context(), "{}");
})->throws(NoApiKeyException::class);
