<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetRequest;
use Sowiso\SDK\Api\PlayExerciseSet\Http\PlayExerciseSetResponse;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;

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
        PlayExerciseSet::Uri,
        PlayExerciseSet::Request,
        PlayExerciseSet::Response,
    ],
    'readonly view' => [
        PlayExerciseSet::UriReadonlyView,
        PlayExerciseSet::RequestReadonlyView,
        PlayExerciseSet::ResponseReadonlyView
    ],
    'without view' => [
        PlayExerciseSet::Uri,
        PlayExerciseSet::RequestWithoutView,
        PlayExerciseSet::Response
    ],
    'without language' => [
        PlayExerciseSet::UriWithoutLanguage,
        PlayExerciseSet::RequestWithoutLanguage,
        PlayExerciseSet::Response
    ],
]);

it('runs all callback methods correctly', function () {
    $context = contextWithUsername();

    runsAllCallbackMethodsCorrectly(
        uri: PlayExerciseSet::Uri,
        request: PlayExerciseSet::Request,
        response: PlayExerciseSet::Response,
        callbackName: PlayExerciseSetCallback::class,
        requestCaptor: function (PlayExerciseSetRequest $request) use ($context) {
            expect($request)
                ->getUsername()->toBe($context->getUsername())
                ->getLanguage()->toBe(PlayExerciseSet::Request['lang'])
                ->getView()->toBe(PlayExerciseSet::Request['view'])
                ->getSetId()->toBe(PlayExerciseSet::Request['set_id']);
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
    );
});

it('runs all callback methods in readonly view correctly', function () {
    $context = contextWithUsername();

    runsAllCallbackMethodsCorrectly(
        uri: PlayExerciseSet::UriReadonlyView,
        request: PlayExerciseSet::RequestReadonlyView,
        response: PlayExerciseSet::ResponseReadonlyView,
        callbackName: PlayExerciseSetCallback::class,
        requestCaptor: function (PlayExerciseSetRequest $request) use ($context) {
            expect($request)
                ->getUsername()->toBe($context->getUsername())
                ->getLanguage()->toBe(PlayExerciseSet::RequestReadonlyView['lang'])
                ->getView()->toBe(PlayExerciseSet::RequestReadonlyView['view'])
                ->getSetId()->toBe(PlayExerciseSet::RequestReadonlyView['set_id']);
        },
        responseCaptor: function (PlayExerciseSetResponse $response) {
            expect($response->getExerciseTries())->toBe([]);
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
        exceptionName: InvalidJsonDataException::class,
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
