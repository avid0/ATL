<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * FileController ATL Config
 */
namespace ATL\DB\Config;

class FileController {
    /**
     * Database object
     * @var \ATL\DB\FileController $db
     */
    public $db;

    /**
     * Constructor
     * 
     * @method __construct
     * @param \ATL\DB\FileController $db
     */
    public function __construct(\ATL\DB\FileController $db){
        $this->db = $db;
    }

    /**
     * @var bool $configed = false
     */
    public $configed = false;

    /**
     * Configure
     * 
     * @method config
     * @return bool
     */
    public function config(){
        if($this->cofiged)
            return true;
        if($this->db->exists("_ATL_"))
            return false;
        $this->configed = true;
        return $this->db->mkdir("_ATL_")
            && $this->db->mkdir("_ATL_/_US_")
            && $this->db->mkdir("_ATL_/_REF_")
            && $this->db->mkdir("_ATL_/_GREANDREF_");
    }

    /**
     * Insert
     * 
     * @method insert
     * @param int $id
     * @param string $type
     * @return bool
     */
    public function insert(int $id, string $type){
        $time = microtime(true);
        $atl = $this->db->open("_ATL_");
        $atl->nmkdir($type);
        $atl = $atl->open($type);
        if($atl->exists($id))
            return false;
        $atl->nmkdir($id);
        $atl = $atl->open($id);
        $atl->put("id", $id);
        $atl->put("type", $type);
        $atl->put("creation", $time);
        $atl->put("modification", $time);
        $atl->put("coin", 0);
        $atl->put("info", "{}");
        $atl->put("temp", "{}");
        $atl->put("plugs", "{}");
        return true;
    }

    /**
     * Has exists
     * 
     * @method has
     * @param int $id
     * @param string $type
     * @return bool
     */
    public function has(int $id, string $type){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        return $atl->exists($id);
    }

    /**
     * Delete
     * 
     * @method del
     * @param int $id
     * @param string $type
     * @return bool
     */
    public function del(int $id, string $type){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        return $atl->del($id);
    }

    /**
     * Modify
     * 
     * @method modify
     * @param int $id
     * @param string $type
     * @param object $info
     * @return bool
     */
    public function modify(int $id, string $type, object $info){
        $time = microtime(true);
        if(isset($info->username)){
            $username = $info->username;
            unset($info->username);
        }else
            $username = '';
        if(isset($info->title)){
            $title = $info->title;
            $info->title = true;
        }else{
            $title = $info->first_name;
            if(isset($info->last_name))
                $title .= ' '.$info->last_name;
            unset($info->first_name);
        }
        $atl = $this->db->open("_ATL_");
        if($username){
            $us = $atl->open("_US_");
            $us->put($username, $id);
        }
        $atl = $atl->open($type);
        $atl = $atl->open($id);
        if(!$atl)
            return false;
        $atl->put("modification", $time);
        $atl->put("username", $username);
        $atl->put("title", $title);
        $atl->put("info", json_encode($info));
        return true;
    }

    /**
     * getPlug
     * 
     * @method getPlug
     * @param int $id
     * @param string $type
     * @param string $index = all
     * @return mixed
     */
    public function getPlug(int $id, string $type, string $index = null){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        $atl = $atl->open($id);
        $plugs = json_decode($atl->get("plugs"), true);
        if(!$plugs)
            return null;
        if($index === null)
            return $plugs;
        if(!isset($plugs[$index]))
            return null;
        return $plugs[$index];
    }

    /**
     * setPlug
     * 
     * @method setPlug
     * @param int $id
     * @param string $type
     * @param string $index
     * @param mixed $contents
     * @return bool
     */
    public function setPlug(int $id, string $type, string $index, $contents){
        $plugs = $this->getPlug($id, $type, $index);
        if($plugs === null)
            $plugs = array();
        $plugs[$index] = $contents;
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        $atl = $atl->open($id);
        $atl->put("plugs", json_encode($plugs));
        return true;
    }
    
    /**
     * getTemp
     * 
     * @method getTemp
     * @param int $id
     * @param string $type
     * @param string $index = all
     * @return mixed
     */
    public function getTemp(int $id, string $type, string $index = null){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        $atl = $atl->open($id);
        $temp = json_decode($atl->get("temp"), true);
        if(!$temp)
            return null;
        if($index === null)
            return $temp;
        if(!isset($temp[$index]))
            return null;
        return $temp[$index];
    }
    
    /**
     * setTemps
     * 
     * @method setTemps
     * @param int $id
     * @param string $type
     * @param array $temp
     * @return int
     */
    public function setTemps(int $id, string $type, array $temp){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        $atl = $atl->open($id);
        return $atl->put("temp", json_encode($temp));
    }

    /**
     * getCreation
     * 
     * @method getCreation
     * @param int $id
     * @param string $type
     * @return float
     */
    public function getCreation(int $id, string $type){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        $atl = $atl->open($id);
        return (float)$atl->get("creation");
    }

    /**
     * getModification
     * 
     * @method getModification
     * @param int $id
     * @param string $type
     * @return float
     */
    public function getModification(int $id, string $type){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        $atl = $atl->open($id);
        return (float)$atl->get("modification");
    }

