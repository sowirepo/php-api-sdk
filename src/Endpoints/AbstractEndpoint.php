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
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Exceptions\ResponseErrorException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\SowisoApiContext;

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
    public function call(SowisoApiContext $context, array $data): array
    {
        try {
            $request = $this->createRequest($context, $data);
            $this->runCallbacks(fn(CallbackInterface $callback) => $callback->request($context, $request));

            $uri = $this->configuration->getBaseUrl() . $request->getUri();

            $httpRequest = $this->httpRequestFactory
                ->createRequest($request->getMethod(), $uri)
                ->withHeader(SowisoApiConfiguration::API_KEY_HEADER, $this->configuration->getApiKey());

            if (null !== $body = $request->getBody()) {
                $httpRequest = $httpRequest
                    ->withHeader('Content-Type', 'application/json')
                    ->withBody($this->httpStreamFactory->createStream($body));
            }

            $httpResponse = $this->httpClient->sendRequest($httpRequest);
            $httpStatusCode = $httpResponse->getStatusCode();
            $httpBody = (string)$httpResponse->getBody();

            /** @var array<string, mixed>|bool|null $fetchedData */
            $fetchedData = json_decode(
                $httpBody === '' ? '{}' : $httpBody,
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );

            if (!is_array($fetchedData)) {
                throw new InvalidJsonDataException();
            }

            if ($httpStatusCode !== 200) {
                $httpStatusMessage = $fetchedData['error'] ?? null;
                $httpStatusMessage = is_string($httpStatusMessage) ? $httpStatusMessage : null;

                throw new ResponseErrorException($httpStatusMessage ?? 'Unknown', $httpStatusCode);
            }

            $response = $this->createResponse($context, $fetchedData, $request);
            $this->runCallbacks(fn(CallbackInterface $callback) => $callback->response($context, $response));
        } catch (SowisoApiException|JsonException|ClientExceptionInterface|Exception $e) {
            $this->runCallbacks(fn(CallbackInterface $callback) => $callback->failure($context, $e));

            if ($e instanceof SowisoApiException) {
                throw $e;
            } elseif ($e instanceof JsonException) {
                throw new InvalidJsonDataException($e);
            }

            throw new FetchingFailedException($e);
        }

        $this->runCallbacks(fn(CallbackInterface $callback) => $callback->success($context, $request, $response));

        return [];
    }

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    abstract protected function createRequest(
        SowisoApiContext $context,
        array $data,
    ): RequestInterface;

    /**
     * @param array<string, mixed> $data
     * @throws SowisoApiException
     */
    abstract protected function createResponse(
        SowisoApiContext $context,
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
        foreach ($this->callbacks as $callback) {
            $run($callback);
        }
    }
}
