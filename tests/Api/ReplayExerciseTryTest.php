<?php

declare(strict_types=1);

use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryRequest;
use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryResponse;
use Sowiso\SDK\Api\ReplayExerciseTry\ReplayExerciseTryCallback;
use Sowiso\SDK\Api\ReplayExerciseTry\ReplayExerciseTryRequestHandler;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;
use Sowiso\SDK\Tests\Fixtures\ReplayExerciseTry;

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
        ReplayExerciseTry::Uri,
        ReplayExerciseTry::Request,
        ReplayExerciseTry::Response,
    ],
    'without language' => [
        ReplayExerciseTry::UriWithoutLanguage,
        ReplayExerciseTry::RequestWithoutLanguage,
        ReplayExerciseTry::Response
    ],
    'with question mode' => [
        ReplayExerciseTry::Uri,
        ReplayExerciseTry::RequestWithQuestionMode,
        ReplayExerciseTry::Response,
    ],
    'with invalid mode' => [
        ReplayExerciseTry::Uri,
        ReplayExerciseTry::RequestWithInvalidMode,
        ReplayExerciseTry::Response,
    ],
])->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends ReplayExerciseTryRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, ReplayExerciseTryRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('makes request correctly with request handler', function () {
    makesRequestWithRequestHandlerCorrectly(
        request: ReplayExerciseTry::Request,
        callbackName: ReplayExerciseTryCallback::class,
        responseCaptor: function (ReplayExerciseTryResponse $response) {
            expect($response->getData()['random_value'])->toBe(12345);
        },
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends ReplayExerciseTryRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, ReplayExerciseTryRequest $request): ?array
                {
                    $response = ReplayExerciseTry::Response;
                    $response['random_value'] = 12345;

                    return $response;
                }
            }
        ),
    );
});

it('runs all callback methods correctly', function (callable|null $useApi) {
    runsAllCallbackMethodsCorrectly(
        uri: ReplayExerciseTry::Uri,
        request: ReplayExerciseTry::Request,
        response: ReplayExerciseTry::Response,
        callbackName: ReplayExerciseTryCallback::class,
        requestCaptor: function (ReplayExerciseTryRequest $request) {
            expect($request)
                ->getTryId()->toBe(ReplayExerciseTry::Request['try_id'])
                ->getLanguage()->toBe(ReplayExerciseTry::Request['lang'])
                ->getMode()->toBe('full')
                ->usesQuestionsMode()->toBe(false);
        },
        responseCaptor: function (ReplayExerciseTryResponse $response) {
        },
        context: context(),
        useApi: $useApi,
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends ReplayExerciseTryRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, ReplayExerciseTryRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('runs all callback methods in question mode correctly', function () {
    runsAllCallbackMethodsCorrectly(
        uri: ReplayExerciseTry::Uri,
        request: ReplayExerciseTry::RequestWithQuestionMode,
        response: ReplayExerciseTry::Response,
        callbackName: ReplayExerciseTryCallback::class,
        requestCaptor: function (ReplayExerciseTryRequest $request) {
            expect($request)
                ->getTryId()->toBe(ReplayExerciseTry::RequestWithQuestionMode['try_id'])
                ->getLanguage()->toBe(ReplayExerciseTry::RequestWithQuestionMode['lang'])
                ->getMode()->toBe('question')
                ->usesQuestionsMode()->toBe(true);
        },
        responseCaptor: function (ReplayExerciseTryResponse $response) {
        },
        context: context(),
    );
});

it('runs onFailure callback method correctly on missing data', function () {
    $request = ReplayExerciseTry::Request;
    unset($request['try_id']);

    runsOnFailureCallbackMethodCorrectlyOnMissingData(
        request: $request,
        callbackName: ReplayExerciseTryCallback::class,
        context: context(),
    );
});

it('runs onFailure callback method correctly on invalid response', function () {
    runsOnFailureCallbackMethodCorrectlyOnException(
        uri: ReplayExerciseTry::Uri,
        request: ReplayExerciseTry::Request,
        response: '',
        callbackName: ReplayExerciseTryCallback::class,
        exceptionName: InvalidJsonResponseException::class,
        context: context(),
    );
});

it('fails on missing request try_id', function () {
    $request = ReplayExerciseTry::Request;
    unset($request['try_id']);

    failsOnMissingRequestData(
        request: $request,
        missingFieldName: 'ReplayExerciseTryRequest::tryId',
        context: context(),
    );
});
