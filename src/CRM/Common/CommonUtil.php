<?php
namespace Zoho\CRM\Common;

use Zoho\CRM\Exception\Logger;

class CommonUtil
{
    public static function getEmptyJSONObject()
    {
        return new ArrayObject();
    }
}
