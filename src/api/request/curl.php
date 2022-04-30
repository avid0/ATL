<?php
/**
 * @author Avid
 * 
 * CURL API Request Method
 */
namespace ATL\API\Request;
use \ATL\Logger;

class CURL {
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
     * Proxy address
     * @var string $proxy_address
     */
    public $proxy_address;

    /**
     * Proxy auth
     * @var $proxy_auth = PROXY_HTTP (default)
     */
    public $proxy_auth = 0;

    /**
     * Proxy type
     * @var int $proxy_type
     */
    public $proxy_type;

    /**
     * CURL Method initializer
     * 
     * @method __construct
     * @param string $token
     */
    public function __construct(string $token){
        $this->token = $token;
    }

    /**
     * CURL handle proxy init
     * 
     * @method initProxy
     * @param resource $ch
     */
    private function initProxy($ch){
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy_address);
        if($this->proxy_auth)
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy_auth);
        if(is_int($this->proxy_type))
            curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxy_type);
    }

    /**
     * CURL request
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return object response->result or false
     */
    public function request(string $method, array $datas = []){
        $url = "https://api.telegram.org/bot{$this->token}/$method";
        $ch = curl_init($url);
        if($this->proxy_address)
            $this->initProxy($ch);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        $res = curl_exec($ch);
        if(!$res){
            Logger::log("ATL\API\Request\CURL::request(): Network connection error: ".curl_error($ch));
            return false;
        }
        curl_close($ch);
        $res = json_decode($res);
        if(!isset($res->ok) || !$res->ok){
            Logger::log("ATL\API\Request\CURL::request(): API error: {$res->description}");
            $this->last_error_code = $res->error_code;
            $this->last_error_message = $res->description;
            return false;
        }
        $this->last_error_code = 200;
        $this->last_error_message = '';
        return $res->result;
    }

    /**
     * CURL read file
     * 
     * @method file
     * @param string $path
     * @return string or false
     */
    public function file(string $path){
        $url = "https://api.telegram.org/file/bot{$this->token}/$path";
        $ch = curl_init($url);
        if($this->proxy_address)
            $this->initProxy($ch);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        if(!$res){
            Logger::log("ATL\API\Request\CURL::file(): Network connection error");
            return false;
        }
        if($res == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            $this->reportWarning("ATL\API\Request\CURL::file(): File not found");
            return false;
        }
        return $res;
    }
}
?>