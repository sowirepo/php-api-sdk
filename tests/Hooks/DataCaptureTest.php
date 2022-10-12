<?php

declare(strict_types=1);

use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetCallback;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Hooks\DataCapture\Data\OnRegisterExerciseTryData;
use Sowiso\SDK\Hooks\DataCapture\DataCaptureHook;
use Sowiso\SDK\Hooks\TryIdVerification\Data\OnRegisterTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\TryIdVerificationHook;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;

it('runs hook correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::Response],
        ['path' => PlayExerciseSet::UriAlternative, 'body' => PlayExerciseSet::ResponseAlternativeOneExercise],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(DataCaptureHook::class)->makePartial();

    $context = contextWithUsername();

    $index = 0;

    $hook->expects('onRegisterExerciseTry')
        ->with(
            capture(function (OnRegisterExerciseTryData $data) use (&$index, $context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getSetId()->toBe(PlayExerciseSet::Request['set_id'])
                    ->getExerciseId()->toEqual(PlayExerciseSet::Response[$index]['exercise_id'])
                    ->getTryId()->toEqual(PlayExerciseSet::Response[$index]['try_id']);

                $index++;
            })
        )
        ->times(2);

    $hook->expects('onRegisterExerciseTry')
        ->with(
            capture(function (OnRegisterExerciseTryData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getSetId()->toBe(PlayExerciseSet::RequestAlternative['set_id'])
                    ->getExerciseId()->toEqual(PlayExerciseSet::ResponseAlternativeOneExercise[0]['exercise_id'])
                    ->getTryId()->toEqual(PlayExerciseSet::ResponseAlternativeOneExercise[0]['try_id']);
            })
        )
        ->once();

    $api->useHook($hook);

    $api->request($context, json_encode(PlayExerciseSet::Request));
    $api->request($context, json_encode(PlayExerciseSet::RequestAlternative));
});

it('skips hook in readonly view correctly', function (string $path, mixed $response) {
    $client = mockHttpClient([
        ['path' => $path, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(DataCaptureHook::class)->makePartial();

    $hook->expects('onRegisterExerciseTry')->never();

    $api->useHook($hook);

    $api->request(contextWithUsername(), json_encode(PlayExerciseSet::RequestReadonlyView));
})->with([
    'default' => [
        PlayExerciseSet::UriReadonlyView,
        PlayExerciseSet::ResponseReadonlyView,
    ],
    'one exercise' => [
        PlayExerciseSet::UriReadonlyView,
        PlayExerciseSet::ResponseOneExerciseReadonlyView,
    ],
    'alternative exercise' => [
        PlayExerciseSet::UriReadonlyView,
        PlayExerciseSet::ResponseAlternativeExerciseReadonlyView
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

    $hook->expects('onRegisterExerciseTry')->once()->globally()->ordered();
    $callback->expects('onSuccess')->once()->globally()->ordered();

    $api->useHook($hook);
    $api->useCallback($callback);

    $api->request($context, json_encode(PlayExerciseSet::Request));
});

it('runs hook before other hooks', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
    ]);

    $api = api(httpClient: $client);

    $context = contextWithUsername();

    $dataCaptureHook = mock(DataCaptureHook::class)->makePartial();
    $tryIdVerificationHook = mock(TryIdVerificationHook::class)->makePartial();

    $dataCaptureHook->expects('onRegisterExerciseTry')
        ->with(
            capture(function (OnRegisterExerciseTryData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getSetId()->toBe(PlayExerciseSet::Request['set_id'])
                    ->getExerciseId()->toEqual(PlayExerciseSet::ResponseOneExercise[0]['exercise_id'])
                    ->getTryId()->toEqual(PlayExerciseSet::ResponseOneExercise[0]['try_id']);
            })
        )
        ->once()->globally()->ordered();

    $tryIdVerificationHook->expects('onRegisterTryId')
        ->with(
            capture(function (OnRegisterTryIdData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getTryId()->toEqual(PlayExerciseSet::ResponseOneExercise[0]['try_id']);
            })
        )
        ->once()->globally()->ordered();

    $api->useHook($tryIdVerificationHook);
    $api->useHook($dataCaptureHook);

    $api->request($context, json_encode(PlayExerciseSet::Request));
});

it('uses higher priority for callbacks', function () {
    $hook = mock(DataCaptureHook::class);

    expect($hook)->playExerciseSetCallback()->priority()->toBe(CallbackPriority::HIGHER);
});
