<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL;

class Admin {
    /**
     * @var array $ids = []
     */
    public $ids = array();

    /**
     * @method register
     * @param int ...$admins
     */
    public function register(int ...$admins){
        $this->ids = array_merge($this->ids, $admins);
    }

    /**
     * @method unregister
     * @param int ...$admins
     */
    public function unregister(int ...$admins){
        $this->ids = array_values(array_diff($this->ids, $admins));
    }

    /**
     * @method has
     * @param int $admin
     * @return bool
     */
    public function has(int $admin){
        return array_search($admin, $this->ids) !== false;
    }

    /**
     * @method get
     * @return array
     */
    public function get(){
        return $this->ids;
    }

    /**
     * @method reset
     */
    public function reset(){
        $this->ids = array();
    }
}
?>