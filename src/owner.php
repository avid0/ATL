<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL;

class Owner {
    /**
     * @var int $id
     */
    public $id;

    /**
     * @method set
     * @param int $id
     * @return $id
     */
    public function set(int $id){
        $this->id = $id;
    }

    /**
     * @method get
     * @return $id
     */
    public function get(){
        return $this->id;
    }

    /**
     * @method has
     * @param int $id
     * @return bool
     */
    public function has(int $id){
        return $this->id == $id;
    }
}
?>