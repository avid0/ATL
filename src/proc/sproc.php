<?php
/**
 * @author Avid
 * 
 * Multiprocess proc method
 */
namespace ATL\Proc;
use \ATL\Logger;
use \ATL\Serialization\Tools;
use \ATL\Serialization\Closure;

class SProc {
    /**
     * Process resource
     * @var resource $proc
     */
    public $proc;

    /**
     * Process write pipe
     * @var resource $write
     */
    public $write;

    /**
     * Process read pipe
     * @var resource $read
     */
    public $read;

    /**
     * Support proc method
     * 
     * @static
     * @method support
     * @return bool
     */
    public static function support(){
        if(strpos(ini_get('disable_functions'), 'proc_open') !== false)
            return false;
        return true;
    }

    /**
     * Constructor
     * 
     * @method open
     * @param callable $callable
     * @param array $params = [] ["var" => $content]
     * @param string $cwd = null
     * @param array $env = []
     */
    public function __construct($callable, array $params = array(), string $cwd = null, array $env = null){
        $this->open($callable, $params, $cwd, $env);
    }

    /**
     * Open process
     * 
     * @method open
     * @param callable $callable
     * @param array $params = [] ["var" => $content]
     * @param string $cwd = null
     * @param array $env = []
     * @return int
     */
    public function open($callable, array $params = array(), string $cwd = null, array $env = null){
        if(!is_callable($callable) && !in_array($callable, array('exit', 'die', ''))){
            Logger::log("ATL\Proc\SProc::open(): The parameter 1 must be an callable, ".gettype($callable)." given");
            return false;
        }
        $add = '<?php include("'.dirname(__DIR__).'/autoload.php");';
        $callable = $add . Closure::serialize($callable, $params) . ';';
        $spec = array(
            array("pipe", "r"),
            array("pipe", "w")
        );
        $exe = \ATL\Serialization\Tools::phpexe();
        if($env)
            $proc = proc_open($exe, $spec, $pipes, $cwd, $env);
        elseif($cwd)
            $proc = proc_open($exe, $spec, $pipes, $cwd);
        else
            $proc = proc_open($exe, $spec, $pipes);
        if(!$proc){
            Logger::log("ATL\Proc\SProc::open(): Failed to create php process");
            return false;
        }
        $this->proc = $proc;
        list($this->write, $this->read) = $pipes;
        return fwrite($this->write, $callable);
    }

    /**
     * Run an another script
     * 
     * @method run
     * @param callable $callable
     * @param array $params = []
     * @return int
     */
    public function run($callable, $params = array()){
        if(!is_callable($callable) && !in_array($callable, array('exit', 'die', ''))){
            Logger::log("ATL\Proc\SProc::run(): The parameter 1 must be an callable, ".gettype($callable)." given");
            return false;
        }
        if(!$this->proc){
            Logger::log("ATL\Proc\SProc::run(): Process do not exists");
            return false;
        }
        if(!$this->write){
            Logger::log("ATL\Proc\SProc::run(): Write pipe was closed");
            return false;
        }
        $callable = Closure::serialize($callable, $params);
        return fwrite($this->write, $callable.';');
    }

    /**
     * Read process
     * 
     * @method read
     * @param int $length = -1
     * @param int $offset = -1
     * @return string
     */
    public function read(int $length = -1, int $offset = -1){
        if(!$this->proc){
            Logger::log("ATL\Proc\SProc::read(): Process do not exists");
            return false;
        }
        if(!$this->read){
            Logger::log("ATL\Proc\SProc::read(): Read pipe was closed");
            return false;
        }
        return stream_get_contents($this->read, $length, $offset);
    }

    /**
     * Close pipes
     * @method closePipes
     */
    public function closePipes(){
        if($this->read){
            fclose($this->read);
            $this->read = null;
        }
        if($this->write){
            fclose($this->write);
            $this->write = null;
        }
    }

    /**
     * Close process
     * @method close
     */
    public function close(){
        $this->closePipes();
        if($this->proc){
            proc_close($this->proc);
            $this->proc = null;
        }
    }

    /**
     * Terminate process
     * 
     * @method terminate
     * @param int $signal = 15
     * @return bool
     */
    public function terminate(int $signal = 15){
        if(!$this->proc){
            Logger::log("ATL\Proc\SProc::terminate(): Process do not exists");
            return false;
        }
        return proc_terminate($this->proc, $signal);
    }

    /**
     * Set end exit callable
     * 
     * @method endExit
     * @return int
     */
    public function endExit(){
        if(!$this->proc){
            Logger::log("ATL\Proc\SProc::endExit(): Process do not exists");
            return false;
        }
        if(!$this->write){
            Logger::log("ATL\Proc\SProc::endExit(): Write pipe was closed");
            return false;
        }
        return fwrite($this->write, "exit;");
    }
}
?>