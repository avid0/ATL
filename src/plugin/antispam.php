<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;
use \ATL\Logger;

class AntiSpam {
    /**
     * @var \ATL $atl
     * @var int $limit = 10
     * @var int $period = 10
     * @var int $timeout = 5
     */
    public $atl;
    public $limit = 10;
    public $period = 10;
    public $timeout = 5;

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
     * Set default id and type
     * 
     * @internal
     * @method setDefault
     * @param int &$id = null
     * @param string &$type = null
     */
    private function setDefault(&$id = null, &$type = null){
        if(!$type){
            if($this->atl->chat)
                $type = $this->atl->chat->type;
            else
                $type = 'private';
        }
        if(!$id){
            ++Logger::$internal; // For directly AntiSpam usage, check spams do not give any log for non message updates
            $id = $this->atl->whereAnswers();
            --Logger::$internal;
        }
    }

    /**
     * @method check
     * @param int $id = answering
     * @param string $type = answering
     * @return bool spamming or not
     */
    public function check(int $id = null, string $type = null){
        if($this->timeout && $this->atl->answer->date)
            if($this->atl->answer->date + $this->timeout < time())
                return true;
        $this->setDefault($id, $type);
        if(!$id || !$type)
            return false;
        $msgs = $this->atl->config->getPlug($id, $type, 'antispam');
        if(!$msgs)
            $msgs = array();
        $now = microtime(true);
        $msgs = array_filter($msgs, function($msg)use($now){
            return $msg + $this->period >= $now;
        });
        $msgs[] = $now;
        $this->atl->config->setPlug($id, $type, 'antispam', $msgs);
        return count($msgs) >= $this->limit;
    }
}
?>