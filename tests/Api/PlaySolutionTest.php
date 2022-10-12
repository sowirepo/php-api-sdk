<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlaySolution\PlaySolutionCallback;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionRequest;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionResponse;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Tests\Fixtures\PlaySolution;

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
        PlaySolution::Uri,
        PlaySolution::Request,
        PlaySolution::Response,
    ],
    'without language' => [
        PlaySolution::UriWithoutLanguage,
        PlaySolution::RequestWithoutLanguage,
        PlaySolution::Response
    ],
]);

it('runs all callback methods correctly', function () {
    runsAllCallbackMethodsCorrectly(
        uri: PlaySolution::Uri,
        request: PlaySolution::Request,
        response: PlaySolution::Response,
        callbackName: PlaySolutionCallback::class,
        requestCaptor: function (PlaySolutionRequest $request) {
            expect($request)
                ->getLanguage()->toBe(PlaySolution::Request['lang'])
                ->getTryId()->toBe(PlaySolution::Request['try_id']);
        },
        responseCaptor: function (PlaySolutionResponse $response) {
            expect($response)
                ->isCompleted()->toBe(PlaySolution::Response['completed'])
                ->getScore()->toBe(PlaySolution::Response['score']);
        },
        context: context(),
    );
});

it('runs onFailure callback method correctly on missing data', function () {
    $request = PlaySolution::Request;
    unset($request['try_id']);

    runsOnFailureCallbackMethodCorrectlyOnMissingData(
        request: $request,
        callbackName: PlaySolutionCallback::class,
        context: context(),
    );
});

it('runs onFailure callback method correctly on invalid response', function () {
    runsOnFailureCallbackMethodCorrectlyOnException(
        uri: PlaySolution::Uri,
        request: PlaySolution::Request,
        response: '',
        callbackName: PlaySolutionCallback::class,
        exceptionName: InvalidJsonDataException::class,
        context: context(),
    );
});

it('fails on missing request try_id', function () {
    $request = PlaySolution::Request;
    unset($request['try_id']);

    failsOnMissingRequestData(
        request: $request,
        missingFieldName: 'PlaySolutionRequest::tryId',
        context: context(),
    );
});

//it('fails on missing response state', function () {
//    $response = PlaySolution::Response;
//    unset($response['completed']);
//
//    failsOnMissingResponseData(
//        uri: PlaySolution::Uri,
//        request: PlaySolution::Request,
//        response: $response,
//        missingFieldName: 'PlaySolutionResponse::completed',
//    );
//});
//
//it('fails on missing response score', function () {
//    $response = PlaySolution::Response;
//    unset($response['score']);
//
//    failsOnMissingResponseData(
//        uri: PlaySolution::Uri,
//        request: PlaySolution::Request,
//        response: $response,
//        missingFieldName: 'PlaySolutionResponse::score',
//    );
//});
