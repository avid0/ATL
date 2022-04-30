<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * Medoo ATL Config
 */
namespace ATL\DB\Config;

class Medoo {
    /**
     * Database object
     * @var \Medoo\Medoo $db
     */
    public $db;

    /**
     * Constructor
     * 
     * @method __construct
     * @param \Medoo\Medoo $db
     */
    public function __construct(\Medoo\Medoo $db){
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
        if($this->configed)
            return true;
        $this->configed = true;
        return $this->db->create("_ATL_", [
            "id" => ["BIGINT", "PRIMARY KEY", "NOT NULL"],
            "type" => ["VARCHAR(32)", "NOT NULL"],
            "creation" => ["FLOAT(6)", "NOT NULL"],
            "modification" => ["FLOAT(6)", "NOT NULL"],
            "username" => ["VARCHAR(64)"],
            "title" => ["TEXT"],
            "info" => ["TEXT"],
            "temp" => ["MEDIUMTEXT"],
            "plugs" => ["MEDIUMTEXT"],
            "reference" => ["BIGINT"],
            "grandreference" => ["BIGINT"]
        ]);
    }

    /**
     * Insert
     * 
     * @method insert
     * @param int $id
     * @param string $type
     * @return \PDOStatement
     */
    public function insert(int $id, string $type){
        $time = microtime(true);
        return $this->db->insert("_ATL_", [
            "id" => $id,
            "type" => $type,
            "creation" => $time,
            "modification" => $time,
            "info" => "{}",
            "temp" => "{}",
            "plugs" => "{}"
        ]);
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
        return $this->db->has("_ATL_", [
            "id" => $id,
            "type" => $type
        ]);
    }

    /**
     * Delete
     * 
     * @method del
     * @param int $id
     * @param string $type
     * @return \PDOStatement
     */
    public function del(int $id, string $type){
        return $this->db->delete("_ATL_", [
            "id" => $id,
            "type" => $type
        ]);
    }

    /**
     * Modify
     * 
     * @method modify
     * @param int $id
     * @param string $type
     * @param object $info
     * @return \PDOStatement
     */
    public function modify(int $id, string $type, object $info){
        $time = microtime(true);
        if(isset($info->username)){
            $username = strtolower($info->username);
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
        return $this->db->update("_ATL_", [
            "modification" => $time,
            "username" => $username,
            "title" => $title,
            "info" => json_encode($info)
        ], [
            "id" => $id,
            "type" => $type
        ]);
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
        $plugs = json_decode($this->db->get("_ATL_", "plugs", [
            "id" => $id,
            "type" => $type
        ]), true);
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
     * @return \PDOStatement
     */
    public function setPlug(int $id, string $type, string $index, $contents){
        $plugs = $this->getPlug($id, $type);
        if($plugs === null)
            $plugs = array();
        $plugs[$index] = $contents;
        return $this->db->update("_ATL_", [
            "plugs" => json_encode($plugs)
        ], [
            "id" => $id,
            "type" => $type
        ]);
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
        $temp = json_decode($this->db->get("_ATL_", "temp", [
            "id" => $id,
            "type" => $type
        ]), true);
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
     * @return \PDOStatement
     */
    public function setTemps(int $id, string $type, array $temp){
        return $this->db->update("_ATL_", [
            "temp" => json_encode($temp)
        ], [
            "id" => $id,
            "type" => $type
        ]);
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
        return (float)$this->db->get("_ATL_", "creation", [
            "id" => $id,
            "type" => $type
        ]);
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
        return (float)$this->db->get("_ATL_", "modification", [
            "id" => $id,
            "type" => $type
        ]);
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
        $info = $this->db->get("_ATL_", [
            "username",
            "title",
            "info"
        ], [
            "id" => $id,
            "type" => $type
        ]);
        $info["info"] = json_decode($info["info"]);
        if($username)
            $info["info"]->username = $info["username"];
        if(isset($info["info"]->title))
            $info["info"]->title = $info["title"];
        else
            $info["info"]->first_name = $info["title"];
        return $info["info"];
    }

    /**
     * @method byUsername
     * @param string $username
     * @return int
     */
    public function byUsername(string $username){
        return $this->db->get("_ATL_", "id", [
            "username" => strtolower($username)
        ]);
    }
    
    /**
     * @method byTitle
     * @param string $tite
     * @return int
     */
    public function byTitle(string $title){
        return $this->db->get("_ATL_", "id", [
            "title" => $title
        ]);
    }

    /**
     * @method map
     * @param string $type
     * @param callable $callable
     * @return \PDOStatement or false
     */
    public function map(string $type, $callable){
        if(!is_callable($callable)){
            Logger::log("\ATL\DB\Config\Medoo::map(): Expects parameter 2 to be callable");
            return false;
        }
        return $this->db->select("_ATL_", [
            "id", "type", "creation", "modification", "info", "plugs", "reference", "grandreference"
        ], [
            "type" => $type
        ], $callable);
    }

    /**
     * @method count
     * @param string $type
     * @return int
     */
    public function count(string $type){
        return $this->db->count("_ATL_", [
            "type" => $type
        ]);
    }
    
    /**
     * @method setReference
     * @param int $id
     * @param string $type
     * @param int $reference
     * @return \PDOStatement
     */
    public function setReference(int $id, string $type, int $reference){
        return $this->db->update("_ATL_", [
            "reference" => $reference
        ], [
            'id' => $id,
            'type' => $type
        ]);
    }

    /**
     * @method getReference
     * @param int $id
     * @param string $type
     * @return int or false
     */
    public function getReference(int $id, string $type){
        return $this->db->get("_ATL_", "reference", [
            'id' => $id,
            'type' => $type
        ]);
    }

    /**
     * @method mapReference
     * @param int $id
     * @param string $type
     * @param callable $callable
     * @return \PDOStatement or false
     */
    public function mapReference(int $id, string $type, $callable){
        if(!is_callable($callable)){
            Logger::log("\ATL\DB\Config\Medoo::mapReference(): Expects parameter 3 to be callable");
            return false;
        }
        return $this->db->select("_ATL_", [
            "id", "type", "creation", "modification", "info", "plugs", "reference", "grandreference"
        ], [
            "type" => $type,
            "reference" => $id
        ], $callable);
    }

    /**
     * @method setGrandReference
     * @param int $id
     * @param string $type
     * @param int $reference
     * @return \PDOStatement
     */
    public function setGrandReference(int $id, string $type, int $reference){
        return $this->db->update("_ATL_", [
            "grandreference" => $reference
        ], [
            'id' => $id,
            'type' => $type
        ]);
    }

    /**
     * @method getGrandReference
     * @param int $id
     * @param string $type
     * @return int or false
     */
    public function getGrandReference(int $id, string $type){
        return $this->db->get("_ATL_", "grandreference", [
            'id' => $id,
            'type' => $type
        ]);
    }

    /**
     * @method mapGrandReference
     * @param int $id
     * @param string $type
     * @param callable $callable
     * @return \PDOStatement or false
     */
    public function mapGrandReference(int $id, string $type, $callable){
        if(!is_callable($callable)){
            Logger::log("\ATL\DB\Config\Medoo::mapGrandReference(): Expects parameter 3 to be callable");
            return false;
        }
        return $this->db->select("_ATL_", [
            "id", "type", "creation", "modification", "info", "plugs", "reference", "grandreference"
        ], [
            "type" => $type,
            "grandreference" => $id
        ], $callable);
    }
}
?>