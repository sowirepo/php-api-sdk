<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Sowiso\SDK\Exceptions\InvalidTryIdException;
use Sowiso\SDK\Exceptions\ResponseErrorException;
use Sowiso\SDK\Exceptions\SowisoApiException;
use Sowiso\SDK\Hooks\TryIdVerification\Data\IsValidTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\Data\OnCatchInvalidTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\Data\OnRegisterTryIdData;
use Sowiso\SDK\Hooks\TryIdVerification\TryIdVerificationHook;
use Sowiso\SDK\SowisoApi;
use Sowiso\SDK\SowisoApiConfiguration;
use Sowiso\SDK\SowisoApiContext;

$api = new SowisoApi(
    configuration: new SowisoApiConfiguration(
        baseUrl: getenv('SOWISO_API_BASE_URL'),
        apiKey: getenv('SOWISO_API_KEY'),
    ),
);

$api->useHook(
    new class extends TryIdVerificationHook {
        private array $registeredTryIds;

        public function __construct()
        {
            $this->loadRegistersUsers();
        }

        public function onRegisterTryId(OnRegisterTryIdData $data): void
        {
            logger("TryIdVerificationHook::onRegisterTryId - {$data->getTryId()}", $data->getContext());

            if (null !== $user = $data->getContext()->getUser()) {
                $this->registeredTryIds[$data->getTryId()] = $user;
                $this->saveRegistersUsers();
            }
        }

        public function onCatchInvalidTryId(OnCatchInvalidTryIdData $data): void
        {
            logger("TryIdVerificationHook::onCatchInvalidTryId - {$data->getTryId()}", $data->getContext());
        }

        public function isValidTryId(IsValidTryIdData $data): bool
        {
            logger("TryIdVerificationHook::isValidTryId - {$data->getTryId()}", implode(', ', $this->registeredTryIds), $data->getContext());

            if (null === $user = $data->getContext()->getUser()) {
                return false;
            }

            if (null === $registeredUser = $this->registeredTryIds[$data->getTryId()] ?? null) {
                return false;
            }

            if ($user !== $registeredUser) {
                return false;
            }

            return true;
        }

        private function loadRegistersUsers(): void
        {
            if (false !== $data = file_get_contents("/var/www/html/implementation/registered-users.json")) {
                $data = $data === '' ? '{}' : $data;
                $this->registeredTryIds = json_decode($data, true);
            } else {
                $this->registeredTryIds = [];
            }
        }

        private function saveRegistersUsers(): void
        {
            file_put_contents("/var/www/html/implementation/registered-users.json", json_encode($this->registeredTryIds, JSON_PRETTY_PRINT));
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
} catch (InvalidTryIdException $e) {
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
