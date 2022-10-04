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
    $method ??= 'GET';
    $apiKey ??= 'ABC123';

    /** @var Expectation|RequestInterface $request */
    $request = $this
        ->toBeInstanceOf(RequestInterface::class);

    return $request
        ->getUri()->getScheme()->toBe('http') // TODO
        ->getUri()->getHost()->toBe('test.sowiso.local') // TODO
        ->getUri()->getPath()->toBe($path)
        ->getMethod()->toBe($method)
        ->getHeader('Content-Type')->toBe(['application/json'])
        ->getHeader('X-API-KEY')->toBe([$apiKey]);
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
    $baseUrl ??= 'http://test.sowiso.local';
    $apiKey ??= 'ABC123';

    return SowisoApiConfiguration::create($baseUrl, $apiKey);
}

function context(): SowisoApiContext
{
    return SowisoApiContext::create();
}

function mockHttpClient(
    string $path,
    mixed $response,
    int $statusCode = 200,
): HttpClient {
    $httpResponse = mock(ResponseInterface::class)->expect(
        getStatusCode: fn() => $statusCode,
        getBody: fn() => Stream::create(json_encode($response)),
    );

    $client = new Client();
    $client->on(new RequestMatcher($path), $httpResponse);

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
 * @return void
 * @throws SowisoApiException
 */
function makesRequestCorrectly(
    string $method,
    string $uri,
    array $request,
    mixed $response,
): void {
    /** @var Client $client */
    $client = mockHttpClient(path: $uri, response: $response);

    api(httpClient: $client)->request(context(), json_encode($request));

    $httpRequest = $client->getLastRequest();

    expect($httpRequest)
        ->toBeApiRequest($uri, $method)
        ->and((string)$httpRequest->getBody())
        ->not()->toContain('__endpoint');
}

/**
 * @param string $uri
 * @param array $request
 * @param array $response
 * @param class-string $callbackName
 * @param callable(RequestInterface): void $requestCaptor
 * @param callable(ResponseInterface): void $responseCaptor
 * @throws SowisoApiException
 */
function runsAllCallbackMethodsCorrectly(
    string $uri,
    array $request,
    mixed $response,
    string $callbackName,
    callable $requestCaptor,
    callable $responseCaptor,
): void {
    $api = api(httpClient: mockHttpClient(path: $uri, response: $response));

    $context = context();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
    $callback = mock($callbackName)->makePartial();

    $requestCapture = capture($requestCaptor);
    $responseCapture = capture($responseCaptor);

    $callback->expects('onRequest')
        ->with($context, $requestCapture)
        ->once();

    $callback->expects('onResponse')
        ->with($context, $responseCapture)
        ->once();

    $callback->expects('onSuccess')
        ->with($context, $requestCapture, $responseCapture)
        ->once();

    $callback->expects('onFailure')
        ->never();

    $api->useCallback($callback);

    $api->request($context, json_encode($request));
}

/**
 * @param array $request
 * @param class-string $callbackName
 * @throws SowisoApiException
 */
function runsOnFailureCallbackMethodCorrectlyOnMissingData(
    array $request,
    string $callbackName,
): void {
    $api = api();

    $context = context();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
    $callback = mock($callbackName)->makePartial();

    $exceptionCapture = capture(function (Exception $exception) {
        expect($exception)
            ->toBeInstanceOf(MissingDataException::class);
    });

    $callback->expects('onRequest')->never();
    $callback->expects('onResponse')->never();
    $callback->expects('onSuccess')->never();

    $callback->expects('onFailure')
        ->with($context, $exceptionCapture)
        ->once();

    $api->useCallback($callback);

    expect(fn() => $api->request($context, json_encode($request)))
        ->toThrow(MissingDataException::class);
}

/**
 * @param string $uri
 * @param array $request
 * @param array $response
 * @param class-string $callbackName
 * @param class-string $exceptionName
 * @throws SowisoApiException
 */
function runsOnFailureCallbackMethodCorrectlyOnException(
    string $uri,
    array $request,
    mixed $response,
    string $callbackName,
    string $exceptionName,
): void {
    $api = api(httpClient: mockHttpClient(path: $uri, response: $response));

    $context = context();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
    $callback = mock($callbackName)->makePartial();

    $exceptionCapture = capture(function (Exception $exception) use ($exceptionName) {
        expect($exception)
            ->toBeInstanceOf($exceptionName);
    });

    $callback->expects('onRequest')
        ->withSomeOfArgs($context)
        ->once();

    $callback->expects('onResponse')->never();
    $callback->expects('onSuccess')->never();

    $callback->expects('onFailure')
        ->with($context, $exceptionCapture)
        ->once();

    $api->useCallback($callback);

    expect(fn() => $api->request($context, json_encode($request)))
        ->toThrow($exceptionName);
}

/**
 * @param array $request
 * @param string $missingFieldName
 * @throws SowisoApiException
 */
function failsOnMissingRequestData(
    array $request,
    string $missingFieldName,
): void {
    expect(fn() => api()->request(context(), json_encode($request)))
        ->toThrow(fn(MissingDataException $e) => expect($e)->getField()->toEqual($missingFieldName));
}

/**
 * @param string $uri
 * @param array $request
 * @param array $response
 * @param string $missingFieldName
 * @throws SowisoApiException
 */
function failsOnMissingResponseData(
    string $uri,
    array $request,
    mixed $response,
    string $missingFieldName,
): void {
    $api = api(httpClient: mockHttpClient(path: $uri, response: $response));

    expect(fn() => $api->request(context(), json_encode($request)))
        ->toThrow(fn(MissingDataException $e) => expect($e)->getField()->toEqual($missingFieldName));
}
