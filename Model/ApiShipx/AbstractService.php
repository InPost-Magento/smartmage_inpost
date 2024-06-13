<?php
namespace Smartmage\Inpost\Model\ApiShipx;

use Magento\Framework\App\Response\Http;
use Smartmage\Inpost\Model\ConfigProvider;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

abstract class AbstractService implements ServiceInterface
{
    const API_RESPONSE_MESSAGE_KEY = 'message';
    const API_RESPONSE_DETAILS_KEY = 'details';

    const API_RESPONSE_ERROR_KEY = 'error';
    const API_ERROR_VALIDATION_FAILED = 'validation_failed';

    const API_RESPONSE_VALIDATION_KEYS_KEY = [
        'required' => 'Podanie wartości jest wymagane.',
        'invalid' => 'Podana wartość jest nieprawidłowa.',
        'too_short' => 'Podana wartość jest zbyt krótka.',
        'too_long' => 'Podana wartość jest zbyt długa.',
        'too_small' => 'Podana wartość jest zbyt mała.',
        'too_big' => 'Podana wartość jest zbyt duża.',
        'invalid_format' => 'Podana wartość ma niepoprawny format, np. gdy w pole numer telefonu zostały wpisane litery.',
        'not_a_number' => 'Wprowadzona wartość powinna być liczbą.',
        'not_an_integer' => 'Wprowadzona wartość powinna być liczbą całkowitą.',
        'label_not_found' => 'Etykieta będzie dostępna od '
    ];

    protected $method;

    protected $successResponseCode;

    protected $successMessage = 'Blank success message';

    protected $callResult;

    protected $requestHeaders = [];

    protected $requestBody;

    protected $timeout = 300;

    protected $callUri;

    protected $configProvider;

    protected $isResponseJson = true;

    protected $errorHandler;
    protected $logger;

    public function __construct(
        PsrLoggerInterface $logger,
        ConfigProvider $configProvider,
        ErrorHandler $errorHandler
    ) {
        $this->logger = $logger;
        $this->configProvider = $configProvider;
        $this->errorHandler = $errorHandler;
    }

    public function getMode()
    {
        return $this->configProvider->getMode();
    }

    public function getBaseUri()
    {
        if ($this->getMode() === \Smartmage\Inpost\Model\Config\Source\Mode::TEST) {
            return \Smartmage\Inpost\Model\Config\Source\Mode::TEST_BASE_URI;
        } elseif ($this->getMode() === \Smartmage\Inpost\Model\Config\Source\Mode::PROD) {
            return \Smartmage\Inpost\Model\Config\Source\Mode::PROD_BASE_URI;
        } else {
            return null;
        }
    }

    public function call($requestBody = null, $parameters = null)
    {
        $ch = curl_init();
        $token = $this->configProvider->getAccessToken();

        $this->requestHeaders['Authorization'] = "Authorization: Bearer " . $token;
        $this->requestHeaders['Accept-Language'] = "Accept-Language: pl_PL";

        $endpoint = $this->getBaseUri() . '/' . $this->callUri;

        if ($this->method === CURLOPT_HTTPGET && is_array($parameters)) {
            $url = $endpoint . '?' . http_build_query($parameters);
            $url = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '[]=', $url);
        } else {
            $url = $endpoint;
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        if ($this->method === CURLOPT_POST) {
            $this->requestHeaders['Content-Type'] = "Content-Type: application/json";
            $requestBodyJson = json_encode($requestBody);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBodyJson);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->requestHeaders);

        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->callResult = [
            CallResult::STRING_STATUS => CallResult::STATUS_FAIL,
            CallResult::STRING_MESSAGE => 'Default fail message',
            CallResult::STRING_RESPONSE_CODE => Http::STATUS_CODE_500
        ];

        if ($responseCode == $this->successResponseCode) {
            if ($this->isResponseJson) {
                $response = json_decode($response, true);
            }
            curl_close($ch);

            $this->callResult[CallResult::STRING_STATUS] = CallResult::STATUS_SUCCESS;
            $this->callResult[CallResult::STRING_MESSAGE] = $this->successMessage;
            $this->callResult[CallResult::STRING_RESPONSE_CODE] = $responseCode;

            return $response;
        } elseif (in_array($responseCode, [
            Http::STATUS_CODE_400,
            Http::STATUS_CODE_401,
            Http::STATUS_CODE_403,
            Http::STATUS_CODE_404,
            Http::STATUS_CODE_422,
            Http::STATUS_CODE_500
        ])) {
            $responseDecoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Log response if JSON decoding failed
                error_log("Failed to decode JSON response: " . $response);
                $responseDecoded = $response;
            }
            $errorsStr = $this->errorHandler->handle($responseDecoded);
            curl_close($ch);

            $this->callResult[CallResult::STRING_STATUS] = CallResult::STATUS_FAIL;
            $this->callResult[CallResult::STRING_MESSAGE] = $errorsStr;
            $this->callResult[CallResult::STRING_RESPONSE_CODE] = $responseCode;

            return $responseDecoded;
        } else {
            $errNo = curl_errno($ch);
            $errStr = curl_error($ch);
            curl_close($ch);

            throw new \Exception('Unknown cURL Error - ' . $errNo . ' [' . $responseCode . ']: ' . $errStr . $response, $responseCode);
        }
    }
}
