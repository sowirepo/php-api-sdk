<?php

declare(strict_types=1);

use Mockery\Mock;
use Mockery\MockInterface;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerEndpoint;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseCallback;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseEndpoint;
use Sowiso\SDK\Api\PlayHint\PlayHintCallback;
use Sowiso\SDK\Api\PlayHint\PlayHintEndpoint;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionCallback;
use Sowiso\SDK\Api\PlaySolution\PlaySolutionEndpoint;
use Sowiso\SDK\Api\StoreAnswer\StoreAnswerCallback;
use Sowiso\SDK\Api\StoreAnswer\StoreAnswerEndpoint;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Data\OnFailureDataInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\InvalidTryIdException;
use Sowiso\SDK\Hooks\TryIdVerification\Data\IsValidTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\Data\OnRegisterTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\TryIdVerificationHook;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;
use Sowiso\SDK\Tests\Fixtures\Payload;
use Sowiso\SDK\Tests\Fixtures\PlayExercise;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;
use Sowiso\SDK\Tests\Fixtures\PlayHint;
use Sowiso\SDK\Tests\Fixtures\PlaySolution;
use Sowiso\SDK\Tests\Fixtures\StoreAnswer;
use Sowiso\SDK\Tests\Hooks\BasicTryIdVerificationHook;

it('runs hook correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
        ['path' => PlayExercise::Uri, 'body' => PlayExercise::Response],
        ['path' => PlayHint::Uri, 'body' => PlayHint::Response],
        ['path' => PlaySolution::Uri, 'body' => PlaySolution::Response],
        ['path' => StoreAnswer::Uri, 'body' => StoreAnswer::Response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(TryIdVerificationHook::class)->makePartial();

    $context = contextWithUsername();

    $tryId = (int) PlayExerciseSet::ResponseOneExercise[0]['try_id'];

    $hook->expects('onRegisterTryId')
        ->with(
            capture(function (OnRegisterTryIdData $data) use ($context, $tryId) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getTryId()->toBe($tryId);
            })
        )
        ->once();

    $hook->expects('isValidTryId')
        ->with(
            capture(function (IsValidTryIdData $data) use ($context, $tryId) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getTryId()->toBe($tryId);
            })
        )
        ->times(5)
        ->andReturnTrue();

    $hook->expects('onCatchInvalidTryId')->never();

    $api->useHook($hook);

    $api->request($context, json_encode(PlayExerciseSet::Request));
    $api->request($context, json_encode(EvaluateAnswer::Request));
    $api->request($context, json_encode(PlayExercise::Request));
    $api->request($context, json_encode(PlayHint::Request));
    $api->request($context, json_encode(PlaySolution::Request));
    $api->request($context, json_encode(StoreAnswer::Request));
});

it('runs hook for play/set with try_id correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
        ['path' => PlayExerciseSet::UriWithTryId, 'body' => PlayExerciseSet::ResponseOneExerciseWithTryId],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(TryIdVerificationHook::class)->makePartial();

    $context = contextWithUsername();

    $tryId = (int) PlayExerciseSet::ResponseOneExercise[0]['try_id'];

    $hook->expects('onRegisterTryId')
        ->with(
            capture(function (OnRegisterTryIdData $data) use ($context, $tryId) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getTryId()->toBe($tryId);
            })
        )
        ->once()->globally()->ordered();

    $hook->expects('isValidTryId')
        ->with(
            capture(function (IsValidTryIdData $data) use ($context, $tryId) {
                expect($data)
                    ->getContext()->toBe($context)
                    ->getTryId()->toBe($tryId);
            })
        )
        ->once()->globally()->ordered()
        ->andReturnTrue();

    $hook->expects('onCatchInvalidTryId')->never();

    $api->useHook($hook);

    $api->request($context, json_encode(PlayExerciseSet::Request));
    $api->request($context, json_encode(PlayExerciseSet::RequestWithTryId));
});

