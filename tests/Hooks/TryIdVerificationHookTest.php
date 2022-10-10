<?php

declare(strict_types=1);

use Sowiso\SDK\Hooks\TryIdVerificationHook;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;

it('runs hook correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
    ]);

    $tryId = (int) PlayExerciseSet::ResponseOneExercise[0]['try_id'];

    $api = api(httpClient: $client);

    $hook = mock(TryIdVerificationHook::class)->makePartial();

    $context = contextWithUsername();

    $hook->expects('onRegisterTryId')
        ->with($context, $tryId)
        ->once()
        ->andReturnSelf();

    $hook->expects('isValidTryId')
        ->with($context, $tryId)
        ->times(1)
        ->andReturnTrue();

    $api->useHook($hook);

    $api->request($context, json_encode(PlayExerciseSet::Request));
    $api->request($context, json_encode(EvaluateAnswer::Request));
});

it('skips hook in readonly view correctly', function (string $path, mixed $response) {
    $client = mockHttpClient([
        ['path' => $path, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(TryIdVerificationHook::class)->makePartial();

    $hook->expects('onRegisterTryId')->never();

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

it('verifies one try_id correctly'); // TODO

it('verifies multiple try_id\'s correctly'); // TODO

it('aborts verification of wrong try_id correctly'); // TODO

it('uses high priority fall callbacks'); // TODO
