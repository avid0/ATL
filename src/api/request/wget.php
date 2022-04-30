<?php
/**
 * @author Avid
 * 
 * WGet API Request Method
 */
namespace ATL\API\Request;
use \ATL\Logger;

class WGet {
    /**
     * Bot API Token
     * @var string $token
     */
    public $token;

    /**
     * Last error code
     * @var int $last_error_code
     */
    public $last_error_code = 200;

    /**
     * Last error message
     * @var string $last_error_message
     */
    public $last_error_error = '';

    /**
     * WGet Method initializer
     * 
     * @method __construct
     * @param string $token
     */
    public function __construct(string $token){
        $this->token = $token;
    }

    /**
     * WGet request
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return object response->result or false
     */
    public function request(string $method, array $datas = []){
        $url = "http://api.telegram.org/bot{$this->token}/$method?".http_build_query($datas);
        $res = file_get_contents($url);
        if(!$res){
            Logger::log("ATL\API\Request\WGet::request(): Network connection error");
            return false;
        }
        $res = json_decode($res);
        if(!isset($res->ok) || !$res->ok){
            Logger::log("ATL\API\Request\WGet::request(): API error: {$res->description}");
            $this->last_error_code = $res->error_code;
            $this->last_error_message = $res->description;
            return false;
        }
        $this->last_error_code = 200;
        $this->last_error_message = '';
        return $res->result;
    }

    /**
     * WGet read file
     * 
     * @method file
     * @param string $path
     * @return string or false
     */
    public function file(string $path){
        $url = "https://api.telegram.org/file/bot{$this->token}/$path";
        $res = file_get_contents($url);
        if(!$res){
            Logger::log("ATL\API\Request\WGet::file(): Network connection error");
            return false;
        }
        if($res == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            Logger::log("ATL\API\Request\WGet::file(): File not found");
            return false;
        }
        return $res;
    }

    /**
     * WGet download file
     * 
     * @method file
     * @param string $path
     * @param string $into
     * @return bool
     */
    public function download(string $path, string $into){
        $url = "https://api.telegram.org/file/bot{$this->token}/$path";
        $read = fopen($url, 'rb');
        if(!$read){
            Logger::log("ATL\API\Request\WGet::download(): Network connection error");
            return false;
        }
        $write = fopen($into, 'wb');
        if(!$write){
            Logger::log("ATL\API\Request\WGet::download(): Dest path is not writable");
            return false;
        }
        $packet = fread($read, 1024);
        if($packet == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            Logger::log("ATL\API\Request\WGet::download(): File not found");
            return false;
        }
        fwrite($write, $packet);
        stream_copy_to_stream($read, $write);
        fclose($read);
        fclose($write);
        return true;
    }
}
?>