it('skips hook in readonly view correctly', function (string $path, mixed $response) {
    $client = mockHttpClient([
        ['path' => $path, 'body' => $response],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(TryIdVerificationHook::class)->makePartial();

    $hook->expects('onRegisterTryId')->never();
    $hook->expects('isValidTryId')->never();
    $hook->expects('onCatchInvalidTryId')->never();

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

it('verifies one try_id correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
    ]);

    $api = api(httpClient: $client);

    $context = contextWithUsername();

    $hook = new BasicTryIdVerificationHook();
    $api->useHook($hook);

    $api->request($context, json_encode(PlayExerciseSet::Request));

    expect($hook)
        ->getUsers()->toHaveCount(1)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserValidatedFor(12345)->toBe(false);

    $api->request($context, json_encode(EvaluateAnswer::Request));

    expect($hook)
        ->getUsers()->toHaveCount(1)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserValidatedFor(12345)->toBe(true);
});

it('verifies multiple try_id\'s correctly', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
        ['path' => PlayExerciseSet::UriAlternativeUser, 'body' => PlayExerciseSet::ResponseAlternativeOneExercise],
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
        ['path' => EvaluateAnswer::UriAlternative, 'body' => EvaluateAnswer::Response],
    ]);

    $api = api(httpClient: $client);

    $hook = new BasicTryIdVerificationHook();
    $api->useHook($hook);

    $api->request(context(user: 'user1'), json_encode(PlayExerciseSet::Request));

    expect($hook)
        ->getUsers()->toHaveCount(1)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserValidatedFor(12345)->toBe(false);

    $api->request(context(user: 'user2'), json_encode(PlayExerciseSet::RequestAlternative));

    expect($hook)
        ->getUsers()->toHaveCount(2)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserNameFor(12346)->toBe('user2')
        ->getUserValidatedFor(12345)->toBe(false)
        ->getUserValidatedFor(12346)->toBe(false);

    $api->request(context(user: 'user1'), json_encode(EvaluateAnswer::Request));

    expect($hook)
        ->getUsers()->toHaveCount(2)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserNameFor(12346)->toBe('user2')
        ->getUserValidatedFor(12345)->toBe(true)
        ->getUserValidatedFor(12346)->toBe(false);

    $api->request(context(user: 'user2'), json_encode(EvaluateAnswer::RequestAlternative));

    expect($hook)
        ->getUsers()->toHaveCount(2)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserNameFor(12346)->toBe('user2')
        ->getUserValidatedFor(12345)->toBe(true)
        ->getUserValidatedFor(12346)->toBe(true);
});

it('aborts verification of wrong try_id correctly', function (string $class, array $request) {
    $api = api();

    $context = contextWithUsername();

    /** @var Mock|MockInterface&CallbackInterface<RequestInterface, ResponseInterface> $callback */
    $callback = mock($class)
        ->makePartial();

    $callback->expects('onSuccess')->never();

    $callback->expects('onFailure')->with(
        capture(function (OnFailureDataInterface $data) use ($context, $request) {
            expect($data)
                ->getContext()->toBe($context)
                ->getException()->toBeInstanceOf(InvalidTryIdException::class);

            /** @var InvalidTryIdException $exception */
            $exception = $data->getException();

            expect($exception)
                ->getTryId()->toBe($request['try_id']);
        })
    )->once();

    $api->useHook(new BasicTryIdVerificationHook());
    $api->useCallback($callback);

    expect(fn () => $api->request($context, json_encode($request)))
        ->toThrow(InvalidTryIdException::class, sprintf("InvalidTryId '%d'", $request['try_id']));
})->with([
    EvaluateAnswerEndpoint::NAME => [
        EvaluateAnswerCallback::class,
        EvaluateAnswer::Request,
    ],
    PlayExerciseEndpoint::NAME => [
        PlayExerciseCallback::class,
        PlayExercise::Request,
    ],
    PlayHintEndpoint::NAME => [
        PlayHintCallback::class,
        PlayHint::Request,
    ],
    PlaySolutionEndpoint::NAME => [
        PlaySolutionCallback::class,
        PlaySolution::Request,
    ],
    StoreAnswerEndpoint::NAME => [
        StoreAnswerCallback::class,
        StoreAnswer::Request,
    ],
]);

it('uses high priority for callbacks', function () {
    $hook = mock(TryIdVerificationHook::class);

    expect($hook)->playExerciseSetCallback()->priority()->toBe(CallbackPriority::HIGH);
});

it('can access additional payload in hook', function () {
    $client = mockHttpClient([
        ['path' => PlayExerciseSet::Uri, 'body' => PlayExerciseSet::ResponseOneExercise],
    ]);

    $api = api(httpClient: $client);

    $hook = mock(TryIdVerificationHook::class)->makePartial();

    $hook->expects('onRegisterTryId')
        ->with(
            capture(function (OnRegisterTryIdData $data) {
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
