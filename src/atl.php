<?php
/**
 * @author Avid [tg:@Av_id]
 * @version 1.0.0
 */
// namespace ATL;
use \ATL\Logger;

class ATL {
    use \ATL\Plugin\Request;
    use \ATL\Plugin\Answer;
    use \ATL\Plugin\Reply;
    use \ATL\Plugin\Make;
    use \ATL\Plugin\Has;

    /**
     * Robot API plugin
     * @var \ATL\API $api
     * @var int $botid
     * @var \ATL\Defaults $default
     */
    public $api;
    public $botid;
    public $default;

    /**
     * Constructor
     * 
     * @method __construct
     */
    public function __construct(){
        $this->default = new \ATL\Defaults;
        $this->owner = new \ATL\Owner;
        $this->admin = new \ATL\Admin;
        $this->keyboard = new \ATL\Keyboard($this);
    }

    /**
     * Open API plugin
     * 
     * @method openAPI
     * @param string $token
     * @param int $request
     * @return object or false
     */
    public function openAPI(string $token, int $request = 2){
        if(!$this->api){
            $this->api = new \ATL\API($token, $request);
        }else{
            $this->api->connect($token, $request);
        }
        $id = explode(':', $token, 2);
        $this->botid = (int)$id[0];
        return $this->api;
    }

    /**
     * Database
     * @var \ATL\DB $db
     * @var object $config
     */
    public $db;
    public $config;
    public $dbconfig;

    /**
     * Open Database plugin
     * 
     * @method openDB
     * @param int $type
     * @param mixed ...$args
     * @return object
     */
    public function openDB(int $type, ...$args){
        $this->dbconfig = array($type, $args);
        $this->db = new \ATL\DB($type, ...$args);
        $this->config = $this->db->config;
        $this->db = $this->db->db;
        return $this->db;
    }

    /**
     * Open proc
     * 
     * @method proc
     * @param callable $callable
     * @param array $params += unshift [self] ["var" => $content]
     * @param int $method = AUTO
     * @param string $cwd = null
     * @return object or bool
     */
    public function proc($callable, array $params = array(), int $method = 0, string $cwd = null){
        array_unshift($params, $this);
        return \ATL\Proc::run($callable, $params, $method, $cwd);
    }

    /**
     * Register shutdown callable
     * 
     * @method shutdown
     * @param callable $callable
     * @param array $params += unshift [self] ["var" => $content]
     * @return int $id
     */
    public function shutdown($callable, array $params = array()){
        if(!is_callable($callable)){
            Logger::log("\ATL::shutdown(): Expects parameter 1 to be callable");
            return false;
        }
        array_unshift($params, $this);
        return \ATL\Proc\Shutdown::register($callable, $params);
    }

    /**
     * Update variables
     * @var object $update
     * @var object $message
     * @var object $chat
     * @var object $user
     * @var \ATL\Answer $answer
     * @var \ATL\Owner $owner
     * @var \ATL\Admin $admin
     * @var \ATL\Keyboard $keyboard
     */
    public $update, $message, $chat, $user;
    public $answer, $owner, $admin, $keyboard;
    
    /**
     * Load update object
     * 
     * @method update
     * @param object $update = php://input
     * @return bool
     */
    public function update(object $update = null){
        if(!$update){
            $update = \ATL\API\Webhook::update();
            if(!$update || !isset($update->update_id)){
                Logger::log("\ATL::update(): There is no Webhook request update object for reading");
                return false;
            }
        }
        $update = \ATL\Parser::parseUpdate($update);
        $this->update = $update;
        $this->updateFindIds();
        return true;
    }

