<?php
/**
 * @author Avid [tg:@Av_id]
 */
namespace ATL\Plugin;
use \ATL\Logger;

class InlineList {
    /**
     * @var \ATL $atl
     * @var int $cache_time = 7 days
     */
    public $atl;
    public $cache_time = 86400*7;

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
     * @method save
     * @param int $crc
     * @param array $keyboard
     * @param int $id = null
     * @param string $type = null
     * @return bool
     */
    public function save(int $crc, array $keyboard, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $lists = $this->atl->config->getPlug($id, $type, "inlinelists");
        if(!$lists)
            $lists = array();
        $now = microtime(true);
        if($this->cache_time != 0){
            foreach($lists as $k => $list)
                if($now - $list['time'] >= $this->cache_time)
                    unset($lists[$k]);
        }
        $keyboard['time'] = $now;
        $lists[$crc] = $keyboard;
        return $this->atl->config->setPlug($id, $type, "inlinelists", $lists);
    }

    /**
     * @method read
     * @param int $crc
     * @param int $id = null
     * @param string $type = null
     * @return array or false
     */
    public function read(int $crc, int $id = null, string $type = null){
        $this->setDefault($id, $type);
        $lists = $this->atl->config->getPlug($id, $type, "inlinelists");
        if(!$lists)
            $lists = array();
        if(!isset($lists[$crc]))
            return false;
        return $lists[$crc];
    }

    /**
     * @method parse
     * @param array $keyboard Reply markup
     * @return object Reply markup
     */
    public function parse(array $keyboard){
        if(!isset($keyboard['inline_list']))
            return $keyboard;
        if(!isset($keyboard['list_top']) || !is_array($keyboard['list_top']))
            $keyboard['list_top'] = array();
        if(!isset($keyboard['list_bottom']) || !is_array($keyboard['list_bottom']))
            $keyboard['list_bottom'] = array();
        if(!isset($keyboard['list_between']) || !is_array($keyboard['list_between']))
            $keyboard['list_between'] = array();
        if(!isset($keyboard['list_next']))
            $keyboard['list_next'] = ">";
        if(!isset($keyboard['list_back']))
            $keyboard['list_back'] = "<";
        if(!isset($keyboard['list_center']))
            $keyboard['list_center'] = false;
        if(!isset($keyboard['list_rows']) || !is_numeric($keyboard['list_rows']))
            $keyboard['list_rows'] = 10;
        if(!isset($keyboard['list_jump']))
            $keyboard['list_jump'] = false;
        elseif($keyboard['list_jump']){
            if(!isset($keyboard['list_jump_next']))
                $keyboard['list_jump_next'] = ">>";
            if(!isset($keyboard['list_jump_back']))
                $keyboard['list_jump_back'] = "<<";
        }
        $json = json_encode($keyboard);
        $crc = crc32($json);
        $keyboard['inline_list'] = array_chunk($keyboard['inline_list'], $keyboard['list_rows']);
        $this->save($crc, $keyboard);
        return $this->loadPage($crc, $keyboard);
    }

    /**
     * @method loadPage
     * @param int $crc
     * @param array $keyboard
     * @param int $page = 0
     * @return $keyboard inline keyboard reply markup
     */
    public function loadPage(int $crc, array $keyboard, int $page = 0){
        $max = count($keyboard['inline_list'])-1;
        if($page < 0)$page = 0;
        if($page > $max)$page = $max;
        $scrollrow = array();
        $jump = $keyboard['list_jump'];
        if($jump && $page - $jump >= 0){
            $keyboard['list_jump_back'] = str_replace(["%n", "%%"], [$page - $jump, "%"], $keyboard['list_jump_back']);
            $scrollrow[] = ["text" => $keyboard['list_jump_back'], "callback_data" => "inlinelist_{$crc}_".($page - $jump)];
        }
        if($page != 0){
            $keyboard['list_back'] = str_replace(["%n", "%%"], [$page - 1, "%"], $keyboard['list_back']);
            $scrollrow[] = ["text" => $keyboard['list_back'], "callback_data" => "inlinelist_{$crc}_".($page - 1)];
        }
        if($keyboard['list_center']){
            $keyboard['list_center'] = str_replace(["%n", "%%"], [$page, "%"], $keyboard['list_center']);
            $scrollrow[] = ["text" => $keyboard['list_center'], "callback_data" => ""];
        }
        if($page != $max){
            $keyboard['list_next'] = str_replace(["%n", "%%"], [$page + 1, "%"], $keyboard['list_next']);
            $scrollrow[] = ["text" => $keyboard['list_next'], "callback_data" => "inlinelist_{$crc}_".($page + 1)];
        }
        if($jump && $page + $jump <= $max){
            $keyboard['list_jump_next'] = str_replace(["%n", "%%"], [$page + $jump, "%"], $keyboard['list_jump_next']);
            $scrollrow[] = ["text" => $keyboard['list_jump_next'], "callback_data" => "inlinelist_{$crc}_".($page + $jump)];
        }
        $page = array_merge($keyboard['list_top'], $keyboard['inline_list'][$page], $keyboard['list_between'], [[$scrollrow]], $keyboard['list_bottom']);
        return array(
            "inline_keyboard" => $page
        );
    }

    /**
     * @method loadData
     * @param int $message
     * @param string $data
     * @param int $id = null
     * @param string $type = null
     * @return object or fales
     */
    public function loadData(int $message, string $data, int $id = null, string $type = null){
        $data = explode('_', $data, 3);
        if($data[0] == 'inlinelist' && isset($data[2])){
            $this->setDefault($id, $type);
            $crc = (int)$data[1];
            $page = (int)$data[2];
            $keyboard = $this->read($crc, $id, $type);
            if(!$keyboard)
                return [
                    "remove_keyboard" => true
                ];
            $keyboard = $this->loadPage($keyboard, $page);
            return $this->atl->editMessageReplyMarkup($id, $message, $keyboard);
        }
        return false;
    }

    /**
     * @method load
     * @param int $id = null
     * @param string $type = null
     * @return object or false
     */
    public function load(int $id = null, string $type = null){
        if(isset($this->update->callback_query)){
            $data = $this->update->callback_query->data;
            $message = $this->answer->mid;
            return $this->loadData($message, $data, $id, $type);
        }
    }
}