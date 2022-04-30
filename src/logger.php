<?php
/**
 * @author Avid
 * 
 * Trigger logger
 */
namespace ATL;

class Logger {
    /**
     * @var int $internal
     */
    public static $internal = 0;

    /**
     * log reporter
     * 
     * @static
     * @method log
     * @param string $message
     * @param int $level
     */
    public static function log($message, $level = E_USER_WARNING){
        if(!(error_reporting() & $level) || self::$internal > 0)
            return;
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        for($i = 0; strpos($trace[$i]['file'], __DIR__) !== false; ++$i);
        $trace = $trace[$i];
        $message = "$message in {$trace['file']} on line {$trace['line']}";
        switch($level){
            case E_USER_ERROR:
                $out = "PHP Error:  $message\n";
                $log = "\nError: $message\n";
            break;
            case E_USER_WARNING:
                $out = "PHP Warning:  $message\n";
                $log = "\nWarning: $message\n";
            break;
            case E_USER_NOTICE:
                $out = "PHP Notice:  $message\n";
                $log = "\nNotice: $message\n";
            break;
            case E_USER_DEPRECATED:
                $out = "PHP Deprecated:  $message\n";
                $log = "\nDeprecated: $message\n";
            break;
            default:
                $out = "PHP Warning:  $message\n";
                $log = "\nWarning: $message\n";
            break;
        }
        print $out;
        error_log($log);
    }
}
?>