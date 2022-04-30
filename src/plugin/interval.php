<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;
use \ATL\Logger;

class Interval {
    /**
     * @var \ATL $atl
     */
    public $atl;

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
     * Make internal
     * 
     * @method make
     * @param string $id
     * @return bool
     */
    public function make(string $id){
        $intervals = $this->atl->general->getPlug('intervals');
        if(!$intervals || !is_array($intervals))
            $intervals = array();
        if(!isset($intervals[$id]))
            $intervals[$id] = array(0, 0, false);
        else
            return true;
        return $this->atl->general->setPlug('intervals', $intervals);
    }

    /**
     * Get interval
     * 
     * @method get
     * @param string $id
     * @return bool
     */
    public function get(string $id){
        $intervals = $this->atl->general->getPlug('intervals');
        if(!$intervals || !is_array($intervals) || !isset($intervals[$id]))
            return false;
        $interval = $intervals[$id];
        return [
            'pid' => $interval[0],
            'time' => $interval[1],
            'close' => $interval[2]
        ];
    }
    
    /**
     * Close interval
     * 
     * @method close
     * @param string $id
     * @return bool
     */
    public function close(string $id){
        $intervals = $this->atl->general->getPlug('intervals');
        if(!$intervals || !is_array($intervals))
            $intervals = array();
        elseif(isset($intervals[$id]))
            unset($intervals[$id]);
        else
            return true;
        return $this->atl->general->setPlug('intervals', $intervals);
    }
    
    /**
     * Check closed interval
     * 
     * @method closed
     * @param string $id
     * @return bool
     */
    public function closed(string $id){
        return !$this->get($id);
    }

    /**
     * Update interval
     * 
     * @method update
     * @param string $id
     * @param int $pid = current pid
     * @return bool
     */
    public function update(string $id, int $pid = null){
        if($pid === null)
            $pid = getmypid();
        $intervals = $this->atl->general->getPlug('intervals');
        if(!$intervals || !is_array($intervals) || !isset($intervals[$id]))
            return false;
        $intervals[$id][0] = $pid;
        $intervals[$id][1] = microtime(true);
        return $this->atl->general->setPlug('intervals', $intervals);
    }

    /**
     * Check interval
     * 
     * @method running
     * @param string $id
     * @param int $interval
     * @return bool
     */
    public function running(string $id, int $interval){
        $ocur = $this->get($id);
        if(!$ocur)
            return true;
        if(strpos(ini_get('disable_functions'), 'posix_kill') !== false)
            return posix_kill($ocur['pid'], 0);
        if(microtime(true) - $ocur['time'] > 2 * $interval / 1000000)
            return false;
        return true;
    }

    /**
     * Run interval
     * 
     * @method run
     * @param string $id
     * @param callable $callable
     * @param int $interval = 1000000 microseconds
     * @param callable $onetime = null
     * @param array $params += unshift [\ATL $atl] ["var" => $content]
     * @param int $method = AUTO
     * @param string $cwd = null
     * @return object or bool
     */
    public function run(string $id, $callable, int $interval = 1000000, $onetime = null, array $params = array(), int $method = 0, string $cwd = null){
        if(!is_callable($callable)){
            Logger::log("\ATL\Plugin\Interval::run(): Expects parameter 1 to be callable");
            return false;
        }
        $this->make($id);
        if($this->running($id, $interval))
            return true;
        $atl = $this->atl;
        return $this->atl->proc(function()use($atl, $id, $callable, $onetime, $interval, $params){
            set_time_limit(0);
            if($onetime)
                $onetime($atl, ...$params);
            while(true){
                if($atl->interval->closed($id))
                    die;
                $atl->interval->update($id);
                $callable($atl, ...$params);
                usleep($interval);
            }
        }, array(), $method, $cwd);
    }
}

?>