<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class Temp {
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
     * @method get
     * @param string $index
     * @param int $id = null
     * @param string $type = null
     * @return mixed
     */
    public function get(string $index, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return $this->atl->config->getTemp($id, $type, $index);
    }

    /**
     * @method set
     * @param string $index
     * @param mixed $contents
     * @param int $id = null
     * @param string $type = null
     * @return mixed
     */
    public function set(string $index, $contents, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $temp = $this->atl->config->getTemp($id, $type);
        if($temp === null)
            $temp = array();
        $temp[$index] = $contents;
        return $this->atl->config->setTemps($id, $type, $temp);
    }

    /**
     * @method has
     * @param string $index
     * @param int $id = null
     * @param string $type = null
     * @return bool
     */
    public function has(string $index, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $temp = $this->atl->config->getTemp($id, $type);
        return $temp === null ? false : isset($temp[$index]);
    }

    /**
     * @method del
     * @param string $index
     * @param int $id = null
     * @param string $type = null
     * @return mixed
     */
    public function del(string $index, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $temp = $this->atl->config->getTemp($id, $type);
        if($temp === null || !isset($temp[$index]))
            return false;
        unset($temp[$index]);
        return $this->atl->config->setTemps($id, $type, $temp);
    }

    /**
     * @method push
     * @param mixed $contents
     * @param int $id = null
     * @param string $type = null
     * @return mixed
     */
    public function push($contents, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $temp = $this->atl->config->getTemp($id, $type);
        if($temp === null)
            $temp = array();
        array_push($temp, $contents);
        return $this->atl->config->setTemps($id, $type, $temp);
    }

    /**
     * @method pop
     * @param int $id = null
     * @param string $type = null
     * @return mixed
     */
    public function pop(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $temp = $this->atl->config->getTemp($id, $type);
        if($temp === null)
            $temp = array();
        $pop = array_pop($temp);
        $this->atl->config->setTemps($id, $type, $temp);
        return $pop;
    }

    /**
     * @method getdel
     * @param string $index
     * @param int $id = null
     * @param string $type = null
     * @return mixed
     */
    public function getdel(string $index, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $temp = $this->atl->config->getTemp($id, $type);
        if($temp === null)
            $temp = array();
        if(!isset($temp[$index]))
            return false;
        $get = $temp[$index];
        unset($temp[$index]);
        $this->atl->config->setTemps($id, $type, $temp);
        return $get;
    }
}
?>