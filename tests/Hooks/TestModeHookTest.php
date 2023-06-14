<?php

declare(strict_types=1);

use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseSetBePlayedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseTryBeEvaluatedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\Data\ShouldExerciseTryBePlayedInTestModeData;
use Sowiso\SDK\Hooks\TestMode\TestModeHook;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;
use Sowiso\SDK\Tests\Fixtures\Payload;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;

it('runs hook correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::UriInTestMode, 'body' => PlayExerciseSet::Response],
        ['path' => PlayExerciseSet::UriWithTryIdInTestMode, 'body' => PlayExerciseSet::Response],
        ['path' => EvaluateAnswer::UriInTestMode, 'body' => EvaluateAnswer::Response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(TestModeHook::class)->makePartial();

    $context = contextWithUsername();

    $hook->expects('shouldExerciseSetBePlayedInTestMode')
        ->with(
            capture(function (ShouldExerciseSetBePlayedInTestModeData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getPayload()->getData()->toBe(Payload::Test)
                    ->getSetId()->toBe(PlayExerciseSet::Request['set_id']);
            })
        )
        ->andReturn(true);

    $hook->expects('shouldExerciseTryBePlayedInTestMode')
        ->with(
            capture(function (ShouldExerciseTryBePlayedInTestModeData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getPayload()->getData()->toBe(Payload::Test)
                    ->getTryId()->toBe(PlayExerciseSet::RequestWithTryId['try_id']);
            })
        )
        ->andReturn(true);

    $hook->expects('shouldExerciseTryBeEvaluatedInTestMode')
        ->with(
            capture(function (ShouldExerciseTryBeEvaluatedInTestModeData $data) use ($context) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getPayload()->getData()->toBe(Payload::Test)
                    ->getTryId()->toBe(EvaluateAnswer::Request['try_id']);
            })
        )
        ->andReturn(true);

    $api->useHook($hook);

    $playExerciseSetRequest = PlayExerciseSet::Request;
    $playExerciseSetRequest[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] = Payload::Test;

    $playExerciseSetWithTryIdRequest = PlayExerciseSet::RequestWithTryId;
    $playExerciseSetWithTryIdRequest[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] = Payload::Test;

    $evaluateAnswerRequest = EvaluateAnswer::Request;
    $evaluateAnswerRequest[SowisoApiConfiguration::PAYLOAD_IDENTIFIER] = Payload::Test;

    $api->request($context, json_encode($playExerciseSetRequest));
    $api->request($context, json_encode($playExerciseSetWithTryIdRequest));
    $api->request($context, json_encode($evaluateAnswerRequest));
});
