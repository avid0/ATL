<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class Blocking {
    /**
     * @var string $message
     */
    public $message;

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
        if($this->message && $this->has()){
            $atl->answerMessage($this->message);
            die;
        }
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
            $id = $this->atl->whereAnswers();
        }
    }

    /**
     * @method block
     * @param int $id = answering
     * @param string $type = answering
     * @return bool
     */
    public function block(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return $this->atl->config->setPlug($id, $type, 'block', '1');
    }

    /**
     * @method unblock
     * @param int $id = answering
     * @param string $type = answering
     * @return bool
     */
    public function unblock(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return $this->atl->config->setPlug($id, $type, 'block', '');
    }
    
    /**
     * @method has
     * @param int $id = answering
     * @param string $type = answering
     * @return bool
     */
    public function has(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return (bool)$this->atl->config->getPlug($id, $type, 'block');
    }
}
?>