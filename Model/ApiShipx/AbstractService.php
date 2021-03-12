<?php
namespace Smartmage\Inpost\Model\ApiShipx;

use Smartmage\Inpost\Model\ConfigProvider;

abstract class AbstractService implements ServiceInterface
{
    protected $method;

    protected $successResponseCode;

    protected $requestHeaders = [];

    protected $responseHeaders = [];

    protected $responseBody;

    protected $responseStatus;

    protected $timeout = 60;

    protected $callUri;

    protected $configProvider;

    public function __construct(
        ConfigProvider $configProvider
    ) {
        $this->configProvider = $configProvider;
    }

    public function getMode()
    {
        return $this->configProvider->getMode();
        //todo full_implementation
        return \Smartmage\Inpost\Model\Config\Source\Mode::TEST;
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
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/inpost.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $ch = curl_init();
        $token = $this->configProvider->getAccessToken();
        $logger->info(print_r($token, true));

        $this->requestHeaders['Authorization'] = "Authorization: Bearer " . $token;

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
        }else if (1 == 2) {
            return null;
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