    /**
     * getInfo
     * 
     * @method getInfo
     * @param int $id
     * @param string $type
     * @return array
     */
    public function getInfo(int $id, string $type){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        $atl = $atl->open($id);
        $username = $atl->get("username");
        $title = $atl->get("title");
        $info = json_decode($atl->get("info"));
        if($username)
            $info->username = $username;
        if(isset($info->title))
            $info->title = $title;
        else
            $info->first_name = $title;
        return $info;
    }

    /**
     * @method byUsername
     * @param string $username
     * @return int
     */
    public function byUsername(string $username){
        $atl = $this->db->open("_ATL_");
        $us = $atl->open("us");
        if(!$us->exists($username))
            return false;
        return (int)$us->get($username);
    }

    /**
     * @method map
     * @param string $type
     * @param callable $callable
     * @return bool
     */
    public function map(string $type, $callable){
        if(!is_callable($callable)){
            Logger::log("\ATL\DB\Config\FileController::map(): Expects parameter 2 to be callable");
            return false;
        }
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        $atl->map($callable);
        return true;
    }
    
    /**
     * @method count
     * @param string $type
     * @return int
     */
    public function count(string $type){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        return $atl->count();
    }

    /**
     * @method setReferral
     * @param int $id
     * @param string $type
     * @param int $referral
     * @return bool
     */
    public function setReferral(int $id, string $type, int $referral){
        $atl = $this->db->open("_ATL_");
        $ref = $atl->open("_REF_");
        $atl = $atl->open($type);
        if($atl->exists('referral') && $atl->get('referral') == $referral)
            return false;
        $atl->put('referral', $referral);
        $ref->nmkdir($type);
        $ref = $ref->open($type);
        if(!$ref->exists($referral))
            return (bool)$ref->put($referral, $id);
        return (bool)$ref->append($referral, "\n$id");
    }

    /**
     * @method getReferral
     * @param int $id
     * @param string $type
     * @return int or false
     */
    public function getReferral(int $id, string $type){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        if(!$atl->exists('referral'))
            return false;
        return (int)$atl->get('referral');
    }

    /**
     * @method mapReferral
     * @param int $id
     * @param string $type
     * @param callable $callable
     * @return bool
     */
    public function mapReferral(int $id, string $type, $callable){
        if(!is_callable($callable)){
            Logger::log("\ATL\DB\Config\FileController::mapReferral(): Expects parameter 3 to be callable");
            return false;
        }
        $atl = $this->db->open("_ATL_");
        $ref = $atl->open("_REF_");
        $atl = $atl->open($type);
        $ref = $ref->open($type);
        if(!$ref->exists($id))
            return false;
        return $ref->mapfile($id, function($referral)use($callable, $atl, $type){
            $referral = array(
                "id" => (int)$referral,
                "type" => $type,
                "creation" => (float)$atl->get('creation'),
                "modification" => (float)$atl->get('modification'),
                "info" => $atl->get("info"),
                "plugs" => $atl->get("plugs"),
                "referral" => (int)$atl->get("referral"),
                "grandreferral" => $atl->get("grandreferral")
            );
            $callable($referral);
        });
    }
    

    /**
     * @method setGrandReferral
     * @param int $id
     * @param string $type
     * @param int $referral
     * @return bool
     */
    public function setGrandReferral(int $id, string $type, int $referral){
        $atl = $this->db->open("_ATL_");
        $ref = $atl->open("_GRANDREF_");
        $atl = $atl->open($type);
        if($atl->exists('grandreferral') && $atl->get('grandreferral') == $referral)
            return false;
        $atl->put('grandreferral', $referral);
        $ref->nmkdir($type);
        $ref = $ref->open($type);
        if(!$ref->exists($referral))
            return (bool)$ref->put($referral, $id);
        return (bool)$ref->append($referral, "\n$id");
    }

    /**
     * @method getGrandReferral
     * @param int $id
     * @param string $type
     * @return int or false
     */
    public function getGrandReferral(int $id, string $type){
        $atl = $this->db->open("_ATL_");
        $atl = $atl->open($type);
        if(!$atl->exists('grandreferral'))
            return false;
        return (int)$atl->get('grandreferral');
    }

    /**
     * @method mapGrandReferral
     * @param int $id
     * @param string $type
     * @param callable $callable
     * @return bool
     */
    public function mapGrandReferral(int $id, string $type, $callable){
        if(!is_callable($callable)){
            Logger::log("\ATL\DB\Config\FileController::mapGrandReferral(): Expects parameter 3 to be callable");
            return false;
        }
        $ref = $this->db->open("_ATL_");
        $ref = $ref->open("_GRANDREF_");
        $ref = $ref->open($type);
        if(!$ref->exists($id))
            return false;
        return $ref->mapfile($id, function($referral)use($callable, $atl, $type){
            $referral = array(
                "id" => (int)$referral,
                "type" => $type,
                "creation" => (float)$atl->get('creation'),
                "modification" => (float)$atl->get('modification'),
                "info" => $atl->get("info"),
                "plugs" => $atl->get("plugs"),
                "referral" => (int)$atl->get("referral"),
                "grandreferral" => $atl->get("grandreferral")
            );
            $callable($referral);
        });
    }
}
?>