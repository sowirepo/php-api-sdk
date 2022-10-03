<?php

declare(strict_types=1);

use Http\Client\HttpClient;
use Http\Message\RequestMatcher\RequestMatcher;
use Http\Mock\Client;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
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
): SowisoApi {
    return new SowisoApi(
        configuration: configuration($baseUrl, $apiKey),
    );
}

function configuration(
    ?string $baseUrl = null,
    ?string $apiKey = null,
): SowisoApiConfiguration {
    $baseUrl ??= "http://test.sowiso.local";
    $apiKey ??= "ABC123";

    return SowisoApiConfiguration::create($baseUrl, $apiKey);
}

function context(): SowisoApiContext
{
    return SowisoApiContext::create();
}

function mockHttpClient(
    string $path,
    array $response,
    int $statusCode = 200,
): HttpClient {
    $response = mock(ResponseInterface::class)->expect(
        getStatusCode: fn() => $statusCode,
        getBody: fn() => Stream::create(json_encode($response)),
    );

    $client = new Client();
    $client->on(new RequestMatcher($path), $response);

    return $client;
}
