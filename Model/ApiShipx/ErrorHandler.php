<?php

namespace Smartmage\Inpost\Model\ApiShipx;

class ErrorHandler implements ErrorHandlerInterface
{
    public function handle($jsonResponse): string
    {
        if (!is_array($jsonResponse)) {
            $raw = is_string($jsonResponse) ? substr($jsonResponse, 0, 500) : '';
            return $raw !== '' ? __('API error. Invalid response: %1', [$raw]) : __('API error. Invalid or empty response.');
        }

        $error = $jsonResponse['error'] ?? '';
        $message = $jsonResponse['message'] ?? '';
        $errors = '[' . $error . ']<br>';
        $errors .= $message . '<br>';

        $details = $jsonResponse['details'] ?? null;
        if (is_array($details)) {
            $detailLines = $this->nestedValues($details);
            foreach ($detailLines as $detail) {
                if ($detail) {
                    $errors .= '- ' . __($detail) . '<br>';
                }
            }
        } elseif ($details) {
            $errors .= '- ' . __($details);
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
                    $tmpValue = __('Parcel ID:') . ' ' . $tmpValue;
                } elseif ($key == 'label_available_from') {
                    $tmpValue = '<strong>' . __('The 2D variant allows you to generate a label 48 hours after creating the shipment (Saturday, Sunday and Monday are excluded)') . '</strong><br>';
                    $tmpValue .= __('Label available from:') . ' ' . $value;
                }
                $output[] = $tmpValue;
            }
        }
        return $output;
    }
}
