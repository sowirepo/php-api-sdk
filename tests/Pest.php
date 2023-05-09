<?php

declare(strict_types=1);

use Http\Client\HttpClient;
use Http\Message\RequestMatcher\RequestMatcher;
use Http\Mock\Client;
use Mockery\Matcher\Closure;
use Mockery\Mock;
use Mockery\MockInterface;
use Nyholm\Psr7\Stream;
use Pest\Expectation;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Data\OnFailureDataInterface;
use Sowiso\SDK\Data\OnRequestDataInterface;
use Sowiso\SDK\Data\OnResponseDataInterface;
use Sowiso\SDK\Data\OnSuccessDataInterface;
use Sowiso\SDK\Exceptions\InvalidDataException;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\SowisoApiContext;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeApiRequest', function (
    string $path,
    ?string $method = null,
    ?string $apiKey = null,
) {
    $method ??= 'GET'; // TODO
    $apiKey ??= 'ABC123'; // TODO

    /** @var Expectation|RequestInterface $request */
    $request = $this
        ->toBeInstanceOf(RequestInterface::class);

    $request
        ->getUri()->getScheme()->toBe('http') // TODO
        ->getUri()->getHost()->toBe('test.sowiso.local') // TODO
        ->getUri()->getPath()->toBe($path)
        ->getMethod()->toBe($method)
        ->getHeader('X-API-KEY')->toBe([$apiKey]);

    return $this;
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function api(
    ?string $baseUrl = null,
    ?string $apiKey = null,
    ?ClientInterface $httpClient = null,
): SowisoApi {
    return new SowisoApi(
        configuration: configuration($baseUrl, $apiKey),
        httpClient: $httpClient,
    );
}

function configuration(
    ?string $baseUrl = null,
    ?string $apiKey = null,
): SowisoApiConfiguration {
    $baseUrl ??= 'http://test.sowiso.local'; // TODO
    $apiKey ??= 'ABC123'; // TODO

    return SowisoApiConfiguration::create($baseUrl, $apiKey);
}

function context(?string $user = null): SowisoApiContext
{
    return SowisoApiContext::create(user: $user);
}

// TODO: user1
function contextWithUsername(?string $user = 'user1'): SowisoApiContext
{
    return SowisoApiContext::create(user: $user);
}

/**
 * @param array<array{path: string, body: mixed, statusCode?: int}> $responses
 * @return HttpClient
 */
function mockHttpClient(array $responses): HttpClient
{
    $client = new Client();

    foreach ($responses as $response) {
        $client->on(
            requestMatcher: new RequestMatcher($response['path']),
            result: mock(ResponseInterface::class)->expect(
                getStatusCode: fn () => $response['statusCode'] ?? 200,
                getBody: fn () => Stream::create(json_encode($response['body'])),
            )
        );
    }

    return $client;
}

/**
 * @param callable(mixed): void $dispatch
 * @return Closure
 */
function capture(callable $dispatch): Closure
{
    $closure = function ($argument) use ($dispatch) {
        $dispatch($argument);
        return true;
    };

    return new Closure($closure);
}

/**
 * @param string $method
 * @param string $uri
 * @param array $request
 * @param array $response
 * @param SowisoApiContext|null $context
 * @return void
 * @throws SowisoApiException
 */
function makesRequestCorrectly(
    string $method,
    string $uri,
    array $request,
    mixed $response,
    ?SowisoApiContext $context = null,
): void {
    $context ??= context();

    /** @var Client $client */
    $client = mockHttpClient([
        ['path' => $uri, 'body' => $response],
    ]);

    api(httpClient: $client)->request($context, json_encode($request));

    $httpRequest = $client->getLastRequest();

    expect($httpRequest)
        ->toBeApiRequest($uri, $method)
        ->when(
            $httpRequest->getMethod() === 'POST',
            fn ($httpRequest) => $httpRequest
                ->getHeader('Content-Type')->toBe(['application/json'])
                ->getBody()->__toString()->not()->toContain('__endpoint')
        );
}

/**
 * @param string $uri
 * @param array $request
 * @param array $response
 * @param class-string $callbackName
 * @param callable(RequestInterface): void $requestCaptor
 * @param callable(ResponseInterface): void $responseCaptor
 * @param SowisoApiContext|null $context
 * @throws SowisoApiException
 */
function runsAllCallbackMethodsCorrectly(
    string $uri,
    array $request,
    mixed $response,
    string $callbackName,
    callable $requestCaptor,
    callable $responseCaptor,
    ?SowisoApiContext $context = null,
): void {
    $client = mockHttpClient([
        ['path' => $uri, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $context ??= context();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
    $callback = mock($callbackName)->makePartial();

    $contextCaptor = fn (SowisoApiContext $it) => expect($it)->toBe($context);

    $callback->expects('onRequest')->with(
        capture(function (OnRequestDataInterface $data) use ($contextCaptor, $requestCaptor) {
            $contextCaptor($data->getContext());
            $requestCaptor($data->getRequest());
        })
    )->once();

    $callback->expects('onResponse')->with(
        capture(function (OnResponseDataInterface $data) use ($contextCaptor, $responseCaptor) {
            $contextCaptor($data->getContext());
            $responseCaptor($data->getResponse());
        })
    )->once();

    $callback->expects('onSuccess')->with(
        capture(function (OnSuccessDataInterface $data) use ($contextCaptor, $requestCaptor, $responseCaptor) {
            $contextCaptor($data->getContext());
            $requestCaptor($data->getRequest());
            $responseCaptor($data->getResponse());
        })
    )->once();

    $callback->expects('onFailure')
        ->never();

    $api->useCallback($callback);

    $api->request($context, json_encode($request));
}

/**
 * @param array $request
 * @param class-string $callbackName
 * @param SowisoApiContext|null $context
 * @throws SowisoApiException
 */
function runsOnFailureCallbackMethodCorrectlyOnMissingData(
    array $request,
    string $callbackName,
    ?SowisoApiContext $context = null,
): void {
    $api = api();

    $context ??= context();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
    $callback = mock($callbackName)->makePartial();

    $callback->expects('onRequest')->never();
    $callback->expects('onResponse')->never();
    $callback->expects('onSuccess')->never();

    $callback->expects('onFailure')->with(
        capture(function (OnFailureDataInterface $data) use ($context) {
            expect($data)
                ->getContext()->toBe($context)
                ->getException()->toBeInstanceOf(MissingDataException::class);
        })
    )->once();

    $api->useCallback($callback);

    expect(fn () => $api->request($context, json_encode($request)))
        ->toThrow(MissingDataException::class);
}

/**
 * @param string $uri
 * @param array $request
 * @param array $response
 * @param class-string $callbackName
 * @param class-string $exceptionName
 * @param SowisoApiContext|null $context
 * @throws SowisoApiException
 */
function runsOnFailureCallbackMethodCorrectlyOnException(
    string $uri,
    array $request,
    mixed $response,
    string $callbackName,
    string $exceptionName,
    ?SowisoApiContext $context = null,
): void {
    $client = mockHttpClient([
        ['path' => $uri, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $context ??= context();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
    $callback = mock($callbackName)->makePartial();

    $callback->expects('onRequest')->with(
        capture(function (OnRequestDataInterface $data) use ($context, $exceptionName) {
            expect($data)->getContext()->toBe($context);
        })
    )->once();

    $callback->expects('onResponse')->never();
    $callback->expects('onSuccess')->never();

    $callback->expects('onFailure')->with(
        capture(function (OnFailureDataInterface $data) use ($context, $exceptionName) {
            expect($data)
                ->getContext()->toBe($context)
                ->getException()->toBeInstanceOf($exceptionName);
        })
    )->once();

    $api->useCallback($callback);

    expect(fn () => $api->request($context, json_encode($request)))
        ->toThrow($exceptionName);
}

/**
 * @param array $request
 * @param string $missingFieldName
 * @param SowisoApiContext|null $context
 */
function failsOnMissingRequestData(
    array $request,
    string $missingFieldName,
    ?SowisoApiContext $context = null,
): void {
    $context ??= context();

    expect(fn () => api()->request($context, json_encode($request)))
        ->toThrow(fn (MissingDataException $e) => expect($e)->getField()->toEqual($missingFieldName));
}

/**
 * @param string $uri
 * @param array $request
 * @param array $response
 * @param string $missingFieldName
 * @param SowisoApiContext|null $context
 * @throws SowisoApiException
 */
function failsOnMissingResponseData(
    string $uri,
    array $request,
    mixed $response,
    string $missingFieldName,
    ?SowisoApiContext $context = null,
): void {
    $client = mockHttpClient([
        ['path' => $uri, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $context ??= context();

    expect(fn () => $api->request($context, json_encode($request)))
        ->toThrow(fn (MissingDataException $e) => expect($e)->getField()->toEqual($missingFieldName));
}

/**
 * @param array $request
 * @param string $message
 * @param SowisoApiContext|null $context
 */
function failsOnInvalidData(
    array $request,
    string $message,
    ?SowisoApiContext $context = null,
): void {
    $context ??= context();

    expect(fn () => api()->request($context, json_encode($request)))
        ->toThrow(fn (InvalidDataException $e) => expect($e)->getMessage()->toEqual($message));
}
