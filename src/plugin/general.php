<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class General {
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
        if(!$this->atl->config->has(1, 'general'))
            $this->atl->config->insert(1, 'general');
    }

    /**
     * set
     * 
     * @method set
     * @param string $index
     * @param mixed $contents
     * @return bool
     */
    public function set(string $index, $contents){
        return $this->atl->temp->set($index, $contents, 1, 'general');
    }

    /**
     * has
     * 
     * @method has
     * @param string $index
     * @return bool
     */
    public function has(string $index){
        return $this->atl->temp->has($index, 1, 'general');
    }

    /**
     * get
     * 
     * @method get
     * @param string $index
     * @return mixed
     */
    public function get(string $index){
        return $this->atl->temp->get($index, 1, 'general');
    }

    /**
     * setPlug
     * 
     * @method setPlug
     * @param string $index
     * @param mixed $contents
     * @return bool
     */
    public function setPlug(string $index, $contents){
        return $this->atl->config->setPlug(1, 'general', $index, $contents);
    }

    /**
     * getPlug
     * 
     * @method getPlug
     * @param string $index
     * @return mixed
     */
    public function getPlug(string $index){
        return $this->atl->config->getPlug(1, 'general', $index);
    }
}
?>