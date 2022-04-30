<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * Telegram API
 */
namespace ATL\Serialization;

class Tools {
    public static function singlequote($string){
        return str_replace(array('\\', "'"), array('\\\\', "\\'"), $string);
    }
    public static function doublequote($string){
        return str_replace(array('\\', '"', '$'), array('\\\\', '\\"', '\\$'), $string);
    }

    public static function phpexe(){
        $exe = getenv('_');
        return $exe ? $exe : "/usr/bin/php";
    }

    public static function readl($tokens, $a, $b, &$i){
        $str = '';
        $u = 0;
        do {
            if(is_array($tokens[$i])){
                $str .= $tokens[$i][1];
                if(($tokens[$i][0] == T_CURLY_OPEN || $tokens[$i][0] == T_DOLLAR_OPEN_CURLY_BRACES) && $a == '{')++$u;
            }else{
                $str .= $tokens[$i];
                if($tokens[$i] == $a)++$u;
                elseif($tokens[$i] == $b)--$u;
            }
        }while(isset($tokens[++$i]) && $u != 0);
        --$i;
        return $str;
    }
}
?>