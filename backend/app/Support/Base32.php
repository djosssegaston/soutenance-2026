<?php

namespace App\Support;

class Base32
{
    private const CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function decode(string $input): string
    {
        $input = strtoupper($input);
        $input = str_replace('=', '', $input);

        $output = '';
        $buffer = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($input); $i++) {
            $charValue = strpos(self::CHARS, $input[$i]);

            if ($charValue === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $charValue;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $output .= chr(($buffer >> ($bitsLeft - 8)) & 0xFF);
                $bitsLeft -= 8;
            }
        }

        return $output;
    }
}
