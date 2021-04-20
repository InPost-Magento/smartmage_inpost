<?php

namespace Smartmage\Inpost\Model\ApiShipx;

class ErrorHandler implements ErrorHandlerInterface
{
    public function handle($jsonResponse): string
    {
        $errors = '[' . $jsonResponse['error'] . ']<br>';
        $errors .= $jsonResponse['message'] . '<br>';

        if (is_array($jsonResponse['details'])) {
            $details = $this->nestedValues($jsonResponse['details']);
            foreach ($details as $key => $detail) {
                if ($detail) {
                    $errors .= '- ' . __($detail) . '<br>';
                }
            }
        } else {
            if ($jsonResponse['details']) {
                $errors .= '- ' . __($jsonResponse['details']);
            }
        }


        return $errors;
    }

    protected function nestedValues($array, $path = ""): array
    {
        $output = [];
        foreach ($array as $key => $value) {
            $nested_value = (is_int($key)) ? $path : $path . $key . ' ';
            if (is_array($value)) {
                $output = array_merge($output, $this->nestedValues($value, $nested_value));
            } else {
                $tmpValue = ucfirst($path) . $value;
                if ($key == 'shipment_id') {
                    $tmpValue = 'Parcel ID: ' . $tmpValue;
                }
                $output[] = $tmpValue;
            }
        }
        return $output;
    }
}
