<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class ReadUpdates {
    /**
     * @var \ATL $atl
     * @var bool $running = false
     */
    public $atl;
    public $running = false;

    /**
     * Constructor
     * 
     * @method __construct
     * @param \ATL $atl
     */
    public function __construct(\ATL $atl){
        $this->atl = $atl;
    }

    /**
     * @method readUpdates
     * @param callable $callable
     * @param int $offset = -1
     * @param int $timeout = 0
     * @param array $allowed = null
     * @return bool
     */
    public function start($callable, int $offset = -1, int $timeout = 0, array $allowed = null){
        if(!is_callable($callable)){
            Logger::log("\ATL\Plugin\Broadcast::run(): Expects parameter 2 to be callable");
            return false;
        }
        $this->running = true;
        while($this->running){
            $updates = $this->atl->getUpdates($offset, 100, $timeout, $allowed);
            if(!$updates){
                $this->running = false;
                return false;
            }
            foreach($updates as $update){
                $offset = $update->update_id;
                $this->atl->update($update);
                $callable($this->atl);
                if(!$this->running)
                    break;
            }
        }
        return true;
    }

    /**
     * @method stop
     */
    public function stop(){
        $this->running = false;
    }
}
?>