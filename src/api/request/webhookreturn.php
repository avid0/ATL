<?php
/**
 * @author Avid
 * 
 * WebhookReturn API Request Method
 */
namespace ATL\API\Request;
use \ATL\API\Webhook;

class WebhookReturn {
    /**
     * Bot API Token (NOT USED)
     * @var string $token
     */
    public $token;

    /**
     * WebhookReturn request
     * 
     * @method request
     * @param string $method
     * @param array $datas = []
     * @return bool Without api response
     */
    public function request(string $method, array $datas = []){
        $datas['method'] = $method;
        $datas = json_encode($datas);
        return Webhook::close($datas);
    }
}
?>