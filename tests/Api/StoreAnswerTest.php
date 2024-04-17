<?php

declare(strict_types=1);

use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerRequest;
use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerResponse;
use Sowiso\SDK\Api\StoreAnswer\StoreAnswerCallback;
use Sowiso\SDK\Api\StoreAnswer\StoreAnswerRequestHandler;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;
use Sowiso\SDK\Tests\Fixtures\StoreAnswer;

it('makes request correctly', function (callable|null $useApi) {
    makesRequestCorrectly(
        method: 'POST',
        uri: StoreAnswer::Uri,
        request: StoreAnswer::Request,
        response: StoreAnswer::Response,
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends StoreAnswerRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, StoreAnswerRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('makes request correctly with request handler', function () {
    makesRequestWithRequestHandlerCorrectly(
        request: StoreAnswer::Request,
        callbackName: StoreAnswerCallback::class,
        responseCaptor: function (StoreAnswerResponse $response) {
            expect($response->getData()['random_value'])->toBe(12345);
        },
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends StoreAnswerRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, StoreAnswerRequest $request): ?array
                {
                    $response = StoreAnswer::Response;
                    $response['random_value'] = 12345;

                    return $response;
                }
            }
        ),
    );
});

it('runs all callback methods correctly', function (callable|null $useApi) {
    runsAllCallbackMethodsCorrectly(
        uri: StoreAnswer::Uri,
        request: StoreAnswer::Request,
        response: StoreAnswer::Response,
        callbackName: StoreAnswerCallback::class,
        requestCaptor: function (StoreAnswerRequest $request) {
            expect($request)
                ->getTryId()->toBe(StoreAnswer::Request['try_id']);
        },
        responseCaptor: function (StoreAnswerResponse $response) {
        },
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends StoreAnswerRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, StoreAnswerRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('runs onFailure callback method correctly on missing data', function () {
    $request = StoreAnswer::Request;
    unset($request['try_id']);

    runsOnFailureCallbackMethodCorrectlyOnMissingData(
        request: $request,
        callbackName: StoreAnswerCallback::class,
    );
});

it('runs onFailure callback method correctly on invalid response', function () {
    runsOnFailureCallbackMethodCorrectlyOnException(
        uri: StoreAnswer::Uri,
        request: StoreAnswer::Request,
        response: '',
        callbackName: StoreAnswerCallback::class,
        exceptionName: InvalidJsonResponseException::class,
    );
});

it('fails on missing request try_id', function () {
    $request = StoreAnswer::Request;
    unset($request['try_id']);

    failsOnMissingRequestData(
        request: $request,
        missingFieldName: 'StoreAnswerRequest::tryId',
    );
});
