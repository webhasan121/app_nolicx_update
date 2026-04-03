<?php

namespace App\Helpers;


class help
{
    public function formatString($inputString)
    {
        $length = strlen($inputString);

        if ($length >= 4) {
            return $inputString;
        } else {
            return str_pad($inputString, 3, '0', STR_PAD_LEFT);
        }
    }
}