    /**
     * @internal
     * @method updateFindIds
     */
    private function updateFindIds(){
        $this->answer = new \ATL\Answer;
        $this->message = $this->chat = $this->user = null;
        $this->answer->pid = $this->update->update_id;
        switch($this->update->type){
            case 'message':
                $this->message = $this->update->message = \ATL\Parser::parseMessage($this->update->message);
                $this->answer->mid = $this->message->message_id;
            break;
            case 'edited_message':
                $this->message = $this->update->edited_message = \ATL\Parser::parseMessage($this->update->edited_message);
                $this->answer->mid = $this->message->message_id;
            break;
            case 'channel_post':
                $this->message = $this->update->channel_post = \ATL\Parser::parseMessage($this->update->channel_post);
                $this->answer->mid = $this->message->message_id;
            break;
            case 'edited_channel_post':
                $this->message = $this->update->edited_channel_post = \ATL\Parser::parseMessage($this->update->edited_channel_post);
                $this->answer->mid = $this->message->message_id;
            break;
            case 'callback_query':
                if(isset($this->update->callback_query->message)){
                    $this->message = $this->update->callback_query->message = \ATL\Parser::parseMessage($this->update->callback_query->message);
                    $this->answer->mid = $this->message->message_id;
                }
                $this->user = $this->update->callback_query->from;
                if(isset($this->update->callback_query->inline_message_id))
                    $this->answer->iid = $this->update->callback_query->inline_message_id;
            break;
            case 'inline_query':
                $this->user = $this->update->inline_query->from;
            break;
            case 'chosen_inline_result':
                $this->user = $this->update->chosen_inline_result->from;
                if(isset($this->update->chosen_inline_result->inline_message_id))
                    $this->answer->iid = $this->update->chosen_inline_result->inline_message_id;
            break;
            case 'shipping_query':
                $this->user = $this->update->shipping_query->from;
            break;
            case 'pre_checkout_query':
                $this->user = $this->update->pre_checkout_query->from;
            break;
            case 'poll':
                $this->answer->lid = $this->update->poll->id;
            break;
            case 'poll_answer':
                $this->answer->lid = $this->update->poll_answer->poll_id;
            break;
            case 'my_chat_member':
                $this->chat = $this->update->my_chat_member->chat;
                $this->user = $this->update->my_chat_member->user;
            break;
        }
        if($this->message)
            $this->chat = $this->message->chat;
        elseif($this->user)
            $this->chat = $this->user;
        if(!$this->user && $this->message)
            $this->user = $this->message->from;
        if($this->chat)
            $this->answer->cid = $this->chat->id;
        if($this->user)
            $this->answer->uid = $this->user->id;
        $this->file = \ATL\Parser::parseMessageFile($this->message);
        if($this->file)
            $this->answer->did = $this->file->file_id;
        if($this->message && in_array($this->message->type, array('message', 'post_message')))
            $this->answer->date = $this->message->date;
    }

    /**
     * @method parseDatas
     * @param array $datas
     * @param string $method = null
     * @return array
     */
    public function parseDatas(array $datas, string $method = null){
        if($this->lang){
            $datas = \ATL\Parser::selectLang($datas, $this->lang);
            $datas = \ATL\Parser::datasInsertKeyboardLang($datas, $this->keyboard, $this->lang);
        }else{
            $datas = \ATL\Parser::datasInsertKeyboard($datas, $this->keyboard);
        }
        if($this->inlinelist && isset($datas['reply_markup'])){
            $reply_markup = $datas['reply_markup'];
            if(is_string($reply_markup))
                $reply_markup = json_decode($reply_markup, true);
            if($reply_markup){
                $reply_markup = $this->inlinelist->parse($reply_markup);
                $datas['reply_markup'] = $reply_markup;
            }
        }
        $datas = \ATL\Parser::datasJsonify($datas, $this->default);
        if($method)
            $datas = \ATL\Parser::datasFileProcessing($datas, $method);
        if($this->update)
            $datas = \ATL\Parser::datasUpdateProcessing($datas, $this->answer);
        return $datas;
    }

    /**
     * Webhook verify
     * 
     * @method webhookVerify
     * @return bool
     */
    public function webhookVerify(){
        return \ATL\API\Webhook::verify();
    }

    /**
     * Webhook close connection
     * 
     * @method closeConnection
     * @param string $message = ''
     * @return bool
     */
    public function closeConnection(string $message = ''){
        return \ATL\API\Webhook::close($message);
    }

