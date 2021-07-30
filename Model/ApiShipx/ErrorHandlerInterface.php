<?php

namespace Smartmage\Inpost\Model\ApiShipx;

interface ErrorHandlerInterface
{
    public function handle($jsonResponse) : string;
}
