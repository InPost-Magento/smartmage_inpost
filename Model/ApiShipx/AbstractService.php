<?php
namespace Smartmage\Inpost\Model\ApiShipx;

use Magento\Framework\App\Response\Http;

abstract class AbstractService implements ServiceInterface
{
    const API_RESPONSE_MESSAGE_KEY = 'message';
    const API_RESPONSE_DETAILS_KEY = 'details';

    const API_RESPONSE_VALIDATION_KEYS_KEY = [
        'required' => 'Podanie wartości jest wymagane.',
        'invalid' => 'Podana wartość jest nieprawidłowa.',
        'too_short' => 'Podana wartość jest zbyt krótka.',
        'too_long' => 'Podana wartość jest zbyt długa.',
        'too_small' => 'Podana wartość jest zbyt mała.',
        'too_big' => 'Podana wartość jest zbyt duża.',
        'invalid_format' => 'Podana wartość ma niepoprawny format, np. gdy w pole numer telefonu zostały wpisane litery.',
        'not_a_number' => 'Wprowadzona wartość powinna być liczbą.',
        'not_an_integer' => 'Wprowadzona wartość powinna być liczbą całkowitą.'
    ];

    protected $method;

    protected $successResponseCode;

    protected $requestHeaders = [];

    protected $responseHeaders = [];

    protected $responseBody;

    protected $responseStatus;

    protected $timeout = 60;

    protected $callUri;

    public function getMode()
    {
        //todo full_implementation
        return \Smartmage\Inpost\Model\Config\Source\Mode::TEST;
    }

    public function getBaseUri()
    {
        if ($this->getMode() === \Smartmage\Inpost\Model\Config\Source\Mode::TEST) {
            return \Smartmage\Inpost\Model\Config\Source\Mode::TEST_BASE_URI;
        } else if ($this->getMode() === \Smartmage\Inpost\Model\Config\Source\Mode::PROD) {
            return \Smartmage\Inpost\Model\Config\Source\Mode::PROD_BASE_URI;
        } else {
            return null;
        }
    }

    public function call($requestBody = null, $parameters = null)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $ch = curl_init();

        $this->requestHeaders['Authorization'] = "Authorization: Bearer " . "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJzYW5kYm94LWFwaS1zaGlweC1wbC5lYXN5cGFjazI0Lm5ldCIsInN1YiI6InNhbmRib3gtYXBpLXNoaXB4LXBsLmVhc3lwYWNrMjQubmV0IiwiZXhwIjoxNTAyMTg4NjIyLCJpYXQiOjE1MDIxODg2MjIsImp0aSI6IjY0YjJmMTVjLTk3OWEtNDU5MC1hZGU0LWZiYzk1MmFmMGE5YyJ9.KLHwKX9c6H5a2trQMCvUIHfOurPeDomv84MoSLbPN6AdhMiRfZ197Y2OhtNwTnwyFUE2zObylLXrIvYaWZo7Aw";

        $logger->info(print_r($this->getBaseUri() . '/' . $this->callUri, true));

        $endpoint = $this->getBaseUri() . '/' . $this->callUri;

        if ($this->method === CURLOPT_HTTPGET && is_array($parameters)) {
            $url = $endpoint . '?' . http_build_query($parameters);
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
        $response = json_decode($response, true);

        if ($responseCode == $this->successResponseCode){
            curl_close($ch);
            $logger->info(print_r($response, true));
            return $response;
        } else if ($responseCode == Http::STATUS_CODE_400) { //Przy przesyłaniu danych metodą POST lub PUT wystąpiły błędy w walidacji. Szczegółowe błędy walidacji zawarte są pod atrybutem details.
            $errorsStr = '';
            if (isset($response[self::API_RESPONSE_DETAILS_KEY])){
                foreach ($response[self::API_RESPONSE_DETAILS_KEY] as $k => $detail) {
                    $errorsStr .= '[ ' . $k . ' : ';
                    foreach ($detail as $detailItem) {
                        $errorsStr .= '( ' . $detail . ' ), ';
                    }
                    $errorsStr .= ' ], ';
                }
            }
            throw new \Exception($response[self::API_RESPONSE_MESSAGE_KEY] . ' - ' . $errorsStr, $responseCode);
        } else if ($responseCode == Http::STATUS_CODE_401) { //Dostęp do zasobu jest niemożliwy ponieważ zapytanie nie zostało podpisane kluczem access token.
            throw new \Exception($response[self::API_RESPONSE_MESSAGE_KEY], $responseCode);
        } else if ($responseCode == Http::STATUS_CODE_403) { //Dostęp do określone zasobu jest zabroniony dla tego zapytania (np. z powodu braku lub niewłaściwego zakresu uprawnień).
            throw new \Exception($response[self::API_RESPONSE_MESSAGE_KEY], $responseCode);
        } else if ($responseCode == Http::STATUS_CODE_404) { //Szukany zasób nie został odnaleziony, np. adres URL jest niepoprawny lub zasób nie istnieje.
            throw new \Exception($response[self::API_RESPONSE_MESSAGE_KEY], $responseCode);
        } else if ($responseCode == Http::STATUS_CODE_500) { //Wystąpił błąd po stronie serwera.
            throw new \Exception($response[self::API_RESPONSE_MESSAGE_KEY], $responseCode);
        }

        if ($response === false) {
            $errNo = curl_errno($ch);
            $errStr = curl_error($ch);
            curl_close($ch);
            if (empty($errStr)) {
                $logger->info(print_r('emty error', true));
                $logger->info(print_r($responseCode, true));
                throw new \Exception('Failed to request resource.', $responseCode);
            }

            $logger->info(print_r('notemty error', true));
            $logger->info(print_r($errNo, true));
            $logger->info(print_r($errStr, true));
            $logger->info(print_r($responseCode, true));

            throw new \Exception('cURL Error # '.$errNo.': '.$errStr, $responseCode);
        }

    }
}
