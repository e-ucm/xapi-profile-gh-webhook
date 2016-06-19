<?php
namespace es\eucm\xapi;

class StringUtils
{

    /**
     * <pre>
     * $pointer = 0;
     * while(($chr = nextchar($source, $pointer)) !== false){
     *   //echo $chr;
     * }
     * </pre>
     * http://stackoverflow.com/questions/3666306/how-to-iterate-utf-8-string-in-php/14366023#14366023
     * https://en.wikipedia.org/wiki/UTF-8
     */
    public static function nextchar($string, &$pointer)
    {
        if(!isset($string[$pointer])) return false;

        $char = ord($string[$pointer]);
        if($char < 128){
            return $string[$pointer++];
        }else{
            if($char < 224){
                $bytes = 2;
            }elseif($char < 240){
                $bytes = 3;
            }elseif($char < 248){
                $bytes = 4;
            }elseif($char == 252){
                $bytes = 5;
            }else{
                $bytes = 6;
            }
            $str =  substr($string, $pointer, $bytes);
            $pointer += $bytes;
            return $str;
        }
    }
    
    // http://nl1.php.net/manual/en/function.str-split.php#107658
    public static function str_split_unicode($str, $l = 0)
    {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
