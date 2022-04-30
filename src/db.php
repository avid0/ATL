<?php
/**
 * @author Avid [tg:@Av_id]
 * 
 * ATL Database
 */
namespace ATL;
use \ATL\ATLException;

class DB {
    /**
     * Database types
     * @var int MEDOO = 0
     * @var int FILE = 1
     */
    const MEDOO = 0;
    const FILE = 1;
    
    /**
     * @var object $db
     */
    public $db;

    /**
     * @var object $config
     */
    public $config;

    /**
     * Constructor
     * 
     * @method __construct
     * @param int $type
     * @param mixed ...$args
     * @throws \ATL\ATLException
     */
    public function __construct(int $type, ...$args){
        switch($type){
            case self::MEDOO:
                $db = new \Medoo\Medoo(...$args);
                $config = new \ATL\DB\Config\Medoo($db);
            break;
            case self::FILE:
                $db = new \ATL\DB\FileController(...$args);
                $config = new \ATL\DB\Config\FileController($db);
            break;
            default:
                throw new ATLException("Invalid db type $type");
        }
        $this->db = $db;
        $this->config = $config;
    }
}

?>