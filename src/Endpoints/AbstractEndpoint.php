<?php

declare(strict_types=1);

namespace Sowiso\SDK\Endpoints;

use Exception;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\FetchingFailedException;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\Exceptions\ResponseErrorException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

abstract class AbstractEndpoint implements EndpointInterface
{
    protected SowisoApiConfiguration $configuration;

    protected ClientInterface $httpClient;
    protected RequestFactoryInterface $httpRequestFactory;
    protected StreamFactoryInterface $httpStreamFactory;

    /** @var array<CallbackInterface<RequestInterface, ResponseInterface>> */
    protected array $callbacks;

    public function __construct()
    {
    }

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    public function call(SowisoApiContext $context, SowisoApiPayload $payload, array $data): array
    {
        try {
            $request = $this->createRequest($context, $payload, $data);
            $this->runCallbacks(fn (CallbackInterface $callback) => $callback->request($context, $payload, $request));

            $uri = rtrim($this->configuration->getBaseUrl(), '/') . $request->getUri();

            $httpRequest = $this->httpRequestFactory
                ->createRequest($request->getMethod(), $uri)
                ->withHeader(SowisoApiConfiguration::API_KEY_HEADER, $this->configuration->getApiKey());

            if (null !== $body = $request->getBody()) {
                $httpRequest = $httpRequest
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody($this->httpStreamFactory->createStream($body));
            }

            $httpResponse = $this->httpClient->sendRequest($httpRequest);

            $responseStatusCode = $httpResponse->getStatusCode();
            $responseBody = (string) $httpResponse->getBody();

            try {
                $responseJson = $this->parseResponseJson($responseBody);
            } catch (JsonException $e) {
                throw new InvalidJsonResponseException($responseBody, $e);
            }

            if ($responseStatusCode !== 200) {
                $responseErrorMessage = $responseJson['error'] ?? null;
                $responseErrorMessage = is_string($responseErrorMessage) ? $responseErrorMessage : null;

                if ($responseErrorMessage !== null) {
                    throw new ResponseErrorException($responseErrorMessage, $responseStatusCode);
                }

                if ($responseBody !== '') {
                    throw new InvalidJsonResponseException($responseBody);
                }

                throw new InvalidJsonResponseException('Unknown Server Error');
            }

            $response = $this->createResponse($context, $payload, $responseJson, $request);
            $this->runCallbacks(fn (CallbackInterface $callback) => $callback->response($context, $payload, $response));
        } catch (SowisoApiException|ClientExceptionInterface|Exception $e) {
            // @phpstan-ignore-next-line
            $this->runCallbacks(fn (CallbackInterface $callback) => $callback->failure($context, $payload, $e));

            if ($e instanceof SowisoApiException) {
                throw $e;
            }

            throw new FetchingFailedException($e);
        }

        $this->runCallbacks(fn (CallbackInterface $callback) => $callback->success($context, $payload, $request, $response));

        return $responseJson;
    }

    /**
     * @return array<string, mixed>
     * @throws JsonException
     */
    private function parseResponseJson(string $body): array
    {
        if ($body === '') {
            return [];
        }

        /** @var array<string, mixed>|bool|null $json */
        $json = json_decode(
            $body,
            associative: true,
            flags: JSON_THROW_ON_ERROR,
        );

        if (!is_array($json)) {
            return [];
        }

        return $json;
    }

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    abstract protected function createRequest(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
    ): RequestInterface;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    abstract protected function createResponse(
        SowisoApiContext $context,
        SowisoApiPayload $payload,
        array $data,
        RequestInterface $request,
    ): ResponseInterface;

    public function withConfiguration(SowisoApiConfiguration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function withHttpClient(ClientInterface $httpClient): self
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    public function withHttpRequestFactory(RequestFactoryInterface $httpRequestFactory): self
    {
        $this->httpRequestFactory = $httpRequestFactory;

        return $this;
    }

    public function withHttpStreamFactory(StreamFactoryInterface $httpStreamFactory): self
    {
        $this->httpStreamFactory = $httpStreamFactory;

        return $this;
    }

    /**
     * @param array<CallbackInterface<RequestInterface, ResponseInterface>> $callbacks
     */
    public function withCallbacks(array $callbacks): self
    {
        $this->callbacks = $callbacks;

        return $this;
    }

    /**
     * @param callable(CallbackInterface<RequestInterface, ResponseInterface>): void $run
     */
    private function runCallbacks(callable $run): void
    {
        $callbacks = $this->callbacks;

        usort($callbacks, fn (CallbackInterface $lhs, CallbackInterface $rhs) => $rhs->priority() <=> $lhs->priority());

        foreach ($callbacks as $callback) {
            $run($callback);
        }
    }
}
