<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\ReplayExerciseTry\Http;

use Sowiso\SDK\Endpoints\Http\AbstractResponse;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

class ReplayExerciseTryResponse extends AbstractResponse
{
    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, SowisoApiPayload $payload, array $data, RequestInterface $request)
    {
        parent::__construct($context, $payload, $data, $request);
    }
}
