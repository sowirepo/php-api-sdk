<?php

declare(strict_types=1);

use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryRequest;
use Sowiso\SDK\Api\ReplayExerciseTry\Http\ReplayExerciseTryResponse;
use Sowiso\SDK\Api\ReplayExerciseTry\ReplayExerciseTryCallback;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\Tests\Fixtures\ReplayExerciseTry;

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
        ReplayExerciseTry::Uri,
        ReplayExerciseTry::Request,
        ReplayExerciseTry::Response,
    ],
    'without language' => [
        ReplayExerciseTry::UriWithoutLanguage,
        ReplayExerciseTry::RequestWithoutLanguage,
        ReplayExerciseTry::Response
    ],
]);

it('runs all callback methods correctly', function () {
    runsAllCallbackMethodsCorrectly(
        uri: ReplayExerciseTry::Uri,
        request: ReplayExerciseTry::Request,
        response: ReplayExerciseTry::Response,
        callbackName: ReplayExerciseTryCallback::class,
        requestCaptor: function (ReplayExerciseTryRequest $request) {
            expect($request)
                ->getLanguage()->toBe(ReplayExerciseTry::Request['lang'])
                ->getTryId()->toBe(ReplayExerciseTry::Request['try_id']);
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
