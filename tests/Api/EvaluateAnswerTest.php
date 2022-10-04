<?php

declare(strict_types=1);

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerRequest;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerResponse;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;

it('makes request correctly', function () {
    makesRequestCorrectly(
        method: 'POST',
        uri: EvaluateAnswer::Uri,
        request: EvaluateAnswer::Request,
        response: EvaluateAnswer::Response,
    );
});

it('runs all callback methods correctly', function () {
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
    );
});

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
        exceptionName: InvalidJsonDataException::class,
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
