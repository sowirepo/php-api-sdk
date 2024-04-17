<?php

declare(strict_types=1);

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerRequestHandler;
use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerRequest;
use Sowiso\SDK\Api\EvaluateAnswer\Http\EvaluateAnswerResponse;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseSetBePlayedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseTryBeEvaluatedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseTryBePlayedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\TestModeHook;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;

it('makes request correctly', function (callable|null $useApi) {
    makesRequestCorrectly(
        method: 'POST',
        uri: EvaluateAnswer::Uri,
        request: EvaluateAnswer::Request,
        response: EvaluateAnswer::Response,
        useApi: $useApi,
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends EvaluateAnswerRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, EvaluateAnswerRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('makes request correctly with request handler', function () {
    makesRequestWithRequestHandlerCorrectly(
        request: EvaluateAnswer::Request,
        callbackName: EvaluateAnswerCallback::class,
        responseCaptor: function (EvaluateAnswerResponse $response) {
            expect($response->getData()['random_value'])->toBe(12345);
        },
        useApi: fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends EvaluateAnswerRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, EvaluateAnswerRequest $request): ?array
                {
                    $response = EvaluateAnswer::Response;
                    $response['random_value'] = 12345;

                    return $response;
                }
            }
        ),
    );
});

it('makes request correctly in "test" mode', function () {
    makesRequestCorrectly(
        method: 'POST',
        uri: EvaluateAnswer::UriInTestMode,
        request: EvaluateAnswer::Request,
        response: EvaluateAnswer::Response,
        useApi: fn (SowisoApi $api) => $api->useHook(
            new class () extends TestModeHook {
                public function shouldExerciseSetBePlayedInTestMode(ShouldExerciseSetBePlayedInTestModeData $data): bool
                {
                    return false; // Not needed in the EvaluateAnswerEndpoint
                }

                public function shouldExerciseTryBePlayedInTestMode(ShouldExerciseTryBePlayedInTestModeData $data): bool
                {
                    return false; // Not needed in the EvaluateAnswerEndpoint
                }

                public function shouldExerciseTryBeEvaluatedInTestMode(ShouldExerciseTryBeEvaluatedInTestModeData $data): bool
                {
                    return true;
                }
            }
        ),
    );
});

it('runs all callback methods correctly', function (callable|null $useApi) {
    runsAllCallbackMethodsCorrectly(
        uri: EvaluateAnswer::Uri,
        request: EvaluateAnswer::Request,
        response: EvaluateAnswer::Response,
        callbackName: EvaluateAnswerCallback::class,
        requestCaptor: function (EvaluateAnswerRequest $request) {
            expect($request)
                ->getTryId()->toBe(EvaluateAnswer::Request['try_id']);
        },
        responseCaptor: function (EvaluateAnswerResponse $response) {
            expect($response)
                ->isCompleted()->toBe(EvaluateAnswer::Response['exercise_evaluation']['completed'])
                ->getScore()->toBe(EvaluateAnswer::Response['exercise_evaluation']['score']);
        },
        useApi: $useApi,
    );
})->with([
    'default' => [null],
    'with empty request handler' => [
        fn () => fn (SowisoApi $api) => $api->useRequestHandler(
            new class () extends EvaluateAnswerRequestHandler {
                public function handle(SowisoApiContext $context, SowisoApiPayload $payload, EvaluateAnswerRequest $request): ?array
                {
                    return null;
                }
            }
        )
    ],
]);

it('runs onFailure callback method correctly on missing data', function () {
    $request = EvaluateAnswer::Request;
    unset($request['try_id']);

    runsOnFailureCallbackMethodCorrectlyOnMissingData(
        request: $request,
        callbackName: EvaluateAnswerCallback::class,
    );
});

it('runs onFailure callback method correctly on invalid response', function () {
    runsOnFailureCallbackMethodCorrectlyOnException(
        uri: EvaluateAnswer::Uri,
        request: EvaluateAnswer::Request,
        response: '',
        callbackName: EvaluateAnswerCallback::class,
        exceptionName: InvalidJsonResponseException::class,
    );
});

it('fails on missing request try_id', function () {
    $request = EvaluateAnswer::Request;
    unset($request['try_id']);

    failsOnMissingRequestData(
        request: $request,
        missingFieldName: 'EvaluateAnswerRequest::tryId',
    );
});

it('fails on missing response state', function () {
    $response = EvaluateAnswer::Response;
    unset($response['exercise_evaluation']['completed']);

    failsOnMissingResponseData(
        uri: EvaluateAnswer::Uri,
        request: EvaluateAnswer::Request,
        response: $response,
        missingFieldName: 'EvaluateAnswerResponse::completed',
    );
});

it('fails on missing response score', function () {
    $response = EvaluateAnswer::Response;
    unset($response['exercise_evaluation']['score']);

    failsOnMissingResponseData(
        uri: EvaluateAnswer::Uri,
        request: EvaluateAnswer::Request,
        response: $response,
        missingFieldName: 'EvaluateAnswerResponse::score',
    );
});
