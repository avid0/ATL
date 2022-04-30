<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;
use \ATL\Logger;

class Broadcast {
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
     * @method run
     * @param string $type
     * @param callable $callable Will run in format
     * @param array $params += unshift [\ATL $atl, array $user] ["var" => $content]
     * @return bool
     */
    public function run(string $type, $callable, array $params = array()){
        if(!is_callable($callable)){
            Logger::log("\ATL\Plugin\Broadcast::run(): Expects parameter 2 to be callable");
            return false;
        }
        $atl = $this->atl;
        return $this->atl->config->map($type, function($user)use($atl, $callable, $params){
            $callable($atl, $user, ...$params);
        });
    }

    /**
     * @method proc
     * @param string $type
     * @param callable $callable
     * @param array $params += unshift [\ATL $atl, array $user] ["var" => $content]
     * @param int $method = AUTO
     * @param string $cwd = null
     * @return object or bool
     */
    public function proc(string $type, $callable, array $params = array(), int $method = 0, string $cwd = null){
        if(!is_callable($callable)){
            Logger::log("\ATL\Plugin\Broadcast::proc(): Expects parameter 2 to be callable");
            return false;
        }
        $atl = $this->atl;
        return $this->atl->proc(function()use($atl, $type, $callable, $params){
            $atl->broadcast->run($type, $callable, $params);
        }, array(), $method, $cwd);
    }

    /**
     * @method request
     * @param string $type
     * @param string $method
     * @param array $datas = []
     * @param int $request = 0
     * @return bool
     */
    public function request(string $type, string $method, array $datas = array(), int $request = 0){
        return $this->run($type, function($atl, $user)use($method, $datas, $request){
            $datas = $atl->setAnswers($datas, $user['id']);
            $atl->request($method, $datas, $request);
        });
    }
    
    /**
     * @method multirequest
     * @param string $type
     * @param array $datas
     * @param int $request = 0
     * @param int $limit = 30
     * @return bool
     */
    public function multirequest(string $type, array $datas, int $request = 0, int $limit = 30){
        return $this->run($type, function($atl, $user)use($datas, $request, $limit){
            foreach($datas as &$data)
                $data = $atl->setAnswers($data, $user['id']);
            $atl->multirequest($datas, $request, $limit);
        });
    }
}
?>