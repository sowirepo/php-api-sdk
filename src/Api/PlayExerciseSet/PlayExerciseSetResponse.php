<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\PlayExerciseSet;

use Sowiso\SDK\Endpoints\Http\AbstractResponse;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class PlayExerciseSetResponse extends AbstractResponse
{
    /** @var array<int, array{exerciseId: int, tryId: int}> */
    private array $exerciseTries;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, array $data, PlayExerciseSetRequest $request)
    {
        parent::__construct($context, $data, $request);

        $this->exerciseTries = array_values(
            array_filter(array_map(fn(array $item) => $this->parseExerciseTry($item), $data))
        );

        usort($this->exerciseTries, fn(array $lhs, array $rhs) => $lhs['tryId'] <=> $rhs['tryId']);

        foreach ($this->exerciseTries as $exerciseTry) {
            if ($exerciseTry === null) {
                throw MissingDataException::create(self::class, 'exerciseTries');
            }
        }

        // In readonly view, the exerciseTries array should be empty.
        // However, in non-readonly view, an exception should be thrown when the exerciseTries array isn't complete.
        if (!$request->isReadonlyView() && count($this->exerciseTries) !== count($data)) {
            throw MissingDataException::create(self::class, 'exerciseTries');
        }
    }

    /**
     * @return array<int, array{exerciseId: int, tryId: int}>
     */
    public function getExerciseTries(): array
    {
        return $this->exerciseTries;
    }

    /**
     * @param array<string, mixed> $item
     * @return array{exerciseId: int, tryId: int}|null
     */
    public function parseExerciseTry(array $item): ?array
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
            'exerciseId' => (int)$exerciseId,
            'tryId' => (int)$tryId,
        ];
    }
}
