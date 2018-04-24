<?php
/**
 *
 * @category   Fnde
 * @package    View
 * @subpackage Helper_Format
 */

class Fnde_View_Helper_Format
{
    /**
     * Format values.
     *
     * value for apply format ex: 12345678900
     * format output ex: 999.999.999-99
     * return 123.456.789-00
     *
     * @param  string $str
     * @param  string $format
     * @return string
     */
    public function format($str, $format)
    {
        $str     = (string) $str;
        $format = (string) $format;
        if (!$str) {
            return null;
        }
        for ($i = 0, $len = strlen( $format ); $i < $len; $i++) {
            if ($format[$i] != 9) {
                $str = substr($str, 0, $i) . $format[$i] . substr($str, $i);
            }
        }
        return substr($str, 0, strlen($format));
    }
}