<?php
/**
 * @author Avid
 * 
 * Bot API Alive connection
 */
namespace ATL\API\Request;
use \ATL\Logger;

class Alive {
    /**
     * Bot API Token
     * @var string $token
     */
    public $token;

    /**
     * API Conenction
     * @var resource $connection
     */
    public $connection;

    /**
     * Contents was readed
     * @var bool $readed
     */
    public $readed = true;
    
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
     * Socket Method initializer
     * 
     * @method __constructor
     * @param string $token
     */
    public function __construct(string $token){
        $this->token = $token;
        $this->reconnect();
    }

    /**
     * Remake API Connection
     * 
     * @method reconnect
     * @return bool
     */
    public function reconnect(){
        $this->close();
        $this->connection = fsockopen('tls://api.telegram.org', 443);
        if(!$this->connection){
            Logger::log("ATL\API\Request\Alive::reconnect(): Network connection error");
            return false;
        }
        return true;
    }

    /**
     * Close API Connection
     * 
     * @method close
     */
    public function close(){
        if($this->connection)
            fclose($this->connection);
        $this->connection = null;
    }

    /**
     * Send request
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return bool
     */
    public function request(string $method, array $datas = []){
        if(!$this->readed){
            $this->read();
        }
        $datas = json_encode($datas);
        $len = strlen($datas);
        $path = "/bot{$this->token}/$method";
        $packet = "POST $path HTTP/1.1\r\n";
        $packet.= "Host: api.telegram.org\r\n";
        $packet.= "Content-Type: application/json\r\n";
        $packet.= "Content-Length: $len\r\n";
        $packet.= "\r\n$datas";
        $len = fwrite($this->connection, $packet);
        if($len == 0){
            Logger::log("ATL\API\Request\Alive::request(): Network writing blocked");
            $this->close();
            return false;
        }
        $this->readed = false;
        return true;
    }

    /**
     * Read output contents
     * 
     * @method read
     * @return object response->result or false
     */
    public function read(){
        if($this->readed){
            Logger::log("ATL\API\Request\Alive::read(): Output was readed from before");
            return false;
        }
        $this->readed = true;
        $res = stream_get_contents($this->connection);
        $res = explode("\r\n\r\n", $res, 2);
        if(!isset($res[1])){
            Logger::log("ATL\API\Request\Alive::read(): Network reading blocked");
            $this->close();
            return false;
        }
        $res = json_decode($res[1]);
        if(!isset($res->ok) || !$res->ok){
            Logger::log("ATL\API\Request\Alive::read(): API error: {$res->description}");
            $this->last_error_code = $res->error_code;
            $this->last_error_message = $res->description;
            return false;
        }
        $this->last_error_code = 200;
        $this->last_error_message = '';
    }

    // Unserialization method
    public function __unserialize(array $data){
        foreach($data as $key => $value)
            $this->$key = $value;
        $this->reconnect();
    }

    /**
     * Socket read file
     * 
     * @method file
     * @param string $path
     * @return string or false
     */
    public function file(string $path){
        if(!$this->readed){
            $this->read();
        }
        $path = "/file/bot{$this->token}/$path";
        $packet = "POST $path HTTP/1.1\r\n";
        $packet.= "Host: api.telegram.org\r\n";
        $packet.= "Content-Type: application/json\r\n";
        $packet.= "Content-Length: 0\r\n";
        $packet.= "\r\n";
        $len = fwrite($this->connection, $packet);
        if($len == 0){
            Logger::log("ATL\API\Request\Alive::file(): Network writing blocked");
            $this->close();
            return false;
        }
        $res = stream_get_contents($this->connection);
        $res = explode("\r\n\r\n", $res, 2);
        $res = $res[1];
        if(!$res){
            Logger::log("ATL\API\Request\Alive::file(): Network connection error");
            return false;
        }
        if($res == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            $this->reportWarning("ATL\API\Request\Alive::file(): File not found");
            return false;
        }
        return $res;
    }
}
?>