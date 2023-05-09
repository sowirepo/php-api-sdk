<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet\Http;

use Sowiso\SDK\Endpoints\Http\AbstractResponse;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class PlayExerciseSetResponse extends AbstractResponse
{
    /** @var array<int, array{exerciseId: int, tryId: int}> */
    private array $exerciseTries;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, SowisoApiPayload $payload, array $data, RequestInterface $request)
    {
        parent::__construct($context, $payload, $data, $request);

        $exerciseTries = $this->parseExerciseTries();

        usort($exerciseTries, fn (array $lhs, array $rhs) => $lhs['tryId'] <=> $rhs['tryId']);

        // In readonly view, the exerciseTries array should be empty.
        // However, in non-readonly view, an exception should be thrown when the exerciseTries array isn't complete.
        $hasInvalidExerciseTries = $request instanceof PlayExerciseSetRequest
            && !$request->isReadonlyView()
            && count($exerciseTries) !== count($data);

        if ($hasInvalidExerciseTries) {
            throw MissingDataException::create(self::class, 'exerciseTries');
        }

        $this->exerciseTries = $exerciseTries;
    }

    /**
     * @return array<int, array{exerciseId: int, tryId: int}>
     */
    public function getExerciseTries(): array
    {
        return $this->exerciseTries;
    }

    /**
     * @return array<int, array{exerciseId: int, tryId: int}>
     */
    private function parseExerciseTries(): array
    {
        $exerciseTries = [];

        /** @var array<string, mixed> $item */
        foreach ($this->data as $item) {
            if (null !== $exerciseTry = $this->parseExerciseTry($item)) {
                $exerciseTries[] = $exerciseTry;
            }
        }

        return $exerciseTries;
    }

    /**
     * @param array<string, mixed> $item
     * @return array{exerciseId: int, tryId: int}|null
     */
    private function parseExerciseTry(array $item): ?array
    {
        $exerciseId = $item['exercise_id'] ?? null;
        $tryId = $item['try_id'] ?? null;

        if (null === $exerciseId || false === filter_var($exerciseId, FILTER_VALIDATE_INT)) {
            return null;
        }

        if (null === $tryId || false === filter_var($tryId, FILTER_VALIDATE_INT)) {
            return null;
        }

        return [
            'exerciseId' => intval($exerciseId),
            'tryId' => intval($tryId),
        ];
    }
}
