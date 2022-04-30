<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class Lang {
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
     * Set user lang
     * 
     * @method set
     * @param string $lang
     * @param int $id = answering
     * @param string $type = answering
     * @return bool
     */
    public function set(string $lang, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $this->cachelang = null;
        return $this->atl->config->setPlug($id, $type, 'lang', $lang);
    }

    /**
     * @var array $cachelang
     */
    public $cachelang;

    /**
     * Get user lang
     * 
     * @method get
     * @param int $id = answering
     * @param string $type = answering
     * @return string $lang or null
     */
    public function get(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        if($this->cachelang && $this->cachelang[0] == $id && $this->cachelang[1] == $type)
            return $this->cachelang[2];
        $lang = $this->atl->config->getPlug($id, $type, 'lang');
        $this->cachelang = [$id, $type, $lang];
        return $lang;
    }

    /**
     * @method has
     * @param string $lang
     * @param int $id = answering
     * @param string $type = answering
     * @return bool
     */
    public function has(string $lang, int $id = null, string $type = null){
        return $this->get($id, $type) == $lang;
    }

    /**
     * @method select
     * @param string|array $input
     * @param int $id = answering
     * @param string $type = answering
     * @return string|array or false
     */
    public function select($input, int $id = null, string $type = null){
        if(is_string($input))
            return $input;
        if($input === [])
            return $input;
        if(!is_array($input))
            return $input;
        $lang = $this->get($id, $type);
        if(!$lang || !isset($input[$lang])){
            if(isset($input['default']))
                return $input['default'];
            if(isset($input['en']))
                return $input['en'];
            return $input;
        }
        return $input[$lang];
    }

    /**
     * @method getText
     * @param string|array $text
     * @param int $id = answering
     * @param string $type = answering
     * @return string or false
     */
    public function getText($text, int $id = null, string $type = null){
        $text = $this->select($text, $id, $type);
        return $text;
    }
}
?>