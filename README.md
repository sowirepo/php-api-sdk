# SOWISO API SDK for PHP

This PHP library contains the SDK to access the SOWISO API. It's not supposed to be used publicly.

## Installation

```bash
composer require sowiso/php-api-sdk
```

## Usage

The SDK is composed of some features that are explained in this section. The general usage can be seen in this (basic
and not-working) example:

```php
$configuration = SowisoApiConfiguration::create();
$api = new SowisoApi($configuration);

$context = SowisoApiContext::create();
$response = $api->request($context, '{}');
```

The SDK must be instantiated with a configuration, and can then be used to request the API.

#### Configuration

```php
$configuration = SowisoApiConfiguration::create(
    baseUrl: 'SOWISO_API_BASE_URL',
    apiKey: 'SOWISO_API_KEY',
);

$api = new SowisoApi($configuration);
```

The SOWISO API requires these configuration settings:

- **API Base URL**: The url of the SOWISO server to use. Example: _"https://cloud.sowiso.nl"_ (no trailing forward-slash)
- **API Key**: The API key that's used to authenticate all requests. Only issued by SOWISO.

#### PSR-17 and PSR-18

The SDK allows to specify to use your own [HTTP Client (PSR-18)](http://www.php-fig.org/psr/psr-18) and your
own [HTTP Message Factories (PSR-17)](http://www.php-fig.org/psr/psr-17).
If not provided, the SDK used [this](https://github.com/php-http/discovery) package to automatically discover applicable packages in your project.

```php
$api = new SowisoApi(
    configuration: SowisoApiConfiguration::create(), // The configuration is needed here
    httpClient: ...,
    httpRequestFactory: ...,
    httpStreamFactory: ...,
);
```

#### Context

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$context = SowisoApiContext::create(
    data: ['anything-you-want' => 42],
    user: 'CURRENT_USER',
);

$response = $api->request($context, '{}'); // The JSON data from the request
```

The context can be used to pass arbitrary data from the request into the callback and hook methods.
Additionally, the context holds some data that is required by some endpoints:

- **PlayExerciseSet**
    - `SowisoApiContext#user`

#### Payload

The SDK supports passing additional payload from every request into the callbacks and hooks.
This additional payload can be specified by a `__additionalPayload` field in the request JSON.
When no or an empty field is provided, the `SowisoApiPayload#getData()` method returns `null`.

#### Exceptions

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$context = SowisoApiContext::create();

try {
    $response = $api->request($context, '{}');
} catch (NoBaseUrlException $e) {
    // when no API base url is set
} catch (NoApiKeyException $e) {
    // when no API key is set
} catch (NoUserException $e) {
    // when no API user is set in the context (and the requested endpoint requires it)
} catch (NoEndpointException $e) {
    // when the API request is missing the endpoint to fetch
} catch (InvalidEndpointException $e) {
    // when the API request has specified an invalid endpoint to fetch
} catch (InvalidJsonDataException $e) {
    // when the SDK cannot handle JSON data
} catch (InvalidJsonRequestException $e) {
    // when the API request has invalid JSON data
} catch (InvalidJsonResponseException $e) {
    // when the API response has invalid JSON data
} catch (MissingDataException $e) {
    // when the API request or response is missing required data
} catch (InvalidDataException $e) {
    // when the API request or response contains invalid data
} catch (ResponseErrorException $e) {
    // when the API response has an error
} catch (FetchingFailedException $e) {
    // when the internal HTTP request to the API has failed for some reason
} catch (DataVerificationFailedException $e) {
    // when verifying data failed in the "DataVerificationHook"
} catch (SowisoApiException $e) {
    // when any other SDK-related error occurs
} catch (\Exception $e) {
    // when any other error occurs
}
```

_Note: It's not recommended catching every exception like it's done in the example._

## Endpoints

The SDK is designed in a way that it takes JSON data as input and returns the API's response in that same format. The
input JSON is required to be a JSON object that contains the endpoint name in a top-level field called `__endpoint`.
That field determines the actual SOWISO API endpoint to call. All data in that JSON object is passed to the API
endpoint, except all fields starting with `__`. The response can either be a JSON object or a JSON array.

_The following example shows the basic use of the SDK and how to specify the API endpoint._

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$context = SowisoApiContext::create();

$response = $api->request($context, '{"__endpoint": "ENDPOINT_NAME"}'); // Use any actual endpoint name here, e.g., "play/set"
```

At the moment, the following endpoints are available in the SDK:

#### PlayExerciseSet

- **Name**: play/set
- **API Endpoint**:
    - `GET` `/api/play/set/set_id/:set_id/username/:username/lang/:lang/view/:view`
    - `GET` `/api/play/set/try_id/:try_id/username/:username/lang/:lang/view/:view`
- **API Documentation**: https://cloud.sowiso.nl/documentation/api/index.html#api-Player-PlaySet
- **Request fields**:
    - `set_id` (Integer) **OR** `try_id` (Integer)
    - `lang` (String) [optional]
    - `view` (String) (`student|readonly`) [optional, default=student]
    - `mode` (String) (`practice|print`) [optional, default=practice]
- **Response fields**:
    - `{exercise_id, try_id}[]` (`ExerciseTry[]`)
- **Required context fields**:
    - `username` (String)

_When the requested view is set to `readonly`, no "Try IDs" are returned for the exercises in that set.
Hence, the corresponding hooks that handle "Try IDs" are not called._

_When a request contains a `try_id` instead of a `set_id`, the `mode` makes a difference to the API response:_

- _`mode=practice` - the exercise set that belongs to that `try_id` is being **continued**_
- _`mode=print` - the exercise set that belongs to that `try_id` is being **restarted** from
  the first exercise of the set_
- _`mode=test` - the exercise set that belongs to that `try_id` is being **restarted** from
  the first exercise of the set (can only be enabled via the `TestMode` hook)_

_Providing both the `try_id` and `set_id` fields is not allowed and results in an `InvalidDataException`._

#### PlayExercise

- **Name**: play/exercise
- **API Endpoint**: `GET` `/api/play/exercise/try_id/:try_id/username/:username/lang/:lang`
- **API Documentation**: https://cloud.sowiso.nl/documentation/api/index.html#api-Player-PlayExercise
- **Request fields**:
    - `try_id` (Integer)
    - `lang` (String) [optional]
    - `view` (String) (`student`) [optional, default=student]
- **Response fields**: _none_
- **Required context fields**:
    - `username` (String)

_This endpoint only supports getting an exercise for a "Try ID", for now. Requesting an exercise for an "Exercise ID"
might be supported in a later version of the SDK._

#### ReplayExerciseTry

- **Name**: replay/try
- **API Endpoint**: `GET` `/api/play/replay/try_id/:try_id/lang/:lang`
- **API Documentation**: https://cloud.sowiso.nl/documentation/api/index.html#api-Player-ReplayExercise
- **Request fields**:
    - `try_id` (Integer)
    - `lang` (String) [optional]
- **Response fields**: _none_

#### EvaluateAnswer

- **Name**: evaluate/answer
- **API Endpoint**: `POST` `/api/evaluate/answer`
- **API Documentation**: https://cloud.sowiso.nl/documentation/api/index.html#api-Player-EvaluateAnswer
- **Request fields**:
    - `try_id` (Integer)
- **Response fields**:
    - `exercise_evaluation->completed` (Boolean)
    - `exercise_evaluation->score` (Number)

#### PlayHint

- **Name**: play/hint
- **API Endpoint**: `GET` `/api/play/hint/try_id/:try_id/lang/:lang`
- **API Documentation**: https://cloud.sowiso.nl/documentation/api/index.html#api-Player-ExerciseHint
- **Request fields**:
    - `try_id` (Integer)
    - `lang` (String) [optional]
- **Response fields**: _none_

#### PlaySolution

- **Name**: play/solution
- **API Endpoint**: `GET` `/api/play/solution/try_id/:try_id/lang/:lang`
- **API Documentation**: https://cloud.sowiso.nl/documentation/api/index.html#api-Player-ExerciseSolution
- **Request fields**:
    - `try_id` (Integer)
    - `lang` (String) [optional]
- **Response fields**:
    - `score` (Number)

#### StoreAnswer

- **Name**: store/answer
- **API Endpoint**: `POST` `/api/store/answer`
- **API Documentation**: https://cloud.sowiso.nl/documentation/api/index.html#api-Store-StoreAnswer
- **Request fields**:
    - `try_id` (Integer)
- **Response fields**: _none_

## Callbacks

The SDK allows specifying callback implementations for each endpoint. Every callback provides four methods to
(optionally) implement:

- The `onRequest` callback method is called before the request is forwarded to the SOWISO API. Its data contains the
  request data and possibly the context data.
- The `onResponse` callback method is called after the response from the SOWISO API was returned. Its data contains the
  response data and possibly the context data.
- The `onSuccess` callback method is called when both the request and response was successful. Its data contains the
  request data, response data, and possibly the context data.
- The `onFailure` callback method is called when either the request or response was not successful. Its data contains
  the exception and possibly the context data.

When multiple callbacks for the same endpoint are used, they are executed in the order they were specified.

#### PlayExerciseSet

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useCallback(new class extends PlayExerciseSetCallback {
    public function onRequest(PlayExerciseSetOnRequestData $data): void {}
    public function onResponse(PlayExerciseSetOnResponseData $data): void {}
    public function onSuccess(PlayExerciseSetOnSuccessData $data): void {}
    public function onFailure(PlayExerciseSetOnFailureData $data): void {}
});
```

#### PlayExercise

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useCallback(new class extends PlayExerciseCallback {
    public function onRequest(PlayExerciseOnRequestData $data): void {}
    public function onResponse(PlayExerciseOnResponseData $data): void {}
    public function onSuccess(PlayExerciseOnSuccessData $data): void {}
    public function onFailure(PlayExerciseOnFailureData $data): void {}
});
```

#### ReplayExerciseTry

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useCallback(new class extends ReplayExerciseTryCallback {
    public function onRequest(ReplayExerciseTryOnRequestData $data): void {}
    public function onResponse(ReplayExerciseTryOnResponseData $data): void {}
    public function onSuccess(ReplayExerciseTryOnSuccessData $data): void {}
    public function onFailure(ReplayExerciseTryOnFailureData $data): void {}
});
```

#### EvaluateAnswer

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useCallback(new class extends EvaluateAnswerCallback {
    public function onRequest(EvaluateAnswerOnRequestData $data): void {}
    public function onResponse(EvaluateAnswerOnResponseData $data): void {}
    public function onSuccess(EvaluateAnswerOnSuccessData $data): void {}
    public function onFailure(EvaluateAnswerOnFailureData $data): void {}
});
```

#### PlayHint

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useCallback(new class extends PlayHintCallback {
    public function onRequest(PlayHintOnRequestData $data): void {}
    public function onResponse(PlayHintOnResponseData $data): void {}
    public function onSuccess(PlayHintOnSuccessData $data): void {}
    public function onFailure(PlayHintOnFailureData $data): void {}
});
```

#### PlaySolution

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useCallback(new class extends PlaySolutionCallback {
    public function onRequest(PlaySolutionOnRequestData $data): void {}
    public function onResponse(PlaySolutionOnResponseData $data): void {}
    public function onSuccess(PlaySolutionrOnSuccessData $data): void {}
    public function onFailure(PlaySolutionOnFailureData $data): void {}
});
```

#### StoreAnswer

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useCallback(new class extends StoreAnswerCallback {
    public function onRequest(StoreAnswerOnRequestData $data): void {}
    public function onResponse(StoreAnswerOnResponseData $data): void {}
    public function onSuccess(StoreAnswerOnSuccessData $data): void {}
    public function onFailure(StoreAnswerOnFailureData $data): void {}
});
```

#### PlayTheory

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useCallback(new class extends PlayTheoryCallback {
    public function onRequest(PlayTheoryOnRequestData $data): void {}
    public function onResponse(PlayTheoryOnResponseData $data): void {}
    public function onSuccess(PlayTheoryrOnSuccessData $data): void {}
    public function onFailure(PlayTheoryOnFailureData $data): void {}
});
```

## RequestHandlers

The SDK allows specifying request handlers for each endpoint. Every request handler provides a `handle` method to (optionally) implement. Its data contains the
request data and possibly the context data. When the `handle` method returns any array, the request is not passed to the API. If the `handle` method does
return `null`, the request is passed to the API. The SDK still runs all callback methods for the endpoint.

When multiple request handlers for the same endpoint are used, only the latest is being used to handle the request.

#### PlayExerciseSet

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useRequestHandler(new class extends PlayExerciseSetRequestHandler {
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseSetRequest $request): ?array
    {
        return null; // Or whatever should be the response
    }
});
```

#### PlayExercise

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useRequestHandler(new class extends PlayExerciseRequestHandler {
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayExerciseRequest $request): ?array
    {
        return null; // Or whatever should be the response
    }
});
```

#### ReplayExerciseTry

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useRequestHandler(new class extends ReplayExerciseTryRequestHandler {
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, ReplayExerciseTryRequest $request): ?array
    {
        return null; // Or whatever should be the response
    }
});
```

#### EvaluateAnswer

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useRequestHandler(new class extends EvaluateAnswerRequestHandler {
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, EvaluateAnswerRequest $request): ?array
    {
        return null; // Or whatever should be the response
    }
});
```

#### PlayHint

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useRequestHandler(new class extends PlayHintRequestHandler {
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayHintRequest $request): ?array
    {
        return null; // Or whatever should be the response
    }
});
```

#### PlaySolution

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useRequestHandler(new class extends PlaySolutionRequestHandler {
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlaySolutionRequest $request): ?array
    {
        return null; // Or whatever should be the response
    }
});
```

