<?php

declare(strict_types=1);

use Mockery\Mock;
use Mockery\MockInterface;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerCallback;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerEndpoint;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Callbacks\CallbackPriority;
use Sowiso\SDK\Data\OnFailureDataInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\InvalidTryIdException;
use Sowiso\SDK\Hooks\TryIdVerificationHook;
use Sowiso\SDK\Tests\Fixtures\EvaluateAnswer;
use Sowiso\SDK\Tests\Fixtures\PlayExerciseSet;
use Sowiso\SDK\Tests\Hooks\BasicTryIdVerificationHook;

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

    $hook->expects('onCatchInvalidTryId')->never();

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
        ['path' => PlayExerciseSet::UriAlternative, 'body' => PlayExerciseSet::ResponseAlternativeOneExercise],
        ['path' => EvaluateAnswer::Uri, 'body' => EvaluateAnswer::Response],
        ['path' => EvaluateAnswer::UriAlternative, 'body' => EvaluateAnswer::Response],
    ]);

    $api = api(httpClient: $client);

    $hook = new BasicTryIdVerificationHook();
    $api->useHook($hook);

    $api->request(context(username: 'user1'), json_encode(PlayExerciseSet::Request));

    expect($hook)
        ->getUsers()->toHaveCount(1)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserValidatedFor(12345)->toBe(false);

    $api->request(context(username: 'user2'), json_encode(PlayExerciseSet::RequestAlternative));

    expect($hook)
        ->getUsers()->toHaveCount(2)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserNameFor(12346)->toBe('user2')
        ->getUserValidatedFor(12345)->toBe(false)
        ->getUserValidatedFor(12346)->toBe(false);

    $api->request(context(username: 'user1'), json_encode(EvaluateAnswer::Request));

    expect($hook)
        ->getUsers()->toHaveCount(2)
        ->getUserNameFor(12345)->toBe('user1')
        ->getUserNameFor(12346)->toBe('user2')
        ->getUserValidatedFor(12345)->toBe(true)
        ->getUserValidatedFor(12346)->toBe(false);

    $api->request(context(username: 'user2'), json_encode(EvaluateAnswer::RequestAlternative));

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

    expect(fn() => $api->request($context, json_encode($request)))
        ->toThrow(InvalidTryIdException::class, sprintf("InvalidTryId '%d'", $request['try_id']));
})->with([
    EvaluateAnswerEndpoint::NAME => [
        EvaluateAnswerCallback::class,
        EvaluateAnswer::Request,
    ],
]);

it('uses high priority fall callbacks', function () {
    $hook = mock(TryIdVerificationHook::class);

    expect($hook)->playExerciseSetCallback()->priority()->toBe(CallbackPriority::HIGH);
});
