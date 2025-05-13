<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetResponse;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetRequestHandler;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseSetBePlayedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseTryBeEvaluatedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseTryBePlayedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\TestModeHook;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;

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
        PlayExerciseSet::Uri,
        PlayExerciseSet::Request,
        PlayExerciseSet::Response,
    ],
    'readonly view' => [
        PlayExerciseSet::UriReadonlyView,
        PlayExerciseSet::RequestReadonlyView,
        PlayExerciseSet::ResponseReadonlyView,
    ],
    'readonly-restricted view' => [
        PlayExerciseSet::UriReadonlyRestrictedView,
        PlayExerciseSet::RequestReadonlyRestrictedView,
        PlayExerciseSet::ResponseReadonlyRestrictedView,
    ],
    'without view' => [
        PlayExerciseSet::Uri,
        PlayExerciseSet::RequestWithoutView,
        PlayExerciseSet::Response,
    ],
    'with invalid view' => [
        PlayExerciseSet::Uri,
        PlayExerciseSet::RequestWithInvalidView,
        PlayExerciseSet::Response,
    ],
    'without language' => [
        PlayExerciseSet::UriWithoutLanguage,
        PlayExerciseSet::RequestWithoutLanguage,
        PlayExerciseSet::Response,
    ],
    'with try_id' => [
        PlayExerciseSet::UriWithTryId,
        PlayExerciseSet::RequestWithTryId,
        PlayExerciseSet::ResponseWithTryId,
    ],
    'with try_id without view' => [
        PlayExerciseSet::UriWithTryId,
        PlayExerciseSet::RequestWithTryIdWithoutView,
        PlayExerciseSet::ResponseWithTryId,
    ],
    'with try_id without language' => [
        PlayExerciseSet::UriWithTryIdWithoutLanguage,
        PlayExerciseSet::RequestWithTryIdWithoutLanguage,
        PlayExerciseSet::ResponseWithTryId,
    ],
    'with try_id with explicit practice mode' => [
        PlayExerciseSet::UriWithTryId,
        PlayExerciseSet::RequestWithTryIdAndPracticeMode,
        PlayExerciseSet::ResponseWithTryId,
    ],
    'with try_id with print mode' => [
        PlayExerciseSet::UriWithTryIdInPrintMode,
        PlayExerciseSet::RequestWithTryIdAndPrintMode,
        PlayExerciseSet::ResponseWithTryId,
    ],
])->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayExerciseSetRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseSetRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('makes request correctly with request handler', function () {
    makesRequestWithRequestHandlerCorrectly(
        request: PlayExerciseSet::Request,
        callbackName: PlayExerciseSetCallback::class,
        responseCaptor: function (PlayExerciseSetResponse $response) {
            expect($response->getData()[0]['random_value'])->toBe(12345);
        },
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayExerciseSetRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseSetRequest $request): ?array
                {
                    $response = PlayExerciseSet::Response;
                    $response[0]['random_value'] = 12345;

                    return $response;
                }
            }
        ),
    );
});

it('makes request correctly in "test" mode', function () {
    makesRequestCorrectly(
        method: 'GET',
        uri: PlayExerciseSet::UriInTestMode,
        request: PlayExerciseSet::Request,
        response: PlayExerciseSet::Response,
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useHook(new class () extends TestModeHook {
            public function shouldExerciseSetBePlayedInTestMode(ShouldExerciseSetBePlayedInTestModeData $data): bool
            {
                return true;
            }

            public function shouldExerciseTryBePlayedInTestMode(ShouldExerciseTryBePlayedInTestModeData $data): bool
            {
                return false; // Not needed for request with set_id
            }

            public function shouldExerciseTryBeEvaluatedInTestMode(ShouldExerciseTryBeEvaluatedInTestModeData $data): bool
            {
                return false; // Not needed in the PlayExerciseSetEndpoint
            }
        }),
    );
});

it('makes request with try_id correctly in "test" mode', function () {
    makesRequestCorrectly(
        method: 'GET',
        uri: PlayExerciseSet::UriWithTryIdInTestMode,
        request: PlayExerciseSet::RequestWithTryId,
        response: PlayExerciseSet::ResponseWithTryId,
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useHook(new class () extends TestModeHook {
            public function shouldExerciseSetBePlayedInTestMode(ShouldExerciseSetBePlayedInTestModeData $data): bool
            {
                return false; // Not needed for request with try_id
            }

            public function shouldExerciseTryBePlayedInTestMode(ShouldExerciseTryBePlayedInTestModeData $data): bool
            {
                return true;
            }

            public function shouldExerciseTryBeEvaluatedInTestMode(ShouldExerciseTryBeEvaluatedInTestModeData $data): bool
            {
                return false; // Not needed in the PlayExerciseSetEndpoint
            }
        }),
    );
});

