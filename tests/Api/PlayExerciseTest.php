<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseRequest;
use Sowiso\SDK\Api\PlayExercise\Http\PlayExerciseResponse;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseCallback;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Tests\Fixtures\PlayExercise;

it('makes request correctly', function (string $uri, array $request, mixed $response) {
    makesRequestCorrectly(
        method: 'GET',
        uri: $uri,
        request: $request,
        response: $response,
        context: contextWithUsername(),
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
    'without language' => [
        PlayExercise::UriWithoutLanguage,
        PlayExercise::RequestWithoutLanguage,
        PlayExercise::Response
    ],
]);

it('runs all callback methods correctly', function () {
    $context = contextWithUsername();

    runsAllCallbackMethodsCorrectly(
        uri: PlayExercise::Uri,
        request: PlayExercise::Request,
        response: PlayExercise::Response,
        callbackName: PlayExerciseCallback::class,
        requestCaptor: function (PlayExerciseRequest $request) use ($context) {
            expect($request)
                ->getUsername()->toBe($context->getUsername())
                ->getLanguage()->toBe(PlayExercise::Request['lang'])
                ->getView()->toBe(PlayExercise::Request['view'])
                ->getTryId()->toBe(PlayExercise::Request['try_id']);
        },
        responseCaptor: function (PlayExerciseResponse $response) {
        },
        context: $context,
    );
});

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
        exceptionName: InvalidJsonDataException::class,
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
