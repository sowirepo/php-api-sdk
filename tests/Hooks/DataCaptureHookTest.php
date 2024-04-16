<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Hooks\DataCapture\Data\OnRegisterExerciseSetData;
use Sowiso\SDK\Hooks\DataCapture\DataCaptureHook;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\Tests\Fixtures\Payload;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;

it('runs hook correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::Response],
        ['path' => PlayExerciseSet::UriAlternative, 'body' => PlayExerciseSet::ResponseAlternativeOneExercise],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(DataCaptureHook::class)->makePartial();

    $context = contextWithUsername();

    $hook->expects('onRegisterExerciseSet')
        ->with(
            capture(function (OnRegisterExerciseSetData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getSetId()->toBe(PlayExerciseSet::Request['set_id'])
                    ->getExerciseTries()->toEqual([
                        [
                            'exerciseId' => PlayExerciseSet::Response[0]['exercise_id'],
                            'tryId' => PlayExerciseSet::Response[0]['try_id'],
                        ],
                        [
                            'exerciseId' => PlayExerciseSet::Response[1]['exercise_id'],
                            'tryId' => PlayExerciseSet::Response[1]['try_id'],
                        ]
                    ]);
            })
        )
        ->once();

    $hook->expects('onRegisterExerciseSet')
        ->with(
            capture(function (OnRegisterExerciseSetData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getSetId()->toBe(PlayExerciseSet::RequestAlternative['set_id'])
                    ->getExerciseTries()->toEqual([
                        [
                            'exerciseId' => PlayExerciseSet::ResponseAlternativeOneExercise[0]['exercise_id'],
                            'tryId' => PlayExerciseSet::ResponseAlternativeOneExercise[0]['try_id'],
                        ],
                    ]);
            })
        )
        ->once();

    $api->useHook($hook);

    $api->request($context, json_encode(PlayExerciseSet::Request));
    $api->request($context, json_encode(PlayExerciseSet::RequestAlternative));
});

it('skips hook in readonly view correctly', function (string $path, mixed $request, mixed $response) {
    $client = mockHttpClient([
        ['path' => $path, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(DataCaptureHook::class)->makePartial();

    $hook->expects('onRegisterExerciseSet')->never();

    $api->useHook($hook);

    $api->request(contextWithUsername(), json_encode($request));
})->with([
    'default' => [
        PlayExerciseSet::UriReadonlyView,
        PlayExerciseSet::RequestReadonlyView,
        PlayExerciseSet::ResponseReadonlyView,
    ],
    'one exercise' => [
        PlayExerciseSet::UriReadonlyView,
        PlayExerciseSet::RequestReadonlyView,
        PlayExerciseSet::ResponseOneExerciseReadonlyView,
    ],
    'alternative exercise' => [
        PlayExerciseSet::UriReadonlyView,
        PlayExerciseSet::RequestReadonlyView,
        PlayExerciseSet::ResponseAlternativeExerciseReadonlyView,
    ],
    'readonly-restricted' => [
        PlayExerciseSet::UriReadonlyRestrictedView,
        PlayExerciseSet::RequestReadonlyRestrictedView,
        PlayExerciseSet::ResponseReadonlyRestrictedView,
    ],
]);

it('skips hook for requests with try_id correctly', function (string $path, mixed $response) {
    $client = mockHttpClient([
        ['path' => $path, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(DataCaptureHook::class)->makePartial();

    $hook->expects('onRegisterExerciseSet')->never();

    $api->useHook($hook);

    $api->request(contextWithUsername(), json_encode(PlayExerciseSet::RequestWithTryId));
})->with([
    'default with try_id' => [
        PlayExerciseSet::UriWithTryId,
        PlayExerciseSet::ResponseWithTryId,
    ],
    'one exercise with try_id' => [
        PlayExerciseSet::UriWithTryId,
        PlayExerciseSet::ResponseOneExerciseWithTryId,
    ],
    'alternative exercise with try_id' => [
        PlayExerciseSet::UriWithTryId,
        PlayExerciseSet::ResponseAlternativeExerciseWithTryId,
    ],
]);

it('runs hook before other callbacks', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
    ]);

    $api = api(httpClient: $client);

    $context = contextWithUsername();

    $callback = mock(PlayExerciseSetCallback::class)->makePartial();
    $hook = mock(DataCaptureHook::class)->makePartial();

    $hook->expects('onRegisterExerciseSet')->once()->globally()->ordered();
    $callback->expects('onSuccess')->once()->globally()->ordered();

    $api->useHook($hook);
    $api->useCallback($callback);

    $api->request($context, json_encode(PlayExerciseSet::Request));
});

it('uses higher priority for callbacks', function () {
    $hook = mock(DataCaptureHook::class);

    expect($hook)->playExerciseSetCallback()->priority()->toBe(CallbackPriority::HIGHER);
});

it('can access additional payload in hook', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(DataCaptureHook::class)->makePartial();

    $hook->expects('onRegisterExerciseSet')
        ->with(
            capture(function (OnRegisterExerciseSetData $data) {
                expect($data)
                    ->getPayload()->getData()->toBe(Payload::Test);
            })
        )
        ->once()->globally()->ordered();

    $api->useHook($hook);

    $request = PlayExerciseSet::Request;
    $request[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] = Payload::Test;

    $api->request(contextWithUsername(), json_encode($request));
});
