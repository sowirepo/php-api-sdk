<?php

declare(strict_types=1);

use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Hooks\ScoreCapture\Data\OnScoreData;
use Sowiso\SDK\Hooks\ScoreCapture\Data\OnScoreSource;
use Sowiso\SDK\Hooks\ScoreCapture\ScoreCaptureHook;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;
use Sowiso\SDK\Tests\Fixtures\Payload;
use Sowiso\SDK\Tests\Fixtures\PlayHint;
use Sowiso\SDK\Tests\Fixtures\PlaySolution;

it('runs hook correctly', function () {
    $client = mockHttpClient([
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
        ['path' => PlayHint::Uri, 'body' => PlayHint::Response], // This will simply be ignored
        ['path' => PlaySolution::Uri, 'body' => PlaySolution::Response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(ScoreCaptureHook::class)->makePartial();

    $context = contextWithUsername();

    $index = 0;

    $hook->expects('onScore')
        ->with(
            capture(function (OnScoreData $data) use (&$index, $context) {
                $source = [
                    OnScoreSource::EVALUATE_ANSWER,
                    OnScoreSource::PLAY_SOLUTION,
                ][$index];

                $tryId = [
                    EvaluateAnswer::Request['try_id'],
                    PlaySolution::Request['try_id'],
                ][$index];

                $score = [
                    EvaluateAnswer::Response['exercise_evaluation']['score'],
                    PlaySolution::Response['score'],
                ][$index];

                expect($data)
                    ->getContext()->toBe($context)
                    ->getSource()->toBe($source)
                    ->getTryId()->toBe($tryId)
                    ->getScore()->toBe($score)
                    ->and($data->isCompleted())->toBe(true);

                $index++;
            })
        )
        ->times(2);

    $api->useHook($hook);

    $api->request($context, json_encode(EvaluateAnswer::Request));
    $api->request($context, json_encode(PlayHint::Request)); // This will simply be ignored
    $api->request($context, json_encode(PlaySolution::Request));
});

it('runs hook before other callbacks', function () {
    $client = mockHttpClient([
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
    ]);

    $api = api(httpClient: $client);

    $context = contextWithUsername();

    $callback = mock(EvaluateAnswerCallback::class)->makePartial();
    $hook = mock(ScoreCaptureHook::class)->makePartial();

    $hook->expects('onScore')->once()->globally()->ordered();
    $callback->expects('onSuccess')->once()->globally()->ordered();

    $api->useHook($hook);
    $api->useCallback($callback);

    $api->request($context, json_encode(EvaluateAnswer::Request));
});

it('uses high priority for callbacks', function () {
    $hook = mock(ScoreCaptureHook::class);

    expect($hook)
        ->evaluateAnswerCallback()->priority()->toBe(CallbackPriority::HIGH)
        ->playSolutionCallback()->priority()->toBe(CallbackPriority::HIGH);
});



it('can access additional payload in hook', function () {
    $client = mockHttpClient([
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(ScoreCaptureHook::class)->makePartial();

    $hook->expects('onScore')
        ->with(
            capture(function (OnScoreData $data) {
                expect($data)
                    ->getPayload()->getData()->toBe(Payload::Test);
            })
        )
        ->once()->globally()->ordered();

    $api->useHook($hook);

    $request = EvaluateAnswer::Request;
    $request[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] = Payload::Test;

    $api->request(contextWithUsername(), json_encode($request));
});