    /**
     * Webhook has closed
     * 
     * @method closedConnection
     * @return bool
     */
    public function closedConnection(){
        return \ATL\API\Webhook::closed();
    }

    /**
     * @var string $botusername
     */
    public $botusername;

    /**
     * @method setBotUsername
     * @param string $username
     */
    public function setBotUsername(string $username){
        if(isset($username[0]) && $username[0] == '@')
            $username = substr($username, 1);
        $this->botusername = $username;
    }

    /**
     * @var \ATL\Plugin\Broadcast $broadcast
     * @var \ATL\Plugin\Step $step
     * @var \ATL\Plugin\Blocking $blocking
     * @var \ATL\Plugin\ForceJoin $forcejoin
     * @var \ATL\Plugin\Tools $tools
     * @var \ATL\Plugin\Referral $referral
     * @var \ATL\Plugin\AntiSpace $antispam
     * @var \ATL\Plugin\Temp $temp
     * @var \ATL\Plugin\InlineList $inlinelist
     * @var \ATL\Plugin\WebhookRouter $webhook_router
     * @var \ATL\Plugin\ReadUpdates $read_updates
     * @var \ATL\Plugin\Lang $lang
     * @var \ATL\Plugin\General $general
     * @var \ATL\Plugin\Interval $interval
     * @var \ATL\Plugin\ParseMode $parsemode
     */
    public $broadcast, $step, $blocking, $forcejoin, $tools;
    public $referral, $antispam, $temp, $inlinelist, $webhook_router;
    public $read_updates, $lang, $general, $interval, $parsemode;

    /**
     * @method openPlugs
     */
    public function openPlugs(){
        ++Logger::$internal;
        $this->config->config();
        $id = $this->whereAnswers();
        --Logger::$internal;
        if($id){
            if($this->chat && isset($this->chat->type))
                $type = $this->chat->type;
            else
                $type = 'private';
            if(!$this->config->has($id, $type))
                $this->config->insert($id, $type);
        }
        if(!$this->broadcast)
            $this->broadcast = new \ATL\Plugin\Broadcast($this);
        if(!$this->step)
            $this->step = new \ATL\Plugin\Step($this);
        if(!$this->blocking)
            $this->blocking = new \ATL\Plugin\Blocking($this);
        if(!$this->forcejoin)
            $this->forcejoin = new \ATL\Plugin\ForceJoin($this);
        if(!$this->tools)
            $this->tools = new \ATL\Plugin\Tools;
        if(!$this->referral)
            $this->referral = new \ATL\Plugin\Referral($this);
        if(!$this->antispam)
            $this->antispam = new \ATL\Plugin\AntiSpam($this);
        if(!$this->temp)
            $this->temp = new \ATL\Plugin\Temp($this);
        if(!$this->inlinelist)
            $this->inlinelist = new \ATL\Plugin\InlineList($this);
        if(!$this->webhook_router)
            $this->webhook_router = new \ATL\Plugin\WebhookRouter($this);
        if(!$this->read_updates)
            $this->read_updates = new \ATL\Plugin\ReadUpdates($this);
        if(!$this->lang)
            $this->lang = new \ATL\Plugin\Lang($this);
        if(!$this->general)
            $this->general = new \ATL\Plugin\General($this);
        if(!$this->interval)
            $this->interval = new \ATL\Plugin\Interval($this);
        if(!$this->parsemode)
            $this->parsemode = new \ATL\Plugin\ParseMode($this);
    }

    // Serialization methods
    public function __serialize(){
        $db = $this->db;
        $config = $this->config;
        $this->db = null;
        $this->config = null;
        $arr = (array)$this;
        $this->db = $db;
        $this->config = $config;
        return $arr;
    }

    public function __unserialize(array $data){
        foreach($data as $key => $value)
            $this->$key = $value;
        if($this->dbconfig)
            $this->openDB($this->dbconfig[0], ...$this->dbconfig[1]);
    }
}
?>