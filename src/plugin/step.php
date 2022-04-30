<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class Step {
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
     * Set user step
     * 
     * @method set
     * @param string $step
     * @param int $id = answering
     * @param string $type = $this->atl->chat->type or private
     * @return bool
     */
    public function set(string $step, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return $this->atl->config->setPlug($id, $type, 'step', $step);
    }

    /**
     * Get user step
     * 
     * @method get
     * @param int $id = answering
     * @param string $type = $this->atl->chat->type or private
     * @return string or false
     */
    public function get(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return $this->atl->config->getPlug($id, $type, 'step');
    }

    /**
     * @method has
     * @param string $step
     * @param int $id = answering
     * @param string $type = answering
     * @return bool
     */
    public function has(string $step, int $id = null, string $type = null){
        return $this->get($id, $type) == $step;
    }

    /**
     * @method prompt
     * @param string $step
     * @param int $id = answering
     * @param string $type = answering
     * @return bool
     */
    public function prompt(string $step, int $id = null, string $type = null){
        if($this->get($id, $type) == $step)
            return true;
        $this->set($step, $id, $type);
        return false;
    }
}
?>