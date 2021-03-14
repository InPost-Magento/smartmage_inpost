<?php

namespace Smartmage\Inpost\Model\ApiShipx;

class ErrorHandler implements ErrorHandlerInterface
{
    public function handle($jsonResponse): string
    {
        $errors = '[' . $jsonResponse['error'] . ']<br>';
        $errors .= $jsonResponse['message'] . '<br>';

        $details = $this->nestedValues($jsonResponse['details']);
        foreach ($details as $detail) {
            $errors .= '- ' . $detail . '<br>';
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
                $output[] = ucfirst($path) . $value;
            }
        }
        return $output;
    }
}
