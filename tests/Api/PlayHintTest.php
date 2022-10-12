<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlayHint\PlayHintCallback;
use Sowiso\SDK\Api\PlayHint\PlayHintRequest;
use Sowiso\SDK\Api\PlayHint\PlayHintResponse;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Tests\Fixtures\PlayHint;

it('makes request correctly', function (string $uri, array $request, mixed $response) {
    makesRequestCorrectly(
        method: 'GET',
        uri: $uri,
        request: $request,
        response: $response,
        context: context(),
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
]);

it('runs all callback methods correctly', function () {
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
    );
});

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
        exceptionName: InvalidJsonDataException::class,
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
