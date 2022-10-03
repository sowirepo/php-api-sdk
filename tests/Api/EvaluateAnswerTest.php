<?php

declare(strict_types=1);

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;

it('runs all callback methods correctly', function () {
    $api = new SowisoApi(
        configuration: configuration(),
        httpClient: mockHttpClient(
            path: EvaluateAnswer::Uri,
            response: EvaluateAnswer::Response,
        ),
    );

    $context = context();

    $callback = mock(EvaluateAnswerCallback::class)->makePartial();

    $callback->expects('onRequest')
        ->withSomeOfArgs($context) // TODO: Check for data objects
        ->once();

    $callback->expects('onResponse')
        ->withSomeOfArgs($context) // TODO: Check for data objects
        ->once();

    $callback->expects('onSuccess')
        ->withSomeOfArgs($context) // TODO: Check for data objects
        ->once();

    $callback->expects('onFailure')
        ->never();

    $api->useCallback($callback);

    $api->request($context, json_encode(EvaluateAnswer::Request));
});

it('runs onFailure callback method correctly', function () {
    $api = api();
    $context = context();

    $callback = mock(EvaluateAnswerCallback::class)->makePartial();

    $callback->expects('onRequest')->never();
    $callback->expects('onResponse')->never();
    $callback->expects('onSuccess')->never();

    $callback->expects('onFailure')->once();

    $api->useCallback($callback);

    $request = EvaluateAnswer::Request;

    unset($request['try_id']);

    expect(fn() => $api->request($context, json_encode($request)))
        ->toThrow(MissingDataException::class);
});

it('fails on missing request try_id', function () {
    $request = EvaluateAnswer::Request;

    unset($request['try_id']);

    expect(fn() => api()->request(context(), json_encode($request)))
        ->toThrow(fn(MissingDataException $e) => expect($e)->getField()->toEqual('EvaluateAnswerRequest::tryId'));
});

it('fails on missing response state', function () {
    $response = EvaluateAnswer::Response;

    unset($response['exercise_evaluation']['completed']);

    $api = new SowisoApi(
        configuration: configuration(),
        httpClient: mockHttpClient(
            path: EvaluateAnswer::Uri,
            response: $response,
        ),
    );

    expect(fn() => $api->request(context(), json_encode(EvaluateAnswer::Request)))
        ->toThrow(fn(MissingDataException $e) => expect($e)->getField()->toEqual('EvaluateAnswerResponse::completed'));
});

it('fails on missing response score', function () {
    $response = EvaluateAnswer::Response;

    unset($response['exercise_evaluation']['score']);

    $api = new SowisoApi(
        configuration: configuration(),
        httpClient: mockHttpClient(
            path: EvaluateAnswer::Uri,
            response: $response,
        ),
    );

    expect(fn() => $api->request(context(), json_encode(EvaluateAnswer::Request)))
        ->toThrow(fn(MissingDataException $e) => expect($e)->getField()->toEqual('EvaluateAnswerResponse::score'));
});
