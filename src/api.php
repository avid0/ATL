<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * Telegram API
 */
namespace ATL;
use \ATL\Logger;

class API {
    /**
     * Request method constants
     * @var int WGET = 1
     * @var int CURL = 2
     * @var int SOCKET = 3
     * @var int WEBHOOKRETURN = 5
     * @var int ALIVE = 6
     */
    const WGET = 1;
    const CURL = 2;
    const SOCKET = 3;
    const WEBHOOKRETURN = 5;
    const ALIVE = 6;

    /**
     * Connection method
     * @var int $method = 0 (closed)
     */
    public $method = 0;

    /**
     * Connection object
     * @var object $connection
     */
    public $connection;

    /**
     * Tmp
     * @var array $tmp = []
     */
    public $tmp = array();

    /**
     * Constructor (open api connection)
     * 
     * @method __construct
     * @param string $token = null Default do not connect
     * @param int $request = CURL
     */
    public function __construct(string $token = null, int $request = 2){
        if($token)
            $this->connect($token, $request);
    }

    /**
     * Open API connection
     * 
     * @method connect
     * @return bool
     * 
     * @param string $token
     * @param int $request = CURL
     * or
     * @param object $request
     */
    public function connect($token, int $request = 2){
        if(is_object($token)){
            if($this->method == self::SOCKET)
                $this->tmp[] = $this->connection;
            if($token instanceof \ATL\API\Request\WGet){
                $this->method = self::WGET;
            }elseif($token instanceof \ATL\API\Request\CURL){
                $this->method = self::CURL;
            }elseif($token instanceof \ATL\API\Request\Socket){
                $this->method = self::SOCKET;
            }elseif($token instanceof \ATL\API\Request\WebhookReturn){
                $this->method = self::WEBHOOKRETURN;
            }elseif($token instanceof \ATL\API\Request\Alive){
                $this->method = self::ALIVE;
            }else{
                Logger::log("\ATL\API\connection(): Invalid API Request object");
                return false;
            }
            $this->connection = $token;
            $token = $this->connection->token;
        }else{
            if(!preg_match('/[0-9]{4,20}:AA[GFHE][a-zA-Z0-9-_]{32}/', (string)$token)){
                Logger::log("\ATL\API\connect(): Invalid telegram bot token format");
                return false;
            }
        }
        switch($request){
            case self::WGET:
                $this->connection = new \ATL\API\Request\WGet($token);
            break;
            case self::CURL:
                $this->connection = new \ATL\API\Request\CURL($token);
            break;
            case self::SOCKET:
                $this->connection = new \ATL\API\Request\Socket($token);
            break;
            case self::WEBHOOKRETURN:
                $this->connection = new \ATL\API\Request\WebhookReturn($token);
            break;
            case self::ALIVE:
                $this->connection = new \ATL\API\Request\Alive($token);
            break;
            default:
                Logger::log("ATL\API::connect(): Request method is invalid");
                return false;
        }
        $this->method = $request;
        return true;
    }

    /**
     * Close API connection
     * 
     * @method close
     * @return bool
     */
    public function close(){
        if(!$this->connection || $this->method == 0){
            Logger::log("ATL\API::close(): Connection do not opened");
            return false;
        }
        switch($this->method){
            case self::SOCKET:
            case self::ALIVE:
                $this->connection->close();
            break;
        }
        $this->connection = null;
        $this->method = 0;
        return true;
    }

    /**
     * Wait API Connections
     *
     * @method wait
     * @return bool Connection do support Wait method
     */
    public function wait(){
        switch($this->method){
            case self::SOCKET:
                $this->connection->wait();
            break;
            default:
                return false;
        }
        return true;
    }

    /**
     * Alive reconnection
     * 
     * @method reconnect
     * @return bool Connection do support Reconnection method or Reconnection was success
     */
    public function reconnect(){
        switch($this->method){
            case self::ALIVE:
                return $this->connection->reconnect();
            default:
                return false;
        }
    }

    /**
     * Read alive output contents
     * 
     * @method read
     * @return object response->result or false
     */
    public function read(){
        switch($this->method){
            case self::ALIVE:
                return $this->connection->read();
            default:
                return false;
        }
    }

    /**
     * Request to telegram api
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @param int $request = default
     * @return object
     */
    public function request(string $method, array $datas = [], int $request = 0){
        if($request == 0){
            if($this->method == 0){
                Logger::log("ATL\API::request(): Connection do not opened");
                return false;
            }
            return $this->connection->request($method, $datas);
        }
        $connection = $this->connection;
        $this->connect($this->connection->token, $request);
        $result = $this->connection->request($method, $datas);
        $this->connect($connection);
        return $result;
    }

    /**
     * Read file
     * 
     * @method file
     * @param string $path
     * @param int $request = default
     * @return string or false
     */
    public function file(string $path, int $request = 0){
        if($request == self::WEBHOOKRETURN || ($request == 0 && $this->method == self::WEBHOOKRETURN)){
            Logger::log("ATL\API::file(): The request method WebhookReturn can not read file");
            return false;
        }
        if($request == 0){
            if($this->method == 0){
                Logger::log("ATL\API::file(): Connection do not opened");
                return false;
            }
            return $this->connection->request($method, $datas);
        }
        $connection = $this->connection;
        $this->connect($this->connection->token, $request);
        $result = $this->connection->file($path);
        $this->connect($connection);
        return $result;
    }
    
    /**
     * Download file
     * 
     * @method download
     * @param string $path
     * @param string $into
     * @return bool
     */
    public function download(string $path, string $into){
        if($this->method != self::WGET){
            $connection = $this->connection;
            $this->connect($this->connection->token, self::WGET);
            $result = $this->connection->download($path, $into);
            $this->connect($connection);
        }else{
            $result = $this->connection->download($path, $into);
        }
        return $result;
    }

    /**
     * Multi request to telegram api
     * 
     * @method multirequest
     * @param array $requests Array of [string $method, array $params = []]
     * @param int $request = 0
     * @param int $limit = 30 Maximum requests per seconds (sleep between requests)
     * @return array of result objects
     */
    public function multirequest(array $requests, int $request = 0, int $limit = 30){
        if($this->method == 0){
            Logger::log("ATL\API::multirequest(): Connection do not opened");
            return false;
        }
        if($request != 0){
            $connection = $this->connection;
            $this->connect($this->connection->token, $request);
        }
        if($this->method == self::WEBHOOKRETURN && isset($requests[1])){
            Logger::log("ATL\API::multirequest(): The request method WebhookReturn can not send multirequests");
            return false;
        }
        $result = [];
        $time = microtime(true);
        for($i = 0; isset($requests[$i]); ++$i){
            if(!isset($requests[$i]['method']))
                continue;
            $method = $requests[$i]['method'];
            unset($requests[$i]['method']);
            $runtime = microtime(true) - $time;
            if($runtime * $limit <= $i){
                usleep(floor(($i / $limit - $runtime) * 1e6));
            }
            $result[] = $this->connection->request($method, $requests[$i]);
        }
        if($request != 0){
            $this->connect($connection);
        }
        return $result;
    }
}
?>