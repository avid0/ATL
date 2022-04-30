<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;
use \ATL\Logger;

class Referral {
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
     * @method setReferral
     * @param int $referral
     * @param int $id = answering
     * @param string $type = answering
     */
    public function setReferral(int $referral, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $referrals = (int)$this->atl->config->getPlug($referral, $type, 'referral');
        $this->atl->config->setPlug($referral, $type, 'referrals', $referrals + 1);
        return $this->atl->config->setReferral($id, $type, $referral);
    }
    
    /**
     * @method getReferral
     * @param int $id = answering
     * @param string $type = answering
     * @return int
     */
    public function getReferral(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return $this->atl->config->getReferral($id, $type);
    }

    /**
     * @method countReferral
     * @param int $id = answering
     * @param string $type = answering
     * @return int
     */
    public function countReferral(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return (int)$this->atl->config->getPlug($id, $type, 'referrals');
    }

    /**
     * @method mapReferral
     * @param callable $callable
     * @param int $id = answering
     * @param string $type = answering
     */
    public function mapReferral($callable, int $id = null, string $type = null){
        if(!is_callable($callable)){
            Logger::log("\ATL\Plugin\Referral::mapReferral(): Expects parameter 1 to be callable");
            return false;
        }
        $this->setDefault($id, $type);
        return $this->atl->config->mapReferral($id, $type, $callable);
    }
    
    /**
     * @method setGrandReferral
     * @param int $referral
     * @param int $id = answering
     * @param string $type = answering
     */
    public function setGrandReferral(int $referral, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $referrals = (int)$this->atl->config->getPlug($id, $type, 'grandreferrals');
        $this->atl->config->setPlug($id, $type, 'grandreferrals', $referrals + 1);
        return $this->atl->config->setGrandReferral($id, $type, $referral);
    }
    
    /**
     * @method getGrandReferral
     * @param int $id = answering
     * @param string $type = answering
     * @return int
     */
    public function getGrandReferral(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return $this->atl->config->getGrandReferral($id, $type);
    }
    
    /**
     * @method countGrandReferral
     * @param int $id = answering
     * @param string $type = answering
     * @return int
     */
    public function countGrandReferral(int $id = null, string $type = null){
        $this->setDefault($id, $type);
        return (int)$this->atl->config->getPlug($id, $type, 'grandreferrals');
    }

    /**
     * @method mapGrandReferral
     * @param callable $callable
     * @param int $id = answering
     * @param string $type = answering
     */
    public function mapGrandReferral($callable, int $id = null, string $type = null){
        if(!is_callable($callable)){
            Logger::log("\ATL\Plugin\Referral::mapGrandReferral(): Expects parameter 1 to be callable");
            return false;
        }
        $this->setDefault($id, $type);
        return $this->atl->config->mapGrandReferral($id, $type, $callable);
    }
}
?>