#### StoreAnswer

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useRequestHandler(new class extends StoreAnswerRequestHandler {
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, StoreAnswerRequest $request): ?array
    {
        return null; // Or whatever should be the response
    }
});
```

#### PlayTheory

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useRequestHandler(new class extends PlayTheoryRequestHandler {
    public function handle(SowisoApiContext $context, SowisoApiPayload $payload, PlayTheoryRequest $request): ?array
    {
        return null; // Or whatever should be the response
    }
});
```

## Hooks

The SDK provides so-called hooks, which _hook into_ one or more callbacks. They are used for providing some
SOWISO-specific functionality.

### DataVerification

The `DataVerificationHook` simplifies the verification of data in requests before they are passed to the API. The main
use cases for this hook is making sure that any request data tampering that might happen between the SOWISO Player and
the SDK gets detected and blocked, before it's passed to the API.

The data verification can be done on a per-endpoint level. For example, all requests that are going to the "play/set"
endpoint will be passed through the `verifyPlayExerciseSetRequest()` method before being sent to the API. Whenever the
verification of data failed, the implementation can throw a `SowisoApiException.DataVerificationFailed` exception (or
any other exception) which aborts the request stack (i.e., the request is not passed to the API) and exists
the `SowisoApi#request()` method.

Along with the parsed, endpoint-specific request data, all request data objects contain the following properties:

