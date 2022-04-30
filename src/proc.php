<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * Multiprocess manager
 */
namespace ATL;
use \ATL\Logger;
use \ATL\ATLException;
use \ATL\Proc\SProc;
use \ATL\Proc\WProc;

class Proc {
    /**
     * Process methods
     * @var int AUTO = 0
     * @var int PROC = 1
     * @var int WEB = 2
     */
    const AUTO = 0;
    const PROC = 1;
    const WEB = 2;

    /**
     * Process method supports
     * @var bool $proc
     * @var bool $web
     */
    public static $proc;
    public static $web;

    /**
     * Constructor
     * 
     * @static
     * @method run
     * @param callable $callable
     * @param array $params = [] ["var" => $content]
     * @param int $method = AUTO
     * @param string $cwd = null
     * @return object or bool
     * @throws \ATL\ATLException
     */
    public static function run($callable, array $params = array(), int $method = 0, string $cwd = null){
        if(!self::$proc && !self::$web){
            Logger::log("\ATL\Proc: This system do not support any multiprocess method");
            return false;
        }
        if($method == self::AUTO){
            if(self::$proc)
                $method = self::PROC;
            elseif(self::$web)
                $method = self::WEB;
        }
        switch($method){
            case self::PROC:
                return new SProc($callable, $params, $cwd);
            case self::WEB:
                return WProc::run($callable, $params, $cwd);
            default:
                throw new ATLException("Invalid multiprocess method $method");
        }
    }
}

Proc::$proc = SProc::support();
Proc::$web = WProc::support();

?>