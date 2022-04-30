<?php
/**
 * @author Avid
 * 
 * Webhook Management
 */
namespace ATL\API;
use \ATL\Logger;

class Webhook {
    /**
     * Webhook verifier
     * 
     * @static
     * @method verify
     * @return bool Verify webhook request
     */
    public static function verify(){
        $ip = $_SERVER['REMOTE_ADDR'];
        return (($ip >= "149.154.160.0" && $ip <= "149.154.176.0") || ($ip >= "91.108.4.0" && $ip <= "91.108.8.0"))
            && ($_SERVER['HTTPS'] == "on" || $_SERVER['SERVER_PORT'] == 443);
    }

    /**
     * Get webhook update
     * 
     * @static
     * @method update
     * @return object Update or false
     */
    public static function update(){
        $res = file_get_contents('php://input');
        if(!$res)return false;
        $res = json_decode($res);
        if(!$res)return false;
        return $res;
    }

    /**
     * Close client webhook connection
     * @static
     * @method close
     * @param string $message = ''
     * @return bool
     */
    public static function close(string $message = ''){
        if(!self::verify()){
            Logger::log("ATL\API\Webhook::close(): The webhook request is invalid");
            return false;
        }
        if(headers_sent()){
            Logger::log("ATL\API\Webhook::close(): Headers sent from before.");
            return false;
        }
        while(ob_get_level() > 0)
            ob_end_clean();
        header('Connection: close');
        ignore_user_abort(true);
        ob_start();
        print $message;
        $size = ob_get_length();
        header("Content-Length: $size");
        header('Content-Type: application/json');
        ob_end_flush();
        flush();
        if(function_exists('litespeed_finish_request'))
            litespeed_finish_request();
        if(function_exists('fastcgi_finish_request'))
            fastcgi_finish_request();
        return true;
    }

    /**
     * Check if webhook connection was closed
     * 
     * @static
     * @method closed
     * @return bool
     */
    public static function closed(){
        return headers_sent();
    }
}
?>