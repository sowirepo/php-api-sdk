<?php

declare(strict_types=1);

use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnRequestData;
use Sowiso\SDK\Api\PlayExercise\Data\PlayExerciseOnRequestData;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnRequestData;
use Sowiso\SDK\Api\PlayHint\Data\PlayHintOnRequestData;
use Sowiso\SDK\Api\PlaySolution\Data\PlaySolutionOnRequestData;
use Sowiso\SDK\Api\ReplayExerciseTry\Data\ReplayExerciseTryOnRequestData;
use Sowiso\SDK\Api\StoreAnswer\Data\StoreAnswerOnRequestData;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Exceptions\DataVerificationFailedException;
use Sowiso\SDK\Hooks\DataVerification\DataVerificationHook;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;
use Sowiso\SDK\Tests\Fixtures\PlayExercise;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;
use Sowiso\SDK\Tests\Fixtures\PlayHint;
use Sowiso\SDK\Tests\Fixtures\PlaySolution;
use Sowiso\SDK\Tests\Fixtures\ReplayExerciseTry;
use Sowiso\SDK\Tests\Fixtures\StoreAnswer;

it('runs hook correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::Response],
        ['path' => PlayExerciseSet::UriWithTryId, 'body' => PlayExerciseSet::ResponseWithTryId],
        ['path' => PlayExercise::Uri, 'body' => PlayExercise::Response],
        ['path' => ReplayExerciseTry::Uri, 'body' => ReplayExerciseTry::Response],
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
        ['path' => PlayHint::Uri, 'body' => PlayHint::Response],
        ['path' => PlaySolution::Uri, 'body' => PlaySolution::Response],
        ['path' => StoreAnswer::Uri, 'body' => StoreAnswer::Response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(DataVerificationHook::class)->makePartial();

    $context = contextWithUsername();

    $hook->expects('verifyPlayExerciseSetRequest')
        ->with(
            capture(function (PlayExerciseSetOnRequestData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getRequest()->usesTryId()->toBe(false)
                    ->getRequest()->getSetId()->toBe(PlayExerciseSet::Request['set_id'])
                    ->getRequest()->getView()->toBe('student');
            })
        )
        ->once();

    $hook->expects('verifyPlayExerciseSetRequest')
        ->with(
            capture(function (PlayExerciseSetOnRequestData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getRequest()->usesTryId()->toBe(true)
                    ->getRequest()->getTryId()->toBe(PlayExerciseSet::RequestWithTryId['try_id'])
                    ->getRequest()->getView()->toBe('student');
            })
        )
        ->once();

    $hook->expects('verifyPlayExerciseRequest')
        ->with(
            capture(function (PlayExerciseOnRequestData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getRequest()->getTryId()->toBe(PlayExercise::Request['try_id'])
                    ->getRequest()->getView()->toBe('student');
            })
        )
        ->once();

    $hook->expects('verifyReplayExerciseTryRequest')
        ->with(
            capture(function (ReplayExerciseTryOnRequestData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getRequest()->getTryId()->toBe(ReplayExerciseTry::Request['try_id']);
            })
        )
        ->once();

    $hook->expects('verifyEvaluateAnswerRequest')
        ->with(
            capture(function (EvaluateAnswerOnRequestData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getRequest()->getTryId()->toBe(EvaluateAnswer::Request['try_id']);
            })
        )
        ->once();

    $hook->expects('verifyPlayHintRequest')
        ->with(
            capture(function (PlayHintOnRequestData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getRequest()->getTryId()->toBe(PlayHint::Request['try_id']);
            })
        )
        ->once();

    $hook->expects('verifyPlaySolutionRequest')
        ->with(
            capture(function (PlaySolutionOnRequestData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getRequest()->getTryId()->toBe(PlaySolution::Request['try_id']);
            })
        )
        ->once();

    $hook->expects('verifyStoreAnswerRequest')
        ->with(
            capture(function (StoreAnswerOnRequestData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getRequest()->getTryId()->toBe(StoreAnswer::Request['try_id']);
            })
        )
        ->once();

    $api->useHook($hook);

    $api->request($context, json_encode(PlayExerciseSet::Request));
    $api->request($context, json_encode(PlayExerciseSet::RequestWithTryId));
    $api->request($context, json_encode(PlayExercise::Request));
    $api->request($context, json_encode(ReplayExerciseTry::Request));
    $api->request($context, json_encode(EvaluateAnswer::Request));
    $api->request($context, json_encode(PlayHint::Request));
    $api->request($context, json_encode(PlaySolution::Request));
    $api->request($context, json_encode(StoreAnswer::Request));
});

