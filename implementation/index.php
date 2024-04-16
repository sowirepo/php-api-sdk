<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Sowiso\SDK\Api\EvaluateAnswer\Data\EvaluateAnswerOnRequestData;
use Sowiso\SDK\Api\PlayExercise\Data\PlayExerciseOnRequestData;
use Sowiso\SDK\Api\PlayExerciseSet\Data\PlayExerciseSetOnRequestData;
use Sowiso\SDK\Api\PlayHint\Data\PlayHintOnRequestData;
use Sowiso\SDK\Api\PlaySolution\Data\PlaySolutionOnRequestData;
use Sowiso\SDK\Api\ReplayExerciseTry\Data\ReplayExerciseTryOnRequestData;
use Sowiso\SDK\Api\StoreAnswer\Data\StoreAnswerOnRequestData;
use Sowiso\SDK\Exceptions\DataVerificationFailedException;
use Sowiso\SDK\Exceptions\ResponseErrorException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\Hooks\DataCapture\Data\OnRegisterExerciseSetData;
use Sowiso\SDK\Hooks\DataCapture\DataCaptureHook;
use Sowiso\SDK\Hooks\DataVerification\DataVerificationHook;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\SowisoApiContext;
use Sowiso\SDK\SowisoApiPayload;

$api = new SowisoApi(
    configuration: new SowisoApiConfiguration(
        baseUrl: getenv('SOWISO_API_BASE_URL'),
        apiKey: getenv('SOWISO_API_KEY'),
    ),
);

$api->useHook(
    new class extends DataCaptureHook {
        public function onRegisterExerciseSet(OnRegisterExerciseSetData $data): void
        {
            logger("DataCaptureHook::onRegisterExerciseSet - {$data->getSetId()}", $data->getExerciseTries(), $data->getContext(), $data->getPayload());

            if (null === $user = $data->getContext()->getUser()) {
                return;
            }

            $registersUsers = loadRegistersUsers();

            foreach ($data->getExerciseTries() as $exerciseTry) {
                $registersUsers[$exerciseTry['tryId']] = $user;
            }

            saveRegistersUsers($registersUsers);
        }
    }
);

$api->useHook(
    new class extends DataVerificationHook {
        public function verifyPlayExerciseSetRequest(PlayExerciseSetOnRequestData $data): void
        {
            if ($data->getRequest()->usesTryId()) {
                $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
            }
        }

        public function verifyPlayExerciseRequest(PlayExerciseOnRequestData $data): void
        {
            $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
        }

        public function verifyReplayExerciseTryRequest(ReplayExerciseTryOnRequestData $data): void
        {
            $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
        }

        public function verifyEvaluateAnswerRequest(EvaluateAnswerOnRequestData $data): void
        {
            $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
        }

        public function verifyPlayHintRequest(PlayHintOnRequestData $data): void
        {
            $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
        }

        public function verifyPlaySolutionRequest(PlaySolutionOnRequestData $data): void
        {
            $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
        }

        public function verifyStoreAnswerRequest(StoreAnswerOnRequestData $data): void
        {
            $this->verifyTryId($data->getContext(), $data->getPayload(), $data->getRequest()->getTryId());
        }

        private function verifyTryId(SowisoApiContext $context, SowisoApiPayload $payload, int $tryId): void {
            $registersUsers = loadRegistersUsers();

            logger("DataVerificationHook::verifyTryId - $tryId", json_encode($registersUsers), $context, $payload);

            if (null === $user = $context->getUser()) {
                throw new DataVerificationFailedException("No user found");
            }

            if (null === $registeredUser = $this->registeredTryIds[$tryId] ?? null) {
                throw new DataVerificationFailedException("No registered user found for tryId '$tryId'");
            }

            if ($user !== $registeredUser) {
                throw new DataVerificationFailedException("User '$user' not registered for tryId '$tryId'");
            }
        }
    }
);

$json = file_get_contents('php://input');

$context = SowisoApiContext::create(
    data: ['timestamp' => time()],
    user: getUser(json_decode($json, true)),
);

header('Content-Type: application/json; charset=utf-8');

try {
    echo $api->request($context, $json);
} catch (ResponseErrorException $e) {
    http_response_code($e->getStatusCode());

    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
} catch (DataVerificationFailedException $e) {
    http_response_code(401);

    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
} catch (SowisoApiException $e) {
    http_response_code(400);

    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);

    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}
// ---

function logger(string $message, mixed ...$data): void
{
    file_put_contents('/var/www/html/implementation/logs.txt', $message . ' - ' . implode("\n", $data) . "\n", FILE_APPEND);
}

function getUser(array $json): ?string
{
    if (null !== ($userFromEnvironment = getenv('SOWISO_API_USER')) && !empty($userFromEnvironment) && is_string($userFromEnvironment)) {
        return $userFromEnvironment;
    }

    if (null !== ($userFromRequest = $json['__user'] ?? null) && !empty($userFromRequest) && is_string($userFromRequest)) {
        return $userFromRequest;
    }

    return null;
}

function loadRegistersUsers(): array
{
    if (false !== $data = file_get_contents("/var/www/html/implementation/registered-users.json")) {
        $data = $data === '' ? '{}' : $data;
        return json_decode($data, true);
    } else {
        return [];
    }
}

function saveRegistersUsers(array $registeredUsers): void
{
    file_put_contents("/var/www/html/implementation/registered-users.json", json_encode($registeredUsers, JSON_PRETTY_PRINT));
}
