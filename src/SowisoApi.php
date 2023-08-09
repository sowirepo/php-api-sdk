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
use Sowiso\SDK\Api\PlaySolution\PlaySolutionEndpoint;
use Sowiso\SDK\Api\ReplayExerciseTry\ReplayExerciseTryEndpoint;
use Sowiso\SDK\Api\StoreAnswer\StoreAnswerEndpoint;
use Sowiso\SDK\Callbacks\CallbackInterface;
use Sowiso\SDK\Endpoints\EndpointInterface;
use Sowiso\SDK\Endpoints\Http\RequestInterface;
use Sowiso\SDK\Endpoints\Http\ResponseInterface;
use Sowiso\SDK\Exceptions\DataVerificationFailedException;
use Sowiso\SDK\Exceptions\InvalidDataException;
use Sowiso\SDK\Exceptions\InvalidEndpointException;
use Sowiso\SDK\Exceptions\InvalidJsonDataException;
use Sowiso\SDK\Exceptions\InvalidJsonRequestException;
use Sowiso\SDK\Exceptions\InvalidJsonResponseException;
use Sowiso\SDK\Exceptions\InvalidTryIdException;
use Sowiso\SDK\Exceptions\MissingDataException;
use Sowiso\SDK\Exceptions\NoApiKeyException;
use Sowiso\SDK\Exceptions\NoBaseUrlException;
use Sowiso\SDK\Exceptions\NoEndpointException;
use Sowiso\SDK\Exceptions\NoUserException;
use Sowiso\SDK\Exceptions\ResponseErrorException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\Hooks\HookInterface;

class SowisoApi
{
    /**
     * Holds all registered endpoints.
     * It's used to resolve the {@link SowisoApiConfiguration::ENDPOINT_IDENTIFIER} identifier.
     *
     * @var array<string, class-string<EndpointInterface>>
     */
    private array $endpoints = [
        EvaluateAnswerEndpoint::NAME => EvaluateAnswerEndpoint::class,
        PlayExerciseEndpoint::NAME => PlayExerciseEndpoint::class,
        PlayExerciseSetEndpoint::NAME => PlayExerciseSetEndpoint::class,
        ReplayExerciseTryEndpoint::NAME => ReplayExerciseTryEndpoint::class,
        PlayHintEndpoint::NAME => PlayHintEndpoint::class,
        PlaySolutionEndpoint::NAME => PlaySolutionEndpoint::class,
        StoreAnswerEndpoint::NAME => StoreAnswerEndpoint::class,
    ];

    /**
     * Holds all registered callbacks.
     * It's used to find the registered callback linked to the {@link SowisoApiConfiguration::ENDPOINT_IDENTIFIER} identifier.
     *
     * Registering a callback is done by the SDK's user.
     *
     * @var array<class-string<EndpointInterface>, array<CallbackInterface<RequestInterface, ResponseInterface>>>
     */
    private array $callbacks = [];

    public function __construct(
        private SowisoApiConfiguration $configuration,
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $httpRequestFactory = null,
        private ?StreamFactoryInterface $httpStreamFactory = null,
    ) {
    }

    /**
     * Registers any callback that implements {@link CallbackInterface}.
     *
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
     * Registers any hook that implements {@link HookInterface}.
     *
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
     * Takes JSON data as input and forwards it to the API. The API's JSON response is then returned.
     *
     * The API endpoint to use is determined by the {@link SowisoApiConfiguration::ENDPOINT_IDENTIFIER} field in the JSON data.
     * When the data has all the required fields for the specific endpoint, the corresponding callback methods are called.
     *
     * @param SowisoApiContext $context a data object that is passed to the callbacks and hooks
     * @param string $data the JSON data to handle
     * @return string the JSON data returned by the API
     * @throws NoBaseUrlException when no API base url is set
     * @throws NoApiKeyException when no API key is set
     * @throws NoUserException when no API user is set in the context (and the requested endpoint requires it)
     * @throws NoEndpointException when the API request is missing the endpoint to fetch
     * @throws InvalidEndpointException when the API request has specified an invalid endpoint to fetch
     * @throws InvalidJsonDataException when the SDK cannot handle JSON data
     * @throws InvalidJsonRequestException when the API request has invalid JSON data
     * @throws InvalidJsonResponseException when the API response has invalid JSON data
     * @throws MissingDataException when the API request or response is missing required data
     * @throws InvalidDataException when the API request or response contains invalid data
     * @throws ResponseErrorException when the API response has an error
     * @throws InvalidTryIdException when an invalid SOWISO try id is caught - thrown by {@link TryIdVerificationHook}
     * @throws DataVerificationFailedException when verifying data failed in the {@link DataVerificationHook}
     * @throws SowisoApiException when any other error occurs
     */
    public function request(SowisoApiContext $context, string $data): string
    {
        try {
            $json = (array) json_decode($data, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidJsonRequestException($e);
        }

        $this->configuration->validate();

        $endpoint = $this->resolveEndpoint($json);

        $endpoint = $endpoint
            ->withConfiguration($this->getConfiguration())
            ->withHttpClient($this->getHttpClient())
            ->withHttpRequestFactory($this->getHttpRequestFactory())
            ->withHttpStreamFactory($this->getHttpStreamFactory())
            ->withCallbacks($this->callbacks[get_class($endpoint)] ?? []);

        $payload = SowisoApiPayload::createFromRequest($json);

        $response = $endpoint->call($context, $payload, $json);

        try {
            return json_encode($response, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidJsonDataException($e);
        }
    }

    /**
     * Helper method to resolve a registered from the request JSON data.
     *
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
