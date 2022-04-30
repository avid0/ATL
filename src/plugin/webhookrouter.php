<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;

class WebhookRouter {
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
     * Get currect web router with https protocol
     * 
     * @method where
     * @return string or false
     */
    public function where(){
        $uri = getenv("REQUEST_URI");
        if(!$uri)
            return false;
        $host = getenv("SERVER_NAME");
        $port = getenv("SERVER_PORT");
        if($port == 443 || $port == 80)
            $url = "https://$host{$uri}";
        else
            $url = "https://$host:$port{$uri}";
        return $url;
    }

    /**
     * setWebhook on currect with https protocol
     * 
     * @method here
     * @return bool
     */
    public function here(){
        $where = $this->where();
        if(!$where)
            return false;
        return $this->atl->setWebhook($where);
    }
}