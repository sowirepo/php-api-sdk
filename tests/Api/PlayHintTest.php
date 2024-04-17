<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlayHint\Http\PlayHintRequest;
use Sowiso\SDK\Api\PlayHint\Http\PlayHintResponse;
use Sowiso\SDK\Api\PlayHint\PlayHintCallback;
use Sowiso\SDK\Api\PlayHint\PlayHintRequestHandler;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;
use Sowiso\SDK\Tests\Fixtures\PlayHint;

it('makes request correctly', function (string $uri, array $request, mixed $response, callable|null $useApi) {
    makesRequestCorrectly(
        method: 'GET',
        uri: $uri,
        request: $request,
        response: $response,
        context: context(),
        useApi: $useApi,
    );
})->with([
    'default' => [
        PlayHint::Uri,
        PlayHint::Request,
        PlayHint::Response,
    ],
    'without language' => [
        PlayHint::UriWithoutLanguage,
        PlayHint::RequestWithoutLanguage,
        PlayHint::Response
    ],
])->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayHintRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayHintRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('makes request correctly with request handler', function () {
    makesRequestWithRequestHandlerCorrectly(
        request: PlayHint::Request,
        callbackName: PlayHintCallback::class,
        responseCaptor: function (PlayHintResponse $response) {
            expect($response->getData()['random_value'])->toBe(12345);
        },
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayHintRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayHintRequest $request): ?array
                {
                    $response = PlayHint::Response;
                    $response['random_value'] = 12345;

                    return $response;
                }
            }
        ),
    );
});

it('runs all callback methods correctly', function (callable|null $useApi) {
    runsAllCallbackMethodsCorrectly(
        uri: PlayHint::Uri,
        request: PlayHint::Request,
        response: PlayHint::Response,
        callbackName: PlayHintCallback::class,
        requestCaptor: function (PlayHintRequest $request) {
            expect($request)
                ->getLanguage()->toBe(PlayHint::Request['lang'])
                ->getTryId()->toBe(PlayHint::Request['try_id']);
        },
        responseCaptor: function (PlayHintResponse $response) {
        },
        context: context(),
        useApi: $useApi,
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayHintRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayHintRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('runs onFailure callback method correctly on missing data', function () {
    $request = PlayHint::Request;
    unset($request['try_id']);

    runsOnFailureCallbackMethodCorrectlyOnMissingData(
        request: $request,
        callbackName: PlayHintCallback::class,
        context: context(),
    );
});

it('runs onFailure callback method correctly on invalid response', function () {
    runsOnFailureCallbackMethodCorrectlyOnException(
        uri: PlayHint::Uri,
        request: PlayHint::Request,
        response: '',
        callbackName: PlayHintCallback::class,
        exceptionName: InvalidJsonResponseException::class,
        context: context(),
    );
});

it('fails on missing request try_id', function () {
    $request = PlayHint::Request;
    unset($request['try_id']);

    failsOnMissingRequestData(
        request: $request,
        missingFieldName: 'PlayHintRequest::tryId',
        context: context(),
    );
});
