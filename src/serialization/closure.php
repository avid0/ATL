<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * Telegram API
 */
namespace ATL\Serialization;
use ATL\Serialization\Tools;

class Closure {
    /**
     * Get Reflection object of callable
     * 
     * @static 
     * @method getReflection
     * @param callable $callable
     * @return object
     */
    public static function getReflection($callable){
        if($callable instanceof \ReflectionFunction || $callable instanceof \ReflectionMethod)
            return $callable;
        elseif(is_object($callable) || is_numeric($callable))
            return new \ReflectionFunction($callable);
        elseif(is_string($callable)){
            $reflection = explode('::', $callable, 2);
            if(!isset($reflection[1]))
                return new \ReflectionFunction($reflection[0]);
            else
                return new \ReflectionMethod($reflection[0], $reflection[1]);
        }elseif(is_array($callable)){
            if(!isset($callable[0]))
                return false;
            if(!isset($callable[1]))
                return new \ReflectionFunction($callable[0]);
            else
                return new \ReflectionMethod($callable[0], $callable[1]);
        }else
            return false;
    }

    /**
     * Get callable inner code
     * 
     * @static
     * @method inner
     * @param callable $callable
     * @return string
     */
    public static function inner($callable){
        if(is_string($callable)){
            if($callable == '')
                return '';
            $callable = strtolower($callable);
            if(in_array($callable, ["exit", "die"]))
                return $callable.';';
        }
        $reflection = self::getReflection($callable);
        if(!$reflection)
            return '';
        $filename = $reflection->getFileName();
        if(!$filename)
            return $callable.'();';
        $start = $reflection->getStartLine() - 1;
        $end = $reflection->getEndLine();
        $source = file($filename);
        $source = array_slice($source, $start, $end - $start);
        $source = implode('', $source);
        $name = strtolower($reflection->getName());
        $tokens = token_get_all("<"."?php $source");
        for($i = 0; isset($tokens[$i]); ++$i)
            if(is_array($tokens[$i]) && ($tokens[$i][0] == T_FN || $tokens[$i][0] == T_FUNCTION)){
                if(is_array($tokens[++$i]) && $tokens[$i][0] == T_WHITESPACE)
                    ++$i;
                if(substr($name, -9) != '{closure}'){
                    if(!is_array($tokens[$i]) || $tokens[$i][0] != T_STRING || strtolower($tokens[$i][1]) != $name){
                        --$i;
                        continue;
                    }
                    if(is_array($tokens[++$i]) && $tokens[$i][0] == T_WHITESPACE)
                        ++$i;
                }
                if($tokens[$i] != '('){
                    --$i;
                    continue;
                }
                Tools::readl($tokens, '(', ')', $i);
                if(is_array($tokens[++$i]) && $tokens[$i][0] == T_WHITESPACE)
                    ++$i;
                if(is_array($tokens[$i]) && $tokens[$i][0] == T_USE){
                    if(is_array($tokens[++$i]) && $tokens[$i][0] == T_WHITESPACE)
                        ++$i;
                    if($tokens[$i] != '('){
                        --$i;
                        continue;
                    }
                    Tools::readl($tokens, '(', ')', $i);
                    if(is_array($tokens[++$i]) && $tokens[$i][0] == T_WHITESPACE)
                        ++$i;
                }
                if($tokens[$i] == ':'){
                    if(is_array($tokens[++$i]) && $tokens[$i][0] == T_WHITESPACE)
                        ++$i;
                    while(is_array($tokens[$i]) && ($tokens[$i][0] == T_STRING || $tokens[$i][0] == T_NS_SEPARATOR || $tokens[$i][0] == T_WHITESPACE))
                        ++$i;
                }
                if(is_array($tokens[$i])){
                    if($tokens[$i][0] != T_DOUBLE_ARROW){
                        --$i;
                        continue;
                    }
                    if(is_array($tokens[++$i]) && $tokens[$i][0] == T_WHITESPACE)
                        ++$i;
                    $str = 'return ';
                    for(; isset($tokens[$i]); ++$i)
                        if(in_array($tokens[$i], [')', ']', '}', ',', ';']))break;
                        elseif(is_array($tokens[$i]) && $tokens[$i][0] == T_CLOSE_TAG)break;
                        elseif(is_array($tokens[$i]))
                            $str.= $tokens[$i][1];
                        else
                            $str.= $tokens[$i];
                    $str.= ';';
                    return $str;
                }else{
                    if($tokens[$i] != '{'){
                        --$i;
                        continue;
                    }
                    $str = Tools::readl($tokens, '{', '}', $i);
                    $str = trim(substr($str, 1, -1));
                    return $str;
                }
            }
        return '';
    }

    /**
     * Get callable vars
     * 
     * @static
     * @method getStaticVars
     * @param callable $callable
     * @return array
     */
    public static function getStaticVars($callable){
        $reflection = self::getReflection($callable);
        if(!$reflection)
            return array();
        $vars = $reflection->getStaticVariables();
        return $vars;
    }

    /**
     * Serialize vars into code
     * 
     * @static
     * @method serializeVars
     * @param array $vars
     * @return string serialized vars
     */
    public static function serializeVars(array $vars){
        $serialize = '';
        foreach($vars as $var => $val){
            if(is_callable($val)){
                $serialize .= '$'.$var.'=function(';
                $callable = self::getReflection($val);
                $params = $callable->getParameters();
                $defaults = '';
                if(isset($params[0])){
                    foreach($params as $param){
                        if($param->hasType())
                            $serialize .= $param->getType().' ';
                        if($param->isVariadic())
                            $serialize .= '...';
                        if($param->isPassedByReference())
                            $serialize .= '&';
                        $serialize .= '$'.$param->getName();
                        if($param->isOptional()){
                            $serialize .= '=null';
                            $defaults .= 'if($'.$param->getName().'===null)';
                            $defaults .= '$'.$param->getName()."=unserialize('".Tools::singlequote(serialize($param->getDefaultValue()))."');";
                        }
                        $serialize .= ',';
                    }
                    $serialize = substr($serialize, 0, -1);
                }
                $serialize .= '){';
                $defaults .= self::serializeVars($callable->getStaticVariables());
                $serialize .= $defaults . self::inner($callable) . '};';
            }else{
                $serialize .= '$'.$var."=unserialize('".Tools::singlequote(serialize($val))."');";
            }
        }
        return $serialize;
    }

    /**
     * Get callable parameters
     * 
     * @static
     * @method getParams
     * @param callable $callable
     * @return array
     */
    public static function getParams($callable){
        $reflection = self::getReflection($callable);
        if(!$reflection)
            return array();
        $params = $reflection->getParameters();
        foreach($params as &$param)
            $param = $param->getName();
        return $params;
    }

    /**
     * Serialize callable code
     * 
     * @static
     * @method serialize
     * @param callable $callable
     * @param array $params = []
     * @return string
     */
    public static function serialize($callable, array $params = array()){
        $callable = self::getReflection($callable);
        $args = self::getParams($callable);
        $vars = self::getStaticVars($callable);
        $inner = self::inner($callable);
        for($i = 0; isset($args[$i]) && isset($params[$i]); ++$i){
            $vars[$args[$i]] = $params[$i];
        }
        $code = self::serializeVars($vars) . $inner;
        return $code;
    }
}

?>