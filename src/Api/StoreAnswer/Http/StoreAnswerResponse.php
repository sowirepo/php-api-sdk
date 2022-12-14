<?php

declare(strict_types=1);

namespace Sowiso\SDK\Api\StoreAnswer\Http;

use Sowiso\SDK\Endpoints\Http\AbstractResponse;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiContext;

class StoreAnswerResponse extends AbstractResponse
{
    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function __construct(SowisoApiContext $context, array $data, RequestInterface $request)
    {
        parent::__construct($context, $data, $request);
    }
}
