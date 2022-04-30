<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class ForceJoin {
    /**
     * @var \ATL $atl
     */
    public $atl;

    /**
     * @var array $channels = []
     * @var int $cache_time = 60 Seconds
     */
    public $channels = array();
    public $cache_time = 60;

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
        if((!$type || $type == 'private') && !$id && $this->atl->user){
            $type = 'private';
            $id = $this->atl->answer->uid;
        }elseif(!$type){
            if($this->atl->chat)
                $type = $this->atl->chat->type;
            else
                $type = 'private';
        }
        if(!$id){
            $id = $this->atl->whereAnswers();
        }
    }

    /**
     * @method add
     * @param string ...$channels
     */
    public function add(string ...$channels){
        foreach($channels as $channel){
            if(strpos($channel, '@') !== 0 && !is_numeric($channel))
                $channel = '@'.$channel;
            $this->channels[] = $channel;
        }
    }

    /**
     * @method has
     * @param int $id = user answering
     * @param string $type = user answering
     * @return bool
     */
    public function has(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $forcejoin = $this->getPlug($id, $type, 'forcejoin');
        if(!$forcejoin){
            $forcejoin = array();
        }
        foreach($this->channels as $channel){
            if(!isset($forcejoin[$channel])){
                $forcejoin[$channel] = 0;
            }
            $now = microtime(true);
            if($forcejoin[$channel] + $this->cache <= $now){
                $status = $this->atl->getChatMember($channel, $id);
                if(!$status)
                    return false;
                $status = $status->status;
                if(!in_array($status, array('creator', 'administrator', 'member')))
                    return false;
                $forcejoin[$channel] = $now;
            }
        }
        $this->setPlug($id, $type, 'forcejoin', $forcejoin);
        return true;
    }
}
?>