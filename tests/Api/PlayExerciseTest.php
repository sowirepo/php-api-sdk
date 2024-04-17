<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseRequest;
use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseResponse;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseCallback;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseRequestHandler;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;
use Sowiso\SDK\Tests\Fixtures\PlayExercise;

it('makes request correctly', function (string $uri, array $request, mixed $response, callable|null $useApi) {
    makesRequestCorrectly(
        method: 'GET',
        uri: $uri,
        request: $request,
        response: $response,
        context: contextWithUsername(),
        useApi: $useApi,
    );
})->with([
    'default' => [
        PlayExercise::Uri,
        PlayExercise::Request,
        PlayExercise::Response,
    ],
    'without view' => [
        PlayExercise::Uri,
        PlayExercise::RequestWithoutView,
        PlayExercise::Response
    ],
    'with invalid view' => [
        PlayExercise::Uri,
        PlayExercise::RequestWithInvalidView,
        PlayExercise::Response
    ],
    'without language' => [
        PlayExercise::UriWithoutLanguage,
        PlayExercise::RequestWithoutLanguage,
        PlayExercise::Response
    ],
])->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayExerciseRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('makes request correctly with request handler', function () {
    makesRequestWithRequestHandlerCorrectly(
        request: PlayExercise::Request,
        callbackName: PlayExerciseCallback::class,
        responseCaptor: function (PlayExerciseResponse $response) {
            expect($response->getData()['random_value'])->toBe(12345);
        },
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayExerciseRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseRequest $request): ?array
                {
                    $response = PlayExercise::Response;
                    $response['random_value'] = 12345;

                    return $response;
                }
            }
        ),
    );
});

it('runs all callback methods correctly', function (callable|null $useApi) {
    $context = contextWithUsername();

    runsAllCallbackMethodsCorrectly(
        uri: PlayExercise::Uri,
        request: PlayExercise::Request,
        response: PlayExercise::Response,
        callbackName: PlayExerciseCallback::class,
        requestCaptor: function (PlayExerciseRequest $request) use ($context) {
            expect($request)
                ->getUser()->toBe($context->getUser())
                ->getLanguage()->toBe(PlayExercise::Request['lang'])
                ->getView()->toBe(PlayExercise::Request['view'])
                ->getTryId()->toBe(PlayExercise::Request['try_id']);
        },
        responseCaptor: function (PlayExerciseResponse $response) {
        },
        context: $context,
        useApi: $useApi,
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayExerciseRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('runs onFailure callback method correctly on missing data', function () {
    $request = PlayExercise::Request;
    unset($request['try_id']);

    runsOnFailureCallbackMethodCorrectlyOnMissingData(
        request: $request,
        callbackName: PlayExerciseCallback::class,
        context: contextWithUsername(),
    );
});

it('runs onFailure callback method correctly on invalid response', function () {
    runsOnFailureCallbackMethodCorrectlyOnException(
        uri: PlayExercise::Uri,
        request: PlayExercise::Request,
        response: '',
        callbackName: PlayExerciseCallback::class,
        exceptionName: InvalidJsonResponseException::class,
        context: contextWithUsername(),
    );
});

it('fails on missing request try_id', function () {
    $request = PlayExercise::Request;
    unset($request['try_id']);

    failsOnMissingRequestData(
        request: $request,
        missingFieldName: 'PlayExerciseRequest::tryId',
        context: contextWithUsername(),
    );
});
