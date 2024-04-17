<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionRequest;
use Sowiso\SDK\Api\PlaySolution\Http\PlaySolutionResponse;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionCallback;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionRequestHandler;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;
use Sowiso\SDK\Tests\Fixtures\PlaySolution;

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
        PlaySolution::Uri,
        PlaySolution::Request,
        PlaySolution::Response,
    ],
    'without language' => [
        PlaySolution::UriWithoutLanguage,
        PlaySolution::RequestWithoutLanguage,
        PlaySolution::Response
    ],
])->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlaySolutionRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlaySolutionRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('makes request correctly with request handler', function () {
    makesRequestWithRequestHandlerCorrectly(
        request: PlaySolution::Request,
        callbackName: PlaySolutionCallback::class,
        responseCaptor: function (PlaySolutionResponse $response) {
            expect($response->getData()['random_value'])->toBe(12345);
        },
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlaySolutionRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlaySolutionRequest $request): ?array
                {
                    $response = PlaySolution::Response;
                    $response['random_value'] = 12345;

                    return $response;
                }
            }
        ),
    );
});

it('runs all callback methods correctly', function (callable|null $useApi) {
    runsAllCallbackMethodsCorrectly(
        uri: PlaySolution::Uri,
        request: PlaySolution::Request,
        response: PlaySolution::Response,
        callbackName: PlaySolutionCallback::class,
        requestCaptor: function (PlaySolutionRequest $request) {
            expect($request)
                ->getLanguage()->toBe(PlaySolution::Request['lang'])
                ->getTryId()->toBe(PlaySolution::Request['try_id']);
        },
        responseCaptor: function (PlaySolutionResponse $response) {
            expect($response)
                ->isCompleted()->toBe(PlaySolution::Response['completed'])
                ->getScore()->toBe(PlaySolution::Response['score']);
        },
        context: context(),
        useApi: $useApi,
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlaySolutionRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlaySolutionRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('runs onFailure callback method correctly on missing data', function () {
    $request = PlaySolution::Request;
    unset($request['try_id']);

    runsOnFailureCallbackMethodCorrectlyOnMissingData(
        request: $request,
        callbackName: PlaySolutionCallback::class,
        context: context(),
    );
});

it('runs onFailure callback method correctly on invalid response', function () {
    runsOnFailureCallbackMethodCorrectlyOnException(
        uri: PlaySolution::Uri,
        request: PlaySolution::Request,
        response: '',
        callbackName: PlaySolutionCallback::class,
        exceptionName: InvalidJsonResponseException::class,
        context: context(),
    );
});

it('fails on missing request try_id', function () {
    $request = PlaySolution::Request;
    unset($request['try_id']);

    failsOnMissingRequestData(
        request: $request,
        missingFieldName: 'PlaySolutionRequest::tryId',
        context: context(),
    );
});

//it('fails on missing response state', function () {
//    $response = PlaySolution::Response;
//    unset($response['completed']);
//
//    failsOnMissingResponseData(
//        uri: PlaySolution::Uri,
//        request: PlaySolution::Request,
//        response: $response,
//        missingFieldName: 'PlaySolutionResponse::completed',
//    );
//});
//
//it('fails on missing response score', function () {
//    $response = PlaySolution::Response;
//    unset($response['score']);
//
//    failsOnMissingResponseData(
//        uri: PlaySolution::Uri,
//        request: PlaySolution::Request,
//        response: $response,
//        missingFieldName: 'PlaySolutionResponse::score',
//    );
//});
