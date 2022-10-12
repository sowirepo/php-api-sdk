<?php

declare(strict_types=1);

namespace Sowiso\SDK;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use JsonException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionClass;
use ReflectionException;
use Sowiso\SDK\Api\EvaluateAnswer\EvaluateAnswerEndpoint;
use Sowiso\SDK\Api\PlayExercise\PlayExerciseEndpoint;
use Sowiso\SDK\Api\PlayExerciseSet\PlayExerciseSetEndpoint;
use Sowiso\SDK\Api\PlayHint\PlayHintEndpoint;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\InvalidEndpointException;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Exceptions\NoEndpointException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\Hooks\HookInterface;

class SowisoApi
{
    /** @var array<string, class-string<EndpointInterface>> */
    private array $endpoints = [
        EvaluateAnswerEndpoint::NAME => EvaluateAnswerEndpoint::class,
        PlayExerciseEndpoint::NAME => PlayExerciseEndpoint::class,
        PlayExerciseSetEndpoint::NAME => PlayExerciseSetEndpoint::class,
        PlayHintEndpoint::NAME => PlayHintEndpoint::class,
    ];

    /** @var array<class-string<EndpointInterface>, array<CallbackInterface<RequestInterface, ResponseInterface>>> */
    private array $callbacks = [];

    public function __construct(
        private SowisoApiConfiguration $configuration,
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $httpRequestFactory = null,
        private ?StreamFactoryInterface $httpStreamFactory = null,
    ) {
    }

    /**
     * @param CallbackInterface<RequestInterface, ResponseInterface> $callback
     * @return SowisoApi
     */
    public function useCallback(CallbackInterface $callback): self
    {
        $endpoint = $callback->endpoint();

        $this->callbacks[$endpoint] ??= [];
        $this->callbacks[$endpoint][] = $callback;

        return $this;
    }

    /**
     * @param HookInterface $hook
     * @return SowisoApi
     */
    public function useHook(HookInterface $hook): self
    {
        foreach ($hook->getCallbacks() as $callback) {
            $this->useCallback($callback);
        }

        return $this;
    }

    /**
     * @throws SowisoApiException
     */
    public function request(SowisoApiContext $context, string $data): string
    {
        try {
            $json = (array) json_decode($data, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidJsonDataException($e);
        }

        $this->configuration->validate();

        $endpoint = $this->resolveEndpoint($json);

        $endpoint = $endpoint
            ->withConfiguration($this->getConfiguration())
            ->withHttpClient($this->getHttpClient())
            ->withHttpRequestFactory($this->getHttpRequestFactory())
            ->withHttpStreamFactory($this->getHttpStreamFactory())
            ->withCallbacks($this->callbacks[get_class($endpoint)] ?? []);

        $response = $endpoint->call($context, $json);

        try {
            return json_encode($response, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidJsonDataException($e);
        }
    }

    /**
     * @param array<string, mixed> $json
     * @throws SowisoApiException
     */
    private function resolveEndpoint(array $json): EndpointInterface
    {
        if (null === $name = $json[SowisoApiConfiguration::ENDPOINT_IDENTIFIER] ?? null) {
            throw new NoEndpointException();
        }

        if (null === $endpoint = $this->endpoints[$name] ?? null) {
            throw new InvalidEndpointException();
        }

        try {
            $class = new ReflectionClass($endpoint);

            if (!$class->implementsInterface(EndpointInterface::class)) {
                throw new InvalidEndpointException();
            }

            return $class->newInstance();
        } catch (ReflectionException) {
            throw new InvalidEndpointException();
        }
    }

    public function getConfiguration(): SowisoApiConfiguration
    {
        return $this->configuration;
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->httpClient ??= Psr18ClientDiscovery::find();
    }

    public function getHttpRequestFactory(): RequestFactoryInterface
    {
        return $this->httpRequestFactory ??= Psr17FactoryDiscovery::findRequestFactory();
    }

    public function getHttpStreamFactory(): StreamFactoryInterface
    {
        return $this->httpStreamFactory ??= Psr17FactoryDiscovery::findStreamFactory();
    }
}
