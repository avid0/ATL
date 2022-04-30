<?php
/**
 * @author Avid
 * 
 * WGet API Request Method
 */
namespace ATL\API\Request;
use \ATL\Logger;

class Socket {
    /**
     * Bot API Token
     * @var string $token
     */
    public $token;

    /**
     * Resource cache for multi-connection api request
     * @static
     * @var array $socks
     */
    public $socks = array();

    /**
     * Socket Method initializer
     * 
     * @method __construct
     * @param string $token
     */
    public function __construct(string $token){
        $this->token = $token;
    }

    /**
     * Send socket request to api.telegram.org without read result by multi-connection
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return bool Without api response
     */
    public function request(string $method, array $datas = []){
        $datas = json_encode($datas);
        $len = strlen($datas);
        $path = "/bot{$this->token}/$method";
        $sock = fsockopen('tls://api.telegram.org', 443);
        if(!$sock){
            Logger::log("ATL\API\Request\Socket::request(): Network connection error");
            return false;
        }
        $packet = "POST $path HTTP/1.1\r\n";
        $packet.= "Host: api.telegram.org\r\n";
        $packet.= "Content-Type: application/json\r\n";
        $packet.= "Content-Length: $len\r\n";
        $packet.= "\r\n$datas";
        $len = fwrite($sock, $packet);
        if($len == 0){
            Logger::log("ATL\API\Request\Socket::request(): Network writing blocked");
            fclose($sock);
            return false;
        }
        $this->socks[] = $sock;
        return true;
    }

    /**
     * Wait for responsing all connections of multi-connection api request
     * 
     * @method wait
     */
    public function wait(){
        array_map('fgetc', $this->socks);
    }

    /**
     * Close all connections of multi-connection api request
     * 
     * @method close
     */
    public function close(){
        array_map('fclose', $this->socks);
        $this->socks = array();
    }

    /**
     * Socket read file
     * 
     * @method file
     * @param string $path
     * @return string or false
     */
    public function file(string $path){
        $path = "/file/bot{$this->token}/$path";
        $sock = fsockopen('tls://api.telegram.org', 443);
        if(!$sock){
            Logger::log("ATL\API\Request\Socket::file(): Network connection error");
            return false;
        }
        $packet = "POST $path HTTP/1.1\r\n";
        $packet.= "Host: api.telegram.org\r\n";
        $packet.= "Content-Type: application/json\r\n";
        $packet.= "Content-Length: 0\r\n";
        $packet.= "\r\n";
        $len = fwrite($sock, $packet);
        if($len == 0){
            Logger::log("ATL\API\Request\Socket::file(): Network writing blocked");
            fclose($sock);
            return false;
        }
        $res = stream_get_contents($sock);
        fclose($sock);
        $res = explode("\r\n\r\n", $res, 2);
        $res = $res[1];
        if(!$res){
            Logger::log("ATL\API\Request\Socket::file(): Network connection error");
            return false;
        }
        if($res == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            Logger::log("ATL\API\Request\Socket::file(): File not found");
            return false;
        }
        return $res;
    }
    
    /**
     * Socket download file into
     * 
     * @method download
     * @param string $path
     * @param string $into
     * @return string or false
     */
    public function download(string $path, string $into){
        $path = "/file/bot{$this->token}/$path";
        $write = fopen($into, 'wb');
        if(!$write){
            Logger::log("ATL\API\Request\Socket::download(): Dest path is not writable");
            return false;
        }
        $sock = fsockopen('tls://api.telegram.org', 443);
        if(!$sock){
            Logger::log("ATL\API\Request\Socket::download(): Network connection error");
            return false;
        }
        $packet = "GET $path HTTP/1.1\r\n";
        $packet.= "Host: api.telegram.org\r\n";
        $packet.= "Content-Type: application/json\r\n";
        $packet.= "Content-Length: 0\r\n";
        $packet.= "\r\n";
        $len = fwrite($sock, $packet);
        if($len == 0){
            Logger::log("ATL\API\Request\Socket::download(): Network writing blocked");
            fclose($sock);
            return false;
        }
        while($packet = fread($sock, 4096))
            if(strpos($packet, "\r\n\r\n") !== false)
                break;
        if(!$packet){
            Logger::log("\ATL\API\Request\Socker::download(): Network HTTP Protocol response invalid");
            fclose($sock);
            return false;
        }
        $packet = explode("\r\n\r\n", $packet, 2);
        $res = $packet[1] . fread($sock, 64);
        if($res == '{"ok":false,"error_code":404,"description":"Not Found"}'){
            fclose($sock);
            fclose($write);
            Logger::log("ATL\API\Request\Socket::file(): File not found");
            return false;
        }
        fwrite($write, $res);
        if($res == $packet[1]){
            fclose($sock);
            fclose($write);
            return true;
        }
        unset($packet);
        stream_copy_to_stream($sock, $write);
        // while($packet = fread($sock, 4096))
        //     fwrite($write, $packet);
        fclose($sock);
        fclose($write);
        return true;
    }
}
?>