- `context` - The context object that's passed into the `SowisoApi#request()` method
- `payload` - The JSON data that's passed in the `__additionalPayload` field of the request

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useHook(new class extends DataVerificationHook {
    public function verifyPlayExerciseSetRequest(PlayExerciseSetOnRequestData $data): void {}
    public function verifyPlayExerciseRequest(PlayExerciseOnRequestData $data): void {}
    public function verifyReplayExerciseTryRequest(ReplayExerciseTryOnRequestData $data): void {}
    public function verifyEvaluateAnswerRequest(EvaluateAnswerOnRequestData $data): void {}
    public function verifyPlayHintRequest(PlayHintOnRequestData $data): void {}
    public function verifyPlaySolutionRequest(PlaySolutionOnRequestData $data): void {}
    public function verifyStoreAnswerRequest(StoreAnswerOnRequestData $data): void {}
});
```

#### Migrating from the TryIdVerification hook to the DataVerification hook

The new `DataVerificationHook` simplifies the whole verification process by a lot; however, it required a bit more
logic in the hook's implementation.

First of all, we have removed the `onRegisterTryId()` method from the `TryIdVerificationHook` since it overlapped with
the `DataCaptureHook::onRegisterExerciseSet()`. When you haven't used this method in the `DataCaptureHook` to register
new exercise tries, please do this.

Secondly, we have removed the `onCatchInvalidTryId()` method. This was an additional and optional helper method that
could be implemented for the cases when an invalid "Try ID" was caught. The same behavior could also be achieved by
catching the `InvalidTryIdException` exception (which will be removed as well) and will be available by catching
the `DataVerificationFailedException` exception instead.

And lastly, the `isValidTryId()` method. This was called on every request that contained a "Try ID". With the
new `DataVerificationHook`, there will be new `validate...Request()` methods available for every endpoint where you can
validate the given "Try ID".

The following example shows an implementation of the `DataVerificationHook` where the private `verifyTryId()` method
resembles the old `isValidTryId()` method (an `DataVerificationFailedException` exception should be thrown
instead of returning false). As you can see, only the "play/set" endpoint needs some special attention at the moment
since that endpoint allows requesting a "Try ID" and a "Set ID" (but not at the same time). Additionally, this example
shows where to verify other request data parameters.

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useHook(new class extends DataVerificationHook {
    public function verifyPlayExerciseSetRequest(PlayExerciseSetOnRequestData $data): void {
        if ($data->getRequest()->usesTryId()) {
            $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
        } else {
            // $this->verifySetId($data->getRequest()->getSetId());
        }

        // $this->verifyView($data->getRequest()->getView());
    }

    public function verifyPlayExerciseRequest(PlayExerciseOnRequestData $data): void {
        $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
        // $this->verifyView($data->getRequest()->getView());
    }

    public function verifyReplayExerciseTryRequest(ReplayExerciseTryOnRequestData $data): void {
        $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
    }

    public function verifyEvaluateAnswerRequest(EvaluateAnswerOnRequestData $data): void {
        $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
    }

    public function verifyPlayHintRequest(PlayHintOnRequestData $data): void {
        $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
    }

    public function verifyPlaySolutionRequest(PlaySolutionOnRequestData $data): void {
        $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
    }

    public function verifyStoreAnswerRequest(StoreAnswerOnRequestData $data): void {
        $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
    }

    private function verifyTryId(SowisoApiContext $context, SowisoApiPayload $payload, int $tryId): void {
        // ...
    }
});
```

