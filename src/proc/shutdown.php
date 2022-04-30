<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Proc;
use \ATL\Logger;

class Shutdown {
    /**
     * @static
     * @var array $callabes = []
     */
    public $callables = array();

    /**
     * @var callable $callable
     * @var array $params = []
     */
    public $callable, $params = array();

    /**
     * Constructor
     * 
     * @method __construct
     * @param callable $callable
     * @param array $params = []
     */
    public function __construct($callable, array $params = array()){
        if(!is_callable($callable)){
            Logger::log("\ATL\Proc\Shutdown(): Expects parameter 1 to be callable");
            return;
        }
        $this->callable = $callable;
        $this->params = $params;
    }

    /**
     * Destructor (run callable)
     * 
     * @method __destruct
     */
    public function __destruct(){
        if($this->callable){
            $callable = $this->callable;
            $params = $this->params;
            $this->params = $this->callable = null;
            $callable(...$params);
        }
    }

    /**
     * @method register
     * @param callable $callable
     * @param array $params = []
     * @return int $id or false
     */
    public function register($callable, array $params = array()){
        if(!is_callable($callable)){
            Logger::log("\ATL\Proc\Shutdown::register(): Expects parameter 1 to be callable");
            return false;
        }
        $id = count($this->callables);
        $this->callables[$id] = new \ATL\Proc\Shutdown($callable, $params);
        return $id;
    }

    /**
     * @method unregister
     * @param int $id
     * @return bool
     */
    public function unregister(int $id){
        if(!isset($this->callables[$id]))
            return false;
        $this->callables[$id]->callable = null;
        unset($this->callalbes[$id]);
        return true;
    }

    /**
     * @method reset
     */
    public function reset(){
        for($id = 0; isset($this->callables[$id]); ++$id)
            $this->unregister($id);
    }
}
?>