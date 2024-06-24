<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Description of Utf8Cleaner
 *
 * @author Fredy Nurman Saleh <email@fredyns.net>
 */
class Utf8cleaner
{

    /**
     * remove non-utf8 character
     *
     * @param string $text
     *
     * @return string
     */
    public static function clean($text = "")
    {
        $text = strtolower(preg_replace('/(<([^>]+)>)/i', '', preg_replace('/[^(\x20-\x7F)]*/', '', $text)));

        $regex = <<<'END'
/
  (
    (?: [\x00-\x7F]               # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]    # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2} # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3} # quadruple-byte sequence 11110xxx 10xxxxxx * 3
    ){1,100}                      # ...one or more times
  )
| ( [\x80-\xBF] )                 # invalid byte in range 10000000 - 10111111
| ( [\xC0-\xFF] )                 # invalid byte in range 11000000 - 11111111
/x
END;

        return preg_replace_callback($regex, "self::_replacer", $text);
    }

    /**
     * replace non-utf8 character
     *
     * @param string $captures
     *
     * @return string
     */
    private static function _replacer($captures)
    {
        if ($captures[1] != "") {
            // Valid byte sequence. Return unmodified.
            return $captures[1];
        } elseif ($captures[2] != "") {
            // Invalid byte of the form 10xxxxxx.
            // Encode as 11000010 10xxxxxx.
            return "\xC2" . $captures[2];
        } else {
            // Invalid byte of the form 11xxxxxx.
            // Encode as 11000011 10xxxxxx.
            return "\xC3" . chr(ord($captures[3]) - 64);
        }
    }
}
