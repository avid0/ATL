<?php
/**
 * @author Avid
 * 
 * Multiprocess Web method
 */
namespace ATL\Proc;
use \ATL\Logger;
use \ATL\Serialization\Tools;
use \ATL\Serialization\Closure;

class WProc {
    /**
     * Support proc method
     * 
     * @static
     * @method support
     * @return bool
     */
    public static function support(){
        return (bool)getenv('SCRIPT_URI');
    }

    /**
     * Get URI of an cwd file
     * 
     * @static
     * @method geturi
     * @param string $file
     * @return string $uri
     */
    public static function geturi(string $file = ''){
        $uri = getenv('SCRIPT_URI');
        if(!$uri)
            return false;
        $uri = rtrim($uri, '\/');
        $url = basename(getenv('SCRIPT_URI'));
        if(in_array(strtolower($url), array('index.php', 'index.html', 'index.htm', 'index.asp'))){
            $base = basename($uri);
            if($url == $base){
                return "$uri/$file";
            }
        }
        $uri = dirname($uri);
        $uri = rtrim($uri, '\/');
        return $uri."/$file";
    }

    /**
     * Run callable
     * 
     * @static
     * @method run
     * @param callable $callable
     * @param array $params = [] ["var" => $content]
     * @param string $cwduri = null
     */
    public static function run($callable, array $params = array(), string $cwduri = null){
        if(!is_callable($callable) && !in_array($callable, array('exit', 'die', ''))){
            Logger::log("ATL\Proc\Web::run(): The parameter 1 must be an callable, ".gettype($callable)." given");
            return false;
        }
        $file = "ATL.".dechex(rand()).dechex(rand()).".tmp.php";
        if(!$cwduri){
            $cwduri = self::geturi();
        }else{
            $cwduri = rtrim($cwduri, '\/').'/';
        }
        $add = '<?php unlink(__FILE__);include("'.dirname(__DIR__).'/autoload.php");\ATL\API\Webhook::close(1);';
        $callable = $add . Closure::serialize($callable, $params);
        $callable.= " ?>";
        $cwduri.= $file;
        file_put_contents("./$file", $callable);
        $res = file_get_contents($cwduri);
        unlink("./$file");
        if(!$res){
            Logger::log("ATL\Proc\Web::run(): localhost connection failed. Maybe invalid CWDURI Parameter initialized");
            return false;
        }
        return true;
    }
}
?>