### DataCapture

The `DataCapture` hook simplifies receiving common, processed data. It can be extended in the future to provide other
common data.

The `OnRegisterExerciseSetData` object contains the following properties:

- `setId` - The "Set ID" of the set containing the exercise
- `exerciseTries` - The list of exercise tries, each including the "Exercise ID" and "Try ID"
- `context` - The context object that's passed into the `SowisoApi#request()` method
- `payload` - The JSON data that's passed in the `__additionalPayload` field of the request

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useHook(new class extends DataCaptureHook {
    public function onRegisterExerciseSet(OnRegisterExerciseSetData $data): void {}
});
```

### ScoreCapture

The `ScoreCapture` hook wraps all endpoints that return some form of score for a user. The `OnScoreData` object contains
the following properties:

- `tryId` - The "Try ID" to which the score is related
- `score` - A number between 0.0 and 10.0 that represents the score, taking history into account
- `completed` - A boolean that indicates whether the answer is completed/final
- `source` - A enum that indicates which endpoint has returned this score
- `context` - The context object that's passed into the `SowisoApi#request()` method
- `payload` - The JSON data that's passed in the `__additionalPayload` field of the request

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useHook(new class extends ScoreCaptureHook {
    public function onScore(OnScoreData $data): void {}
});
```

### TestMode

The `TestMode` hook simplifies everything related to playing and evaluating in "test" mode.
By implementing and using this hook, you can decide if a certain "Exercise Set" or "Exercise Try" should use the "test" mode.

_Note: When an "Exercise Set" was played in "test" mode, the returned "Exercise Tries" should also be evaluated in "test" mode._

All data objects contain the following properties:

- `context` - The context object that's passed into the `SowisoApi#request()` method
- `payload` - The JSON data that's passed in the `__additionalPayload` field of the request

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useHook(new class extends TestModeHook {
    public function shouldExerciseSetBePlayedInTestMode(ShouldExerciseSetBePlayedInTestModeData $data): bool {}
    public function shouldExerciseTryBePlayedInTestMode(ShouldExerciseTryBePlayedInTestModeData $data): bool {}
    public function shouldExerciseTryBeEvaluatedInTestMode(ShouldExerciseTryBeEvaluatedInTestModeData $data): bool {}
});
```