it('makes request with try_id correctly in "print" mode with disabled "test" mode', function () {
    makesRequestCorrectly(
        method: 'GET',
        uri: PlayExerciseSet::UriWithTryIdInPrintMode,
        request: PlayExerciseSet::RequestWithTryIdAndPrintMode,
        response: PlayExerciseSet::ResponseWithTryId,
        context: contextWithUsername(),
        useApi: fn (SowisoApi $api) => $api->useHook(new class () extends TestModeHook {
            public function shouldExerciseSetBePlayedInTestMode(ShouldExerciseSetBePlayedInTestModeData $data): bool
            {
                return false; // Not needed for request with try_id
            }

            public function shouldExerciseTryBePlayedInTestMode(ShouldExerciseTryBePlayedInTestModeData $data): bool
            {
                return false;
            }

            public function shouldExerciseTryBeEvaluatedInTestMode(ShouldExerciseTryBeEvaluatedInTestModeData $data): bool
            {
                return false; // Not needed in the PlayExerciseSetEndpoint
            }
        }),
    );
});

it('runs all callback methods correctly', function (callable|null $useApi) {
    $context = contextWithUsername();

    runsAllCallbackMethodsCorrectly(
        uri: PlayExerciseSet::Uri,
        request: PlayExerciseSet::Request,
        response: PlayExerciseSet::Response,
        callbackName: PlayExerciseSetCallback::class,
        requestCaptor: function (PlayExerciseSetRequest $request) use ($context) {
            expect($request)
                ->getUser()->toBe($context->getUser())
                ->getLanguage()->toBe(PlayExerciseSet::Request['lang'])
                ->getView()->toBe(PlayExerciseSet::Request['view'])
                ->getSetId()->toBe(PlayExerciseSet::Request['set_id'])
                ->usesTryId()->toBe(false);
        },
        responseCaptor: function (PlayExerciseSetResponse $response) {
            expect($response->getExerciseTries())->sequence(
                fn ($value) => $value->toMatchArray([
                    'exerciseId' => PlayExerciseSet::Response[0]['exercise_id'],
                    'tryId' => PlayExerciseSet::Response[0]['try_id'],
                ]),
                fn ($value) => $value->toMatchArray([
                    'exerciseId' => PlayExerciseSet::Response[1]['exercise_id'],
                    'tryId' => PlayExerciseSet::Response[1]['try_id'],
                ]),
            );
        },
        context: $context,
        useApi: $useApi,
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends PlayExerciseSetRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseSetRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('runs all callback methods in readonly view correctly', function () {
    $context = contextWithUsername();

    runsAllCallbackMethodsCorrectly(
        uri: PlayExerciseSet::UriReadonlyView,
        request: PlayExerciseSet::RequestReadonlyView,
        response: PlayExerciseSet::ResponseReadonlyView,
        callbackName: PlayExerciseSetCallback::class,
        requestCaptor: function (PlayExerciseSetRequest $request) use ($context) {
            expect($request)
                ->getUser()->toBe($context->getUser())
                ->getLanguage()->toBe(PlayExerciseSet::RequestReadonlyView['lang'])
                ->getView()->toBe(PlayExerciseSet::RequestReadonlyView['view'])
                ->getSetId()->toBe(PlayExerciseSet::RequestReadonlyView['set_id'])
                ->usesTryId()->toBe(false);
        },
        responseCaptor: function (PlayExerciseSetResponse $response) {
            expect($response->getExerciseTries())->toBe([]);
        },
        context: $context,
    );
});

it('runs all callback methods in readonly-restricted view correctly', function () {
    $context = contextWithUsername();

    runsAllCallbackMethodsCorrectly(
        uri: PlayExerciseSet::UriReadonlyRestrictedView,
        request: PlayExerciseSet::RequestReadonlyRestrictedView,
        response: PlayExerciseSet::ResponseReadonlyRestrictedView,
        callbackName: PlayExerciseSetCallback::class,
        requestCaptor: function (PlayExerciseSetRequest $request) use ($context) {
            expect($request)
                ->getUser()->toBe($context->getUser())
                ->getLanguage()->toBe(PlayExerciseSet::RequestReadonlyRestrictedView['lang'])
                ->getView()->toBe(PlayExerciseSet::RequestReadonlyRestrictedView['view'])
                ->getSetId()->toBe(PlayExerciseSet::RequestReadonlyRestrictedView['set_id'])
                ->usesTryId()->toBe(false);
        },
        responseCaptor: function (PlayExerciseSetResponse $response) {
            expect($response->getExerciseTries())->toBe([]);
        },
        context: $context,
    );
});

it('runs all callback methods with try_id correctly', function () {
    $context = contextWithUsername();

    runsAllCallbackMethodsCorrectly(
        uri: PlayExerciseSet::UriWithTryId,
        request: PlayExerciseSet::RequestWithTryId,
        response: PlayExerciseSet::ResponseWithTryId,
        callbackName: PlayExerciseSetCallback::class,
        requestCaptor: function (PlayExerciseSetRequest $request) use ($context) {
            expect($request)
                ->getUser()->toBe($context->getUser())
                ->getLanguage()->toBe(PlayExerciseSet::RequestWithTryId['lang'])
                ->getView()->toBe(PlayExerciseSet::RequestWithTryId['view'])
                ->getTryId()->toBe(PlayExerciseSet::RequestWithTryId['try_id'])
                ->getSetId()->toBe(null)
                ->usesTryId()->toBe(true);
        },
        responseCaptor: function (PlayExerciseSetResponse $response) {
            expect($response->getExerciseTries())->sequence(
                fn ($value) => $value->toMatchArray([
                    'exerciseId' => PlayExerciseSet::ResponseWithTryId[0]['exercise_id'],
                    'tryId' => PlayExerciseSet::ResponseWithTryId[0]['try_id'],
                ]),
                fn ($value) => $value->toMatchArray([
                    'exerciseId' => PlayExerciseSet::ResponseWithTryId[1]['exercise_id'],
                    'tryId' => PlayExerciseSet::ResponseWithTryId[1]['try_id'],
                ]),
            );
        },
        context: $context,
    );
});

it('runs onFailure callback method correctly on missing data', function () {
    $request = PlayExerciseSet::Request;
    unset($request['set_id']);

    runsOnFailureCallbackMethodCorrectlyOnMissingData(
        request: $request,
        callbackName: PlayExerciseSetCallback::class,
        context: contextWithUsername(),
    );
});

it('runs onFailure callback method correctly on invalid response', function () {
    runsOnFailureCallbackMethodCorrectlyOnException(
        uri: PlayExerciseSet::Uri,
        request: PlayExerciseSet::Request,
        response: '',
        callbackName: PlayExerciseSetCallback::class,
        exceptionName: InvalidJsonResponseException::class,
        context: contextWithUsername(),
    );
});

it('fails on invalid request with set_id and try_id', function () {
    failsOnInvalidData(
        request: PlayExerciseSet::RequestWithSetIdAndTryId,
        message: "InvalidData 'setId and tryId supplied'",
        context: contextWithUsername(),
    );
});

it('fails on invalid request with explicit "test" mode', function () {
    failsOnInvalidData(
        request: PlayExerciseSet::RequestWithTryIdAndTestMode,
        message: "InvalidData 'mode=test supplied, use TestModeHook instead'",
        context: contextWithUsername(),
    );
});

it('fails on invalid request with set_id and try_id and explicit "test" mode', function () {
    failsOnInvalidData(
        request: PlayExerciseSet::RequestWithSetIdAndTryIdAndTestMode,
        message: "InvalidData 'mode=test supplied, use TestModeHook instead'",
        context: contextWithUsername(),
    );
});

it('fails on missing request set_id', function () {
    $request = PlayExerciseSet::Request;
    unset($request['set_id']);

    failsOnMissingRequestData(
        request: $request,
        missingFieldName: 'PlayExerciseSetRequest::setId',
        context: contextWithUsername(),
    );
});

it('fails on missing response try_id\'s', function () {
    $response = PlayExerciseSet::Response;
    unset($response[0]['try_id']);

    failsOnMissingResponseData(
        uri: PlayExerciseSet::Uri,
        request: PlayExerciseSet::Request,
        response: $response,
        missingFieldName: 'PlayExerciseSetResponse::exerciseTries',
        context: contextWithUsername(),
    );
});

it('fails on missing response exercise_id\'s', function () {
    $response = PlayExerciseSet::Response;
    unset($response[0]['exercise_id']);

    failsOnMissingResponseData(
        uri: PlayExerciseSet::Uri,
        request: PlayExerciseSet::Request,
        response: $response,
        missingFieldName: 'PlayExerciseSetResponse::exerciseTries',
        context: contextWithUsername(),
    );
});
