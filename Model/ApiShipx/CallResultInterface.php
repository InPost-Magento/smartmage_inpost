<?php

namespace Smartmage\Inpost\Model\ApiShipx;

interface CallResultInterface
{

    const STATUS_FAIL = 0;
    const STATUS_SUCCESS = 1;

    const STRING_STATUS = 'status';
    const STRING_MESSAGE = 'message';
    const STRING_RESPONSE_CODE = 'response_code';
    const STRING_FILE = 'file';

}
