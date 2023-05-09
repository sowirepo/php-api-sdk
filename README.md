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
} catch (InvalidTryIdException $e) {
    // when an invalid SOWISO try id is caught
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
- **Response fields**:
    - `{exercise_id, try_id}[]` (`ExerciseTry[]`)
- **Required context fields**:
    - `username` (String)

_When the requested view is set to `readonly`, no "Try IDs" are returned for the exercises in that set.
Hence, the corresponding hooks that handle "Try IDs" are not called._

_When a request contains a `try_id` instead of a `set_id`, the API _continues_ the exercise set that belongs to that `try_id`.
Providing both fields is not allowed and results in an `InvalidDataException`._

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

## Hooks

The SDK provides so-called hooks, which _hook into_ one or more callbacks. They are used for providing some
SOWISO-specific functionality.

#### TryIdVerification

The `TryIdVerification` hook wraps all endpoints that deal with "Try IDs". These IDs are used by SOWISO to distinguish
between different attempts (_tries_) of exercises.

The `onRegisterTryId()` method is called when a new "Try ID" is returned by the API (e.g., from the `PlayExerciseSet`
endpoint). For any consecutive request that depends on a "Try ID" (e.g., to the `EvaluateAnswer` endpoint),
the `isValidTryId()` method should return _false_ when the "Try ID" wasn't registered to the current user. In that case,
the request is aborted and the `onCatchInvalidTryId()` method is called. All data objects contains the following
properties:

- `tryId` - The "Try ID" that was created or requested
- `context` - The context object that's passed into the `SowisoApi#request()` method
- `payload` - The JSON data that's passed in the `__additionalPayload` field of the request

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useHook(new class extends TryIdVerificationHook {
    public function onRegisterTryId(OnRegisterTryIdData $data): void {}
    public function onCatchInvalidTryId(OnCatchInvalidTryIdData $data): void {}
    public function isValidTryId(IsValidTryIdData $data): bool { return true; }
});
```

### DataCapture

The `DataCapture` hook simplifies receiving common, processed data. It can be extended in the future to provide other
common data.

The `OnRegisterExerciseTryData` object contains the following properties:

- `setId` - The "Set ID" of the set containing the exercise
- `exerciseId` - The ID of the exercise itself
- `tryId` - The "Try ID" of the "Exercise Try"
- `context` - The context object that's passed into the `SowisoApi#request()` method
- `payload` - The JSON data that's passed in the `__additionalPayload` field of the request

```php
$api = new SowisoApi(SowisoApiConfiguration::create()); // The configuration is needed here

$api->useHook(new class extends DataCaptureHook {
    public function onRegisterExerciseTry(OnRegisterExerciseTryData $data): void {}
});
```

#### ScoreCapture

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
