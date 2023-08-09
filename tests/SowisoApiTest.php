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
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\InvalidBaseUrlException;
use Sowiso\SDK\Exceptions\InvalidEndpointException;
use Sowiso\SDK\Exceptions\InvalidJsonRequestException;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\Exceptions\NoApiKeyException;
use Sowiso\SDK\Exceptions\NoBaseUrlException;
use Sowiso\SDK\Exceptions\NoEndpointException;
use Sowiso\SDK\Hooks\DataCapture\Data\OnRegisterExerciseSetData;
use Sowiso\SDK\Hooks\DataCapture\DataCaptureHook;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;

dataset('api-endpoint-data', [
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
    $client = mockHttpClient([
        ['path' => $path, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $context = contextWithUsername();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
    $callback = mock($class)
        ->makePartial();

    $callback->expects('onRequest')->with(
        capture(function (OnRequestDataInterface $data) use ($context) {
            expect($data)->getContext()->toBe($context);
        })
    )->once();

    $callback->expects('onResponse')->with(
        capture(function (OnResponseDataInterface $data) use ($context) {
            expect($data)->getContext()->toBe($context);
        })
    )->once();

    $callback->expects('onSuccess')->with(
        capture(function (OnSuccessDataInterface $data) use ($context) {
            expect($data)->getContext()->toBe($context);
        })
    )->once();

    $callback->expects('onFailure')->never();

    $api->useCallback($callback);

    $api->request($context, json_encode($request));
})->with('api-endpoint-data');

it('runs callbacks with priority in correct order', function (string $class, string $path, array $request, array $response) {
    $client = mockHttpClient([
        ['path' => $path, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $context = contextWithUsername();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $firstCallback */
    $firstCallback = mock($class)
        ->makePartial()
        ->shouldReceive('priority')
        ->andReturn(CallbackPriority::LOW)
        ->getMock();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $secondCallback */
    $secondCallback = mock($class)
        ->makePartial();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $thirdCallback */
    $thirdCallback = mock($class)
        ->makePartial()
        ->shouldReceive('priority')
        ->andReturn(CallbackPriority::HIGH)
        ->getMock();

    $thirdCallback->expects('onRequest')->once()->globally()->ordered();
    $secondCallback->expects('onRequest')->once()->globally()->ordered();
    $firstCallback->expects('onRequest')->once()->globally()->ordered();

    $thirdCallback->expects('onResponse')->once()->globally()->ordered();
    $secondCallback->expects('onResponse')->once()->globally()->ordered();
    $firstCallback->expects('onResponse')->once()->globally()->ordered();

    $thirdCallback->expects('onSuccess')->once()->globally()->ordered();
    $secondCallback->expects('onSuccess')->once()->globally()->ordered();
    $firstCallback->expects('onSuccess')->once()->globally()->ordered();

    $api->useCallback($firstCallback);
    $api->useCallback($secondCallback);
    $api->useCallback($thirdCallback);

    $api->request($context, json_encode($request));
})->with('api-endpoint-data');

it('fails when no base url is set', function () {
    api(baseUrl: "")->request(context(), "{}");
})->throws(NoBaseUrlException::class);

it('fails when an invalid base url is set', function () {
    api(baseUrl: "ABC")->request(context(), "{}");
})->throws(InvalidBaseUrlException::class);

it('fails when no API key is set', function () {
    api(apiKey: "")->request(context(), "{}");
})->throws(NoApiKeyException::class);

it('fails with no endpoint', function () {
    api()->request(context(), "{}");
})->throws(NoEndpointException::class);

it('fails with invalid endpoint', function () {
    api()->request(context(), json_encode(['__endpoint' => 'XYZ']));
})->throws(InvalidEndpointException::class);

it('fails with invalid JSON request data', function (string $request) {
    api()->request(context(), $request);
})->with(["", "{ / }"])->throws(InvalidJsonRequestException::class);

it('fails with invalid JSON response data', function (string $response, string $expectedError) {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => $response, 'statusCode' => 500, 'json' => false],
    ]);

    $request = json_encode(PlayExerciseSet::Request);

    expect(fn () => api(httpClient: $client)->request(contextWithUsername(), $request))
        ->toThrow(fn (InvalidJsonResponseException $exception) => expect($exception->getMessage())->toBe($expectedError));
})->with([
    ['', 'InvalidJsonResponse ("Unknown Server Error")'],
    ['{ / }', 'InvalidJsonResponse ("{ / }")'],
    ['<!DOCTYPE html> ...', 'InvalidJsonResponse ("<!DOCTYPE html> ...")'],
]);

it('can handle no additional payload', function (array $request) {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(DataCaptureHook::class)->makePartial();

    $hook->expects('onRegisterExerciseSet')
        ->with(
            capture(function (OnRegisterExerciseSetData $data) {
                expect($data)
                    ->getPayload()->getData()->toBeNull();
            })
        )
        ->once()->globally()->ordered();

    $api->useHook($hook);

    $api->request(contextWithUsername(), json_encode($request));
})->with(function () {
    $requestWithNoPayload = PlayExerciseSet::Request;

    yield "none" => [$requestWithNoPayload];

    $requestWithEmptyStringPayload = PlayExerciseSet::Request;
    $requestWithEmptyStringPayload[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] = "";

    yield "empty string" => [$requestWithEmptyStringPayload];

    $requestWithEmptyArrayPayload = PlayExerciseSet::Request;
    $requestWithEmptyArrayPayload[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] = [];

    yield "empty array" => [$requestWithEmptyArrayPayload];

    $requestWithEmptyObjectPayload = PlayExerciseSet::Request;
    $requestWithEmptyObjectPayload[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] = new stdClass();

    yield "empty object" => [$requestWithEmptyObjectPayload];
});
