<?php

declare(strict_types=1);

use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerRequest;
use Sowiso\SDK\Api\StoreAnswer\Http\StoreAnswerResponse;
use Sowiso\SDK\Api\StoreAnswer\StoreAnswerCallback;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Tests\Fixtures\StoreAnswer;

it('makes request correctly', function () {
    makesRequestCorrectly(
        method: 'POST',
        uri: StoreAnswer::Uri,
        request: StoreAnswer::Request,
        response: StoreAnswer::Response,
    );
});

it('runs all callback methods correctly', function () {
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
});

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
        exceptionName: InvalidJsonDataException::class,
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