it('aborts correctly when verification failed', function () {
    $api = api();

    $hook = mock(DataVerificationHook::class)->makePartial();

    $context = contextWithUsername();

    $hook->shouldReceive('verifyPlayExerciseSetRequest')->andThrow(new DataVerificationFailedException('test'));
    $hook->shouldReceive('verifyPlayExerciseSetRequest')->andThrow(new DataVerificationFailedException('test'));
    $hook->shouldReceive('verifyPlayExerciseRequest')->andThrow(new DataVerificationFailedException('test'));
    $hook->shouldReceive('verifyReplayExerciseTryRequest')->andThrow(new DataVerificationFailedException('test'));
    $hook->shouldReceive('verifyEvaluateAnswerRequest')->andThrow(new DataVerificationFailedException('test'));
    $hook->shouldReceive('verifyPlayHintRequest')->andThrow(new DataVerificationFailedException('test'));
    $hook->shouldReceive('verifyPlaySolutionRequest')->andThrow(new DataVerificationFailedException('test'));
    $hook->shouldReceive('verifyStoreAnswerRequest')->andThrow(new DataVerificationFailedException('test'));

    $api->useHook($hook);

    expect(fn () => $api->request($context, json_encode(PlayExerciseSet::Request)))
        ->toThrow(DataVerificationFailedException::class, 'DataVerificationFailed ("test")');

    expect(fn () => $api->request($context, json_encode(PlayExerciseSet::RequestWithTryId)))
        ->toThrow(DataVerificationFailedException::class, 'DataVerificationFailed ("test")');

    expect(fn () => $api->request($context, json_encode(PlayExercise::Request)))
        ->toThrow(DataVerificationFailedException::class, 'DataVerificationFailed ("test")');

    expect(fn () => $api->request($context, json_encode(ReplayExerciseTry::Request)))
        ->toThrow(DataVerificationFailedException::class, 'DataVerificationFailed ("test")');

    expect(fn () => $api->request($context, json_encode(EvaluateAnswer::Request)))
        ->toThrow(DataVerificationFailedException::class, 'DataVerificationFailed ("test")');

    expect(fn () => $api->request($context, json_encode(PlayHint::Request)))
        ->toThrow(DataVerificationFailedException::class, 'DataVerificationFailed ("test")');

    expect(fn () => $api->request($context, json_encode(PlaySolution::Request)))
        ->toThrow(DataVerificationFailedException::class, 'DataVerificationFailed ("test")');

    expect(fn () => $api->request($context, json_encode(StoreAnswer::Request)))
        ->toThrow(DataVerificationFailedException::class, 'DataVerificationFailed ("test")');
});

it('uses high priority for callbacks', function () {
    $hook = mock(DataVerificationHook::class);

    expect($hook)
        ->playExerciseSetCallback()->priority()->toBe(CallbackPriority::HIGH)
        ->playExerciseCallback()->priority()->toBe(CallbackPriority::HIGH)
        ->replayExerciseTryCallback()->priority()->toBe(CallbackPriority::HIGH)
        ->evaluateAnswerCallback()->priority()->toBe(CallbackPriority::HIGH)
        ->playHintCallback()->priority()->toBe(CallbackPriority::HIGH)
        ->playSolutionCallback()->priority()->toBe(CallbackPriority::HIGH)
        ->storeAnswerCallback()->priority()->toBe(CallbackPriority::HIGH);
});
