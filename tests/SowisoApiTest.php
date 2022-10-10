<?php

declare(strict_types=1);

use Mockery\Mock;
use Mockery\MockInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerEndpoint;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetEndpoint;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\InvalidBaseUrlException;
use Sowiso\SDK\Exceptions\InvalidEndpointException;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Exceptions\NoApiKeyException;
use Sowiso\SDK\Exceptions\NoBaseUrlException;
use Sowiso\SDK\Exceptions\NoEndpointException;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;

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

it('runs endpoint callbacks correctly', function (string $class, string $path, array $request, array $response) {
    $api = api(
        httpClient: mockHttpClient(
            path: $path,
            response: $response,
        ),
    );

    $context = contextWithUsername();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
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

    $api->request($context, json_encode($request));
})->with([
    PlayExerciseSetEndpoint::NAME => [
        PlayExerciseSetCallback::class,
        PlayExerciseSet::Uri,
        PlayExerciseSet::Request,
        PlayExerciseSet::Response,
    ],
    EvaluateAnswerEndpoint::NAME => [
        EvaluateAnswerCallback::class,
        EvaluateAnswer::Uri,
        EvaluateAnswer::Request,
        EvaluateAnswer::Response,
    ],
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

it('fails with no JSON data', function () {
    api()->request(context(), "");
})->throws(InvalidJsonDataException::class);

it('fails with invalid JSON data', function () {
    api()->request(context(), "{ / }");
})->throws(InvalidJsonDataException::class);

it('fails with no endpoint', function () {
    api()->request(context(), "{}");
})->throws(NoEndpointException::class);

it('fails with invalid endpoint', function () {
    api()->request(context(), json_encode(['__endpoint' => 'XYZ']));
})->throws(InvalidEndpointException